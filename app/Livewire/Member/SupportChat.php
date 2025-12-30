<?php

namespace App\Livewire\Member;

use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\ChatRoomMember;
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
    
    public $topics = [
        'unggah_mandiri' => ['icon' => 'fa-upload', 'label' => 'Unggah Mandiri'],
        'plagiasi' => ['icon' => 'fa-search', 'label' => 'Cek Plagiasi'],
        'bebas_pustaka' => ['icon' => 'fa-clipboard-check', 'label' => 'Bebas Pustaka'],
        'peminjaman' => ['icon' => 'fa-book', 'label' => 'Peminjaman/Pengembalian'],
        'lainnya' => ['icon' => 'fa-question-circle', 'label' => 'Lainnya'],
    ];

    public function mount()
    {
        if (auth()->check()) {
            $this->loadRoom();
        }
    }

    public function openChat()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        
        $this->loadRoom();
        
        if (!$this->room) {
            $this->showTopicSelector = true;
        }
        
        $this->isOpen = true;
    }

    public function selectTopic($topic)
    {
        $this->createRoom($topic);
        $this->showTopicSelector = false;
        $this->loadMessages();
    }

    public function loadRoom()
    {
        $this->room = ChatRoom::where('type', 'support')
            ->where('member_id', auth()->id())
            ->first();
            
        if ($this->room) {
            $this->loadMessages();
        }
    }

    public function createRoom($topic)
    {
        $user = auth()->user();
        
        $this->room = ChatRoom::create([
            'type' => 'support',
            'member_id' => $user->id,
            'topic' => $topic,
            'name' => 'Support: ' . $user->name,
            'status' => 'open',
        ]);

        // Add member to room
        ChatRoomMember::create([
            'chat_room_id' => $this->room->id,
            'user_id' => $user->id,
            'role' => 'member',
        ]);

        // Send auto welcome message
        ChatMessage::create([
            'chat_room_id' => $this->room->id,
            'sender_id' => null,
            'message' => $this->getWelcomeMessage($topic),
            'type' => 'system',
        ]);
    }

    public function getWelcomeMessage($topic)
    {
        $topicLabel = $this->topics[$topic]['label'] ?? 'Lainnya';
        $user = auth()->user();
        
        return "Selamat datang di Layanan Perpustakaan UNIDA Gontor!\n\n" .
               "Halo {$user->name}, terima kasih telah menghubungi kami.\n" .
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
            
        // Mark as read
        ChatRoomMember::where('chat_room_id', $this->room->id)
            ->where('user_id', auth()->id())
            ->update(['last_read_at' => now()]);
    }

    public function sendMessage()
    {
        if (!$this->room || (!trim($this->newMessage) && !$this->image)) return;

        $data = [
            'chat_room_id' => $this->room->id,
            'sender_id' => auth()->id(),
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
        
        // Update room status if resolved
        if ($this->room->status === 'resolved') {
            $this->room->update(['status' => 'open']);
        }

        $this->newMessage = '';
        $this->image = null;
        $this->loadMessages();
        
        $this->dispatch('message-sent');
    }

    public function closeChat()
    {
        $this->isOpen = false;
    }

    public function getUnreadCountProperty()
    {
        if (!$this->room) return 0;
        
        $member = ChatRoomMember::where('chat_room_id', $this->room->id)
            ->where('user_id', auth()->id())
            ->first();
            
        if (!$member) return 0;
        
        return ChatMessage::where('chat_room_id', $this->room->id)
            ->where('sender_id', '!=', auth()->id())
            ->where('created_at', '>', $member->last_read_at ?? '1970-01-01')
            ->count();
    }

    public function render()
    {
        return view('livewire.member.support-chat');
    }
}
