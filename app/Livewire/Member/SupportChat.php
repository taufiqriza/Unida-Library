<?php

namespace App\Livewire\Member;

use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\StaffNotification;
use App\Models\User;
use App\Notifications\SupportReplyNotification;
use Livewire\Component;
use Livewire\WithFileUploads;

class SupportChat extends Component
{
    use WithFileUploads;

    public $isOpen = false;
    public $showTopicSelector = false;
    public $room = null;
    public $messages = [];
    public $newMessage = '';
    public $image;
    public $lastReadAt = null;
    
    public $topics = [
        'unggah' => ['icon' => 'fa-upload', 'label' => 'Unggah Mandiri'],
        'plagiasi' => ['icon' => 'fa-search', 'label' => 'Cek Plagiasi'],
        'bebas' => ['icon' => 'fa-clipboard-check', 'label' => 'Bebas Pustaka'],
        'pinjam' => ['icon' => 'fa-book', 'label' => 'Peminjaman'],
        'lainnya' => ['icon' => 'fa-question-circle', 'label' => 'Lainnya'],
    ];

    protected function member()
    {
        return auth('member')->user();
    }

    public function mount()
    {
        if (auth('member')->check()) {
            $this->loadRoom();
        }
    }

    public function openChat()
    {
        if (!auth('member')->check()) {
            return redirect()->route('member.login');
        }
        
        $this->loadRoom();
        
        if (!$this->room) {
            $this->showTopicSelector = true;
        } else {
            $this->markAsRead();
        }
        
        $this->isOpen = true;
    }

    public function selectTopic($topic)
    {
        if ($this->room) {
            $this->room->update(['topic' => $topic]);
            ChatMessage::create([
                'chat_room_id' => $this->room->id,
                'sender_id' => null,
                'message' => 'Topik diubah ke: ' . ($this->topics[$topic]['label'] ?? $topic),
                'type' => 'system',
            ]);
        } else {
            $this->createRoom($topic);
        }
        $this->showTopicSelector = false;
        $this->loadMessages();
    }

    public function loadRoom()
    {
        if (!auth('member')->check()) return;
        
        $this->room = ChatRoom::where('type', 'support')
            ->where('member_id', $this->member()->id)
            ->first();
            
        if ($this->room) {
            $this->lastReadAt = $this->room->member_last_read;
            $this->loadMessages();
        }
    }

    public function createRoom($topic)
    {
        $member = $this->member();
        
        $this->room = ChatRoom::create([
            'type' => 'support',
            'member_id' => $member->id,
            'topic' => $topic,
            'name' => 'Support: ' . $member->name,
            'status' => 'open',
            'member_last_read' => now(),
        ]);

        ChatMessage::create([
            'chat_room_id' => $this->room->id,
            'sender_id' => null,
            'message' => $this->getWelcomeMessage($topic),
            'type' => 'system',
        ]);
        
        $this->notifyStaff();
    }

    public function getWelcomeMessage($topic)
    {
        $topicLabel = $this->topics[$topic]['label'] ?? 'Lainnya';
        $member = $this->member();
        
        return "Selamat datang di Layanan Perpustakaan UNIDA Gontor!\n\n" .
               "Halo {$member->name}, terima kasih telah menghubungi kami.\n" .
               "Topik: {$topicLabel}\n\n" .
               "Staff kami akan segera membalas pesan Anda.\n" .
               "Jam layanan: Senin-Jumat, 08:00-16:00 WIB";
    }

    public function loadMessages()
    {
        if (!$this->room) return;
        
        $this->messages = ChatMessage::where('chat_room_id', $this->room->id)
            ->with('sender:id,name')
            ->orderBy('created_at', 'asc')
            ->get()
            ->toArray();
    }
    
    public function markAsRead()
    {
        if ($this->room) {
            $this->room->update(['member_last_read' => now()]);
            $this->lastReadAt = now();
        }
    }

    public function sendMessage()
    {
        if (!$this->room || (!trim($this->newMessage) && !$this->image)) return;

        $data = [
            'chat_room_id' => $this->room->id,
            'sender_id' => null,
            'type' => 'text',
        ];

        if ($this->image) {
            $path = $this->image->store('chat-attachments', 'public');
            $data['attachment_path'] = $path;
            $data['attachment_name'] = $this->image->getClientOriginalName();
            $data['attachment_type'] = 'image';
            $data['message'] = trim($this->newMessage) ?: null;
        } else {
            $data['message'] = trim($this->newMessage);
        }

        ChatMessage::create($data);
        
        if ($this->room->status === 'resolved') {
            $this->room->update(['status' => 'open']);
        }
        
        $this->room->touch();
        $this->markAsRead();
        $this->notifyStaff();

        $this->newMessage = '';
        $this->image = null;
        $this->loadMessages();
        
        $this->dispatch('message-sent');
    }
    
    protected function notifyStaff()
    {
        $member = $this->member();
        
        // Get all staff
        $staffUsers = User::whereIn('role', ['super_admin', 'admin', 'librarian', 'staff', 'pustakawan'])->get();
            
        foreach ($staffUsers as $staff) {
            // Database notification
            StaffNotification::create([
                'user_id' => $staff->id,
                'type' => 'support_message',
                'title' => 'Pesan Support Baru',
                'message' => "{$member->name} ({$this->topics[$this->room->topic]['label'] ?? 'Support'})",
                'data' => json_encode(['room_id' => $this->room->id, 'member_id' => $member->id]),
                'url' => '/staff/chat?support=' . $this->room->id,
            ]);
            
            // Push notification
            try {
                $staff->notify(new \App\Notifications\SupportMessagePushNotification(
                    $member->name,
                    $this->room->topic,
                    $this->room->id
                ));
            } catch (\Exception $e) {
                // Ignore push errors
            }
        }
    }
    }

    public function closeChat()
    {
        $this->markAsRead();
        $this->isOpen = false;
    }

    public function getUnreadCountProperty()
    {
        if (!$this->room || !auth('member')->check()) return 0;
        
        return ChatMessage::where('chat_room_id', $this->room->id)
            ->whereNotNull('sender_id')
            ->where('type', '!=', 'system')
            ->where('created_at', '>', $this->room->member_last_read ?? '1970-01-01')
            ->count();
    }

    public function render()
    {
        return view('livewire.member.support-chat');
    }
}
