<?php

namespace App\Livewire\Member;

use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\StaffNotification;
use App\Models\User;
use App\Services\ChatbotService;
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
    public $connectedToStaff = false; // Flag: sudah terhubung ke staff
    
    public $topics = [
        'unggah' => ['icon' => 'fa-upload', 'label' => 'Unggah Mandiri'],
        'plagiasi' => ['icon' => 'fa-search', 'label' => 'Cek Plagiasi'],
        'bebas' => ['icon' => 'fa-clipboard-check', 'label' => 'Bebas Pustaka'],
        'pinjam' => ['icon' => 'fa-book', 'label' => 'Peminjaman'],
        'lainnya' => ['icon' => 'fa-question-circle', 'label' => 'Lainnya'],
    ];

    protected ChatbotService $chatbot;

    public function boot(ChatbotService $chatbot)
    {
        $this->chatbot = $chatbot;
    }

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
                'message' => 'Keperluan diubah ke: ' . ($this->topics[$topic]['label'] ?? $topic),
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
            $this->connectedToStaff = $this->room->connected_to_staff ?? false;
            $this->loadMessages();
        }
    }

    public function connectToStaff()
    {
        if (!$this->room) {
            $this->createRoom('lainnya');
        }
        
        $this->connectedToStaff = true;
        $this->room->update(['connected_to_staff' => true]);
        
        // System message
        ChatMessage::create([
            'chat_room_id' => $this->room->id,
            'sender_id' => null,
            'message' => "👨‍💼 Anda terhubung dengan pustakawan.\nMohon tunggu balasan dari tim kami.\n\n⏱️ Jam layanan: Senin-Kamis 08:00-16:00, Sabtu-Minggu 08:00-21:00",
            'type' => 'bot',
        ]);
        
        $this->notifyStaff();
        $this->loadMessages();
        $this->dispatch('message-sent');
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

        // Bot welcome message
        $this->chatbot->createBotMessage($this->room, $this->getBotWelcome($topic));
    }

    protected function getBotWelcome($topic): string
    {
        $member = $this->member();
        $name = explode(' ', $member->name)[0];
        $topicLabel = $this->topics[$topic]['label'] ?? 'Lainnya';
        
        return "👋 **Halo, {$name}!**\n\nSaya asisten virtual perpustakaan UNIDA Gontor.\nKeperluan: **{$topicLabel}**\n\nSilakan ketik pertanyaan Anda, atau pilih topik:\n• **unggah** - Cara upload karya ilmiah\n• **plagiasi** - Cek plagiasi\n• **bebas pustaka** - Surat bebas pustaka\n• **pinjam** - Info peminjaman\n• **jam** - Jam operasional\n\nKetik **\"staff\"** untuk bicara dengan pustakawan.";
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

        $messageText = trim($this->newMessage);
        
        // Save member message
        $data = [
            'chat_room_id' => $this->room->id,
            'sender_id' => null,
            'type' => 'text',
        ];

        if ($this->image) {
            $path = $this->image->store('chat-attachments', 'public');
            $data['attachment'] = $path;
            $data['attachment_name'] = $this->image->getClientOriginalName();
            $data['attachment_type'] = 'image';
            $data['message'] = $messageText ?: null;
        } else {
            $data['message'] = $messageText;
        }

        ChatMessage::create($data);
        
        $this->newMessage = '';
        $this->image = null;
        
        // If connected to staff, skip bot and notify staff directly
        if ($this->connectedToStaff) {
            $this->notifyStaff();
        } elseif ($messageText && !$this->image) {
            // Process with chatbot (only for text messages)
            $botResponse = $this->chatbot->processMessage($this->room, $messageText);
            
            if ($botResponse) {
                $this->chatbot->createBotMessage($this->room, $botResponse['message']);
                
                // If not handled by bot (wants staff), connect to staff
                if (!$botResponse['handled']) {
                    $this->connectedToStaff = true;
                    $this->room->update(['connected_to_staff' => true]);
                    $this->notifyStaff();
                }
            }
        } else {
            // Image/attachment - always notify staff
            $this->notifyStaff();
        }
        
        if ($this->room->status === 'resolved') {
            $this->room->update(['status' => 'open']);
        }
        
        $this->room->touch();
        $this->markAsRead();
        $this->loadMessages();
        
        $this->dispatch('message-sent');
    }
    
    protected function notifyStaff()
    {
        $member = $this->member();
        $topicLabel = $this->topics[$this->room->topic]['label'] ?? 'Support';
        
        // Only notify admin/librarian (not staff)
        $staffUsers = User::whereIn('role', ['super_admin', 'admin', 'librarian'])->get();
            
        foreach ($staffUsers as $staff) {
            // Check if already notified recently (within 5 minutes)
            $recentNotif = StaffNotification::where('notifiable_id', $staff->id)
                ->where('type', 'support_message')
                ->whereJsonContains('data->room_id', $this->room->id)
                ->where('created_at', '>', now()->subMinutes(5))
                ->exists();
                
            if ($recentNotif) continue;
            
            StaffNotification::create([
                'notifiable_type' => User::class,
                'notifiable_id' => $staff->id,
                'type' => 'support_message',
                'title' => 'Pesan Support Baru',
                'body' => "{$member->name} ({$topicLabel})",
                'data' => ['room_id' => $this->room->id, 'member_id' => $member->id],
                'action_url' => '/staff/chat?support=' . $this->room->id,
                'icon' => 'headset',
                'color' => 'orange',
            ]);
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
            ->where(function($q) {
                $q->whereNotNull('sender_id')->orWhere('type', 'bot');
            })
            ->where('type', '!=', 'system')
            ->where('created_at', '>', $this->room->member_last_read ?? '1970-01-01')
            ->count();
    }

    public function render()
    {
        return view('livewire.member.support-chat');
    }
}
