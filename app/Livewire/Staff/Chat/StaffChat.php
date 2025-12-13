<?php

namespace App\Livewire\Staff\Chat;

use App\Models\Branch;
use App\Models\StaffMessage;
use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;

class StaffChat extends Component
{
    use WithFileUploads;

    public $isOpen = false;
    public $activeTab = 'conversations'; // conversations, contacts, branches
    public $activeChat = null;
    public $activeChatUser = null;
    public $selectedBranch = null;
    public $message = '';
    public $attachment;
    public $messages = [];
    public $searchContact = '';

    protected $listeners = ['openChat'];

    public function mount()
    {
        $this->updateOnlineStatus();
    }

    public function updateOnlineStatus()
    {
        auth()->user()->update([
            'is_online' => true,
            'last_seen_at' => now(),
        ]);
    }

    public function toggle()
    {
        $this->isOpen = !$this->isOpen;
        if ($this->isOpen) {
            $this->updateOnlineStatus();
        }
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->searchContact = '';
        $this->selectedBranch = null;
    }

    public function selectBranch($branchId)
    {
        $this->selectedBranch = $branchId;
        $this->activeTab = 'contacts';
    }

    public function openChat($userId)
    {
        $this->activeChat = $userId;
        $this->activeChatUser = User::with('branch')->find($userId);
        $this->loadMessages();
        $this->markAsRead();
    }

    public function closeChat()
    {
        $this->activeChat = null;
        $this->activeChatUser = null;
        $this->messages = [];
        $this->message = '';
    }

    public function backToContacts()
    {
        $this->selectedBranch = null;
        $this->activeTab = 'branches';
    }

    public function loadMessages()
    {
        if (!$this->activeChat) return;

        $this->messages = StaffMessage::conversation(auth()->id(), $this->activeChat)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->take(100)
            ->get()
            ->toArray();
    }

    public function markAsRead()
    {
        if (!$this->activeChat) return;

        StaffMessage::where('sender_id', $this->activeChat)
            ->where('receiver_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function sendMessage()
    {
        if (!$this->activeChat) return;
        if (empty(trim($this->message)) && !$this->attachment) return;

        // Validate attachment if present
        if ($this->attachment) {
            $this->validate([
                'attachment' => 'file|max:10240|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip'
            ]);
        }

        $data = [
            'sender_id' => auth()->id(),
            'receiver_id' => $this->activeChat,
            'message' => trim($this->message) ?: null,
        ];

        if ($this->attachment) {
            $path = $this->attachment->store('chat-attachments', 'public');
            $data['attachment'] = $path;
            $data['attachment_type'] = str_starts_with($this->attachment->getMimeType(), 'image/') ? 'image' : 'file';
        }

        StaffMessage::create($data);

        $this->message = '';
        $this->attachment = null;
        $this->loadMessages();
        $this->dispatch('scrollToBottom');
    }

    public function removeAttachment()
    {
        $this->attachment = null;
    }

    public function getConversationsProperty()
    {
        $userId = auth()->id();

        // Get all conversation partner IDs first
        $partnerIds = StaffMessage::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->get()
            ->map(fn($msg) => $msg->sender_id === $userId ? $msg->receiver_id : $msg->sender_id)
            ->unique()
            ->values();

        // Eager load all users at once to avoid N+1
        $users = User::with('branch')->whereIn('id', $partnerIds)->get()->keyBy('id');

        $conversations = StaffMessage::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($msg) use ($userId) {
                return $msg->sender_id === $userId ? $msg->receiver_id : $msg->sender_id;
            })
            ->map(function ($messages) use ($userId, $users) {
                $latest = $messages->first();
                $otherUserId = $latest->sender_id === $userId ? $latest->receiver_id : $latest->sender_id;
                $unread = $messages->where('receiver_id', $userId)->whereNull('read_at')->count();
                
                return [
                    'user' => $users->get($otherUserId),
                    'latest' => $latest,
                    'unread' => $unread,
                ];
            })
            ->filter(fn($c) => $c['user'] !== null)
            ->values();

        return $conversations;
    }

    public function getContactsProperty()
    {
        $query = User::with('branch')
            ->where('id', '!=', auth()->id())
            ->whereIn('role', ['super_admin', 'admin', 'librarian', 'staff', 'superadmin', 'pustakawan'])
            ->when($this->searchContact, fn($q) => $q->where('name', 'like', "%{$this->searchContact}%"))
            ->when($this->selectedBranch, fn($q) => $q->where('branch_id', $this->selectedBranch))
            ->orderBy('name')
            ->get();

        if ($this->selectedBranch) {
            return $query;
        }

        return $query->groupBy(fn($user) => $user->branch?->name ?? 'Tanpa Cabang');
    }

    public function getBranchesProperty()
    {
        return Branch::withCount(['users' => function($q) {
            $q->where('id', '!=', auth()->id())
              ->whereIn('role', ['super_admin', 'admin', 'librarian', 'staff', 'superadmin', 'pustakawan']);
        }])->orderByDesc('is_main')->orderBy('name')->get();
    }

    public function getUnreadCountProperty()
    {
        return StaffMessage::unreadFor(auth()->id())->count();
    }

    public function formatMessage($message)
    {
        $pattern = '/(https?:\/\/[^\s<]+)/i';
        return preg_replace($pattern, '<a href="$1" target="_blank" class="underline hover:no-underline">$1</a>', e($message));
    }

    public function refreshData()
    {
        $this->updateOnlineStatus();
        if ($this->activeChat) {
            $this->loadMessages();
            $this->markAsRead();
        }
    }

    public function render()
    {
        return view('livewire.staff.chat.staff-chat');
    }
}
