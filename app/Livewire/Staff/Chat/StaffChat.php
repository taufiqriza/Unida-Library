<?php

namespace App\Livewire\Staff\Chat;

use App\Models\Book;
use App\Models\Branch;
use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\ChatRoomMember;
use App\Models\StaffNotification;
use App\Models\Task;
use App\Models\User;
use App\Services\ChatService;
use Livewire\Component;
use Livewire\WithFileUploads;

class StaffChat extends Component
{
    use WithFileUploads;

    public $isOpen = false;
    public $isExpanded = false; // Expanded view for larger chat panel
    public $activeView = 'list';  // list, chat
    public $activeTab = 'conversations'; // conversations, branches, contacts
    public $chatSubTab = 'personal'; // personal, groups (sub-tabs in conversations)
    public $activeRoomId = null;
    public $activeRoom = null;
    public $selectedBranch = null;
    public $message = '';
    public $attachment;
    public $voiceNote;
    public $messages = [];
    public $searchQuery = '';
    
    // Task Picker
    public $showTaskPicker = false;
    public $selectedTaskId = null;
    public $taskSearch = '';
    
    // Task Modal
    public $viewingTaskId = null;
    
    // Book Picker
    public $showBookPicker = false;
    public $selectedBookId = null;
    public $bookSearch = '';
    
    // Message Search
    public $showMessageSearch = false;
    public $messageSearchQuery = '';
    public $searchResults = [];
    
    // Sound & Notification
    public $lastMessageCount = 0;
    public $soundEnabled = true;
    
    // Branch Search
    public $branchSearch = '';

    protected ChatService $chatService;

    public function boot(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    protected $listeners = ['openChatRoom'];

    public function mount()
    {
        $this->updateOnlineStatus();
    }

    public function uploadAttachment($file)
    {
        // Handle pasted image from clipboard
        $this->attachment = $file;
    }

    public function updateOnlineStatus()
    {
        auth()->user()->update([
            'is_online' => true,
            'last_seen_at' => now(),
        ]);
    }

    // Called from notification click via browser event
    public function openChatRoom($roomId)
    {
        $this->isOpen = true;
        $this->openRoom($roomId);
    }

    public function toggle()
    {
        $this->isOpen = !$this->isOpen;
        if ($this->isOpen) {
            $this->updateOnlineStatus();
        } else {
            $this->closeChat();
        }
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->searchQuery = '';
        $this->selectedBranch = null;
    }

    public function selectBranch($branchId)
    {
        $this->selectedBranch = $branchId;
    }

    public function backToList()
    {
        $this->selectedBranch = null;
    }

    public function openRoom($roomId)
    {
        $this->activeRoomId = $roomId;
        $this->activeRoom = ChatRoom::with(['branch', 'members.user'])->find($roomId);
        $this->activeView = 'chat';
        $this->loadMessages();
        $this->markAsRead();
        $this->dispatch('scrollToBottom');
    }

    public function openDirectChat($userId)
    {
        $room = $this->chatService->getDirectRoom(auth()->id(), $userId);
        $this->openRoom($room->id);
    }

    public function closeChat()
    {
        $this->activeRoomId = null;
        $this->activeRoom = null;
        $this->activeView = 'list';
        $this->selectedBranch = null;
        $this->messages = [];
        $this->message = '';
        $this->attachment = null;
    }

    public function loadMessages()
    {
        if (!$this->activeRoomId) return;

        // Get latest 50 messages (ordered by newest first, then reverse for display)
        $this->messages = ChatMessage::where('chat_room_id', $this->activeRoomId)
            ->where('is_deleted', false)
            ->select(['id', 'chat_room_id', 'sender_id', 'message', 'attachment', 'attachment_type', 'attachment_name', 'task_id', 'book_id', 'type', 'voice_path', 'voice_duration', 'created_at'])
            ->with([
                'sender:id,name,photo,branch_id',
                'sender.branch:id,name',
                'task:id,title,status_id,assigned_to,due_date',
                'task.assignee:id,name',
                'task.status:id,name,color',
                'book:id,title,isbn,image',
                'book.authors:id,name'
            ])
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get()
            ->reverse()
            ->values()
            ->toArray();
    }

    public function markAsRead()
    {
        if (!$this->activeRoomId) return;

        ChatRoomMember::where('chat_room_id', $this->activeRoomId)
            ->where('user_id', auth()->id())
            ->update(['last_read_at' => now()]);
    }

    public function sendMessage()
    {
        if (!$this->activeRoomId) return;
        if (empty(trim($this->message)) && !$this->attachment && !$this->selectedTaskId && !$this->selectedBookId) return;

        $attachmentPath = null;
        $attachmentType = null;
        $attachmentName = null;

        if ($this->attachment) {
            $this->validate([
                'attachment' => 'file|max:10240|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip'
            ]);

            $attachmentPath = $this->attachment->store('chat-attachments', 'public');
            $attachmentType = str_starts_with($this->attachment->getMimeType(), 'image/') ? 'image' : 'file';
            $attachmentName = $this->attachment->getClientOriginalName();
        }

        // Create message with optional task/book
        $chatMessage = ChatMessage::create([
            'chat_room_id' => $this->activeRoomId,
            'sender_id' => auth()->id(),
            'message' => trim($this->message) ?: null,
            'attachment' => $attachmentPath,
            'attachment_type' => $attachmentType,
            'attachment_name' => $attachmentName,
            'task_id' => $this->selectedTaskId,
            'book_id' => $this->selectedBookId,
            'type' => 'text',
        ]);

        // Update sender's last_read_at
        ChatRoomMember::where('chat_room_id', $this->activeRoomId)
            ->where('user_id', auth()->id())
            ->update(['last_read_at' => now()]);

        // Send notification to other room members
        $this->sendChatNotifications($chatMessage);

        // Clear room cache to show updated latest message
        cache()->forget("chat_rooms_" . auth()->id());

        $this->message = '';
        $this->attachment = null;
        $this->selectedTaskId = null;
        $this->selectedBookId = null;
        $this->loadMessages();
        $this->dispatch('scrollToBottom');
    }

    protected function sendChatNotifications(ChatMessage $message)
    {
        $room = ChatRoom::find($message->chat_room_id);
        $sender = auth()->user();
        
        // Get other members who haven't muted
        $memberIds = ChatRoomMember::where('chat_room_id', $message->chat_room_id)
            ->where('user_id', '!=', $sender->id)
            ->where('is_muted', false)
            ->pluck('user_id');
        
        if ($memberIds->isEmpty()) return;

        // Prepare notification content
        $roomName = $room->isDirect() ? $sender->name : $room->name;
        $msgPreview = $message->message 
            ? \Str::limit($message->message, 50) 
            : ($message->voice_path ? '🎤 Voice note' : ($message->attachment ? '📎 File' : ($message->task_id ? '📋 Task' : '📚 Buku')));
        
        // Create notification for each member
        foreach ($memberIds as $userId) {
            // Clear their room cache too
            cache()->forget("chat_rooms_{$userId}");
            
            StaffNotification::create([
                'type' => 'chat_message',
                'notifiable_type' => User::class,
                'notifiable_id' => $userId,
                'category' => 'chat',
                'priority' => 'normal',
                'title' => $roomName,
                'body' => $room->isDirect() ? $msgPreview : "{$sender->name}: {$msgPreview}",
                'action_url' => "/staff?open_chat={$room->id}",
                'action_label' => 'Buka Chat',
                'data' => [
                    'room_id' => $room->id,
                    'message_id' => $message->id,
                    'sender_id' => $sender->id,
                    'sender_name' => $sender->name,
                ],
            ]);
        }
    }

    public function removeAttachment()
    {
        $this->attachment = null;
    }

    public function sendVoice($base64, $duration)
    {
        if (!$this->activeRoomId || !$base64) return;
        
        // Extract mime type and decode
        preg_match('#^data:(audio/[^;]+)#i', $base64, $matches);
        $mimeType = $matches[1] ?? 'audio/webm';
        $ext = str_contains($mimeType, 'mp4') ? 'mp4' : 'webm';
        
        $data = base64_decode(preg_replace('#^data:[^;]+;base64,#i', '', $base64));
        $filename = 'voice-notes/' . uniqid() . '.' . $ext;
        
        \Storage::disk('public')->makeDirectory('voice-notes');
        \Storage::disk('public')->put($filename, $data);
        
        $chatMessage = ChatMessage::create([
            'chat_room_id' => $this->activeRoomId,
            'sender_id' => auth()->id(),
            'voice_path' => $filename,
            'voice_duration' => min($duration, 180),
            'type' => 'voice',
        ]);

        ChatRoomMember::where('chat_room_id', $this->activeRoomId)
            ->where('user_id', auth()->id())
            ->update(['last_read_at' => now()]);

        $this->sendChatNotifications($chatMessage);
        cache()->forget("chat_rooms_" . auth()->id());
        $this->loadMessages();
        $this->dispatch('scrollToBottom');
    }

    // Task Picker Methods
    public function openTaskPicker()
    {
        $this->showTaskPicker = true;
        $this->taskSearch = '';
    }

    public function closeTaskPicker()
    {
        $this->showTaskPicker = false;
        $this->taskSearch = '';
    }

    public function attachTask($taskId)
    {
        $this->selectedTaskId = $taskId;
        $this->showTaskPicker = false;
    }

    public function removeTask()
    {
        $this->selectedTaskId = null;
    }

    public function getAvailableTasksProperty()
    {
        $user = auth()->user();
        
        return Task::query()
            ->where(function($q) use ($user) {
                // Tasks from user's branch OR tasks user is involved in
                $q->where('branch_id', $user->branch_id)
                  ->orWhere('reported_by', $user->id)
                  ->orWhere('assigned_to', $user->id);
            })
            ->when($this->taskSearch, fn($q) => 
                $q->where('title', 'like', "%{$this->taskSearch}%")
            )
            ->with(['assignee:id,name', 'status'])
            ->latest()
            ->take(10)
            ->get();
    }

    public function getSelectedTaskProperty()
    {
        if (!$this->selectedTaskId) return null;
        return Task::with(['assignee:id,name', 'status'])->find($this->selectedTaskId);
    }

    public function getViewingTaskProperty()
    {
        if (!$this->viewingTaskId) return null;
        return Task::with(['assignee:id,name', 'reporter:id,name', 'status', 'branch'])->find($this->viewingTaskId);
    }

    public function openTaskModal($taskId)
    {
        $this->viewingTaskId = $taskId;
    }

    public function closeTaskModal()
    {
        $this->viewingTaskId = null;
    }

    // =========================================
    // BOOK PICKER METHODS
    // =========================================
    
    public function openBookPicker()
    {
        $this->showBookPicker = true;
        $this->bookSearch = '';
    }

    public function closeBookPicker()
    {
        $this->showBookPicker = false;
        $this->bookSearch = '';
    }

    public function attachBook($bookId)
    {
        $this->selectedBookId = $bookId;
        $this->showBookPicker = false;
    }

    public function removeBook()
    {
        $this->selectedBookId = null;
    }

    public function getAvailableBooksProperty()
    {
        $user = auth()->user();
        $search = $this->bookSearch;
        
        return Book::query()
            ->where('branch_id', $user->branch_id)
            ->when($search, fn($q) => 
                $q->where(function($q2) use ($search) {
                    $q2->where('title', 'like', "%{$search}%")
                       ->orWhere('isbn', 'like', "%{$search}%")
                       ->orWhereHas('authors', fn($a) => $a->where('name', 'like', "%{$search}%"));
                })
            )
            ->select(['id', 'title', 'isbn', 'image', 'branch_id'])
            ->with('authors:id,name')
            ->withCount('items')
            ->latest()
            ->take(10)
            ->get();
    }

    public function getSelectedBookProperty()
    {
        if (!$this->selectedBookId) return null;
        return Book::select(['id', 'title', 'isbn', 'image'])
            ->with('authors:id,name')
            ->withCount('items')
            ->find($this->selectedBookId);
    }

    // =========================================
    // MESSAGE SEARCH METHODS
    // =========================================
    
    public function toggleMessageSearch()
    {
        $this->showMessageSearch = !$this->showMessageSearch;
        if (!$this->showMessageSearch) {
            $this->messageSearchQuery = '';
            $this->searchResults = [];
        }
    }

    public function searchMessages()
    {
        if (!$this->activeRoomId || strlen($this->messageSearchQuery) < 2) {
            $this->searchResults = [];
            return;
        }

        $this->searchResults = ChatMessage::where('chat_room_id', $this->activeRoomId)
            ->where('is_deleted', false)
            ->where('message', 'like', "%{$this->messageSearchQuery}%")
            ->select(['id', 'sender_id', 'message', 'created_at'])
            ->with('sender:id,name')
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get()
            ->toArray();
    }

    public function clearMessageSearch()
    {
        $this->messageSearchQuery = '';
        $this->searchResults = [];
        $this->showMessageSearch = false;
    }

    // =========================================
    // SOUND NOTIFICATION METHODS
    // =========================================
    
    public function toggleSound()
    {
        $this->soundEnabled = !$this->soundEnabled;
    }

    public function toggleMute()
    {
        if (!$this->activeRoomId) return;

        $member = ChatRoomMember::where('chat_room_id', $this->activeRoomId)
            ->where('user_id', auth()->id())
            ->first();

        if ($member) {
            $member->toggleMute();
        }
    }

    public function refreshData()
    {
        // Only update online status every minute to reduce DB writes
        static $lastOnlineUpdate = null;
        if (!$lastOnlineUpdate || now()->diffInSeconds($lastOnlineUpdate) >= 60) {
            $this->updateOnlineStatus();
            $lastOnlineUpdate = now();
        }
        
        // Only reload messages if we're in chat view
        if ($this->activeRoomId && $this->activeView === 'chat') {
            $oldCount = count($this->messages);
            $this->loadMessages();
            $newCount = count($this->messages);
            
            // Play sound if new messages arrived and sound is enabled
            if ($newCount > $oldCount && $this->soundEnabled) {
                // Check if the new message is not from current user
                if (!empty($this->messages)) {
                    $lastMsg = end($this->messages);
                    if ($lastMsg['sender_id'] !== auth()->id()) {
                        $this->dispatch('playNewMessageSound');
                    }
                }
            }
            
            $this->markAsRead();
        }
    }

    // Computed Properties

    public function getRoomsProperty()
    {
        $userId = auth()->id();
        
        // Cache rooms for 30 seconds to reduce queries
        $cacheKey = "chat_rooms_{$userId}";
        
        $rooms = cache()->remember($cacheKey, 30, function () use ($userId) {
            return ChatRoom::forUser($userId)
                ->select(['id', 'name', 'type', 'branch_id'])
                ->withCount('members')
                ->with([
                    'latestMessage:id,chat_room_id,sender_id,message,created_at',
                    'latestMessage.sender:id,name',
                    'branch:id,name'
                ])
                ->get();
        });

        // Calculate unread and sort (not cached - dynamic)
        $rooms = $rooms->map(function ($room) use ($userId) {
            $room->unread_count = $room->getUnreadCountFor($userId);
            $room->display_name = $room->getDisplayNameFor($userId);
            $room->other_user = $room->isDirect() ? $room->getOtherUser($userId) : null;
            return $room;
        });

        // Filter by search
        if ($this->searchQuery) {
            $rooms = $rooms->filter(function ($room) {
                return str_contains(strtolower($room->display_name), strtolower($this->searchQuery));
            });
        }

        // Separate groups and direct
        $groups = $rooms->filter(fn($r) => $r->isGroup())->sortByDesc('latestMessage.created_at');
        $directs = $rooms->filter(fn($r) => $r->isDirect())->sortByDesc('latestMessage.created_at');

        return [
            'groups' => $groups->values(),
            'directs' => $directs->values(),
        ];
    }

    public function getBranchesProperty()
    {
        return Branch::with(['users' => function($q) {
            $q->where('id', '!=', auth()->id())
              ->whereIn('role', ['super_admin', 'admin', 'librarian', 'staff', 'pustakawan'])
              ->select(['id', 'branch_id', 'last_seen_at']);
        }])
        ->withCount(['users' => function($q) {
            $q->where('id', '!=', auth()->id())
              ->whereIn('role', ['super_admin', 'admin', 'librarian', 'staff', 'pustakawan']);
        }])
        ->when($this->branchSearch, fn($q) => $q->where('name', 'like', "%{$this->branchSearch}%"))
        ->orderByDesc('is_main')
        ->orderBy('name')
        ->get();
    }

    public function getBranchContactsProperty()
    {
        if (!$this->selectedBranch) return collect();

        return User::with('branch')
            ->where('id', '!=', auth()->id())
            ->where('branch_id', $this->selectedBranch)
            ->whereIn('role', ['super_admin', 'admin', 'librarian', 'staff', 'pustakawan'])
            ->when($this->searchQuery, fn($q) => $q->where('name', 'like', "%{$this->searchQuery}%"))
            ->orderBy('name')
            ->get();
    }

    public function getAllContactsProperty()
    {
        $query = User::with('branch')
            ->where('id', '!=', auth()->id())
            ->whereIn('role', ['super_admin', 'admin', 'librarian', 'staff', 'pustakawan'])
            ->when($this->searchQuery, fn($q) => $q->where('name', 'like', "%{$this->searchQuery}%"))
            ->orderByRaw('branch_id IS NOT NULL') // Users without branch first
            ->orderBy('name')
            ->get();

        // Group by branch, with Super Admin/Pusat first
        return $query->groupBy(fn($user) => $user->branch?->name ?? 'Super Admin / Pusat');
    }

    public function getUnreadCountProperty()
    {
        return $this->chatService->getTotalUnreadCount(auth()->id());
    }

    public function getRoomMembersProperty()
    {
        if (!$this->activeRoom) return collect();
        
        return $this->activeRoom->users()
            ->orderByDesc('is_online')
            ->orderBy('name')
            ->get();
    }

    public function formatMessage($message)
    {
        if (!$message) return '';
        $pattern = '/(https?:\/\/[^\s<]+)/i';
        return preg_replace($pattern, '<a href="$1" target="_blank" class="underline hover:no-underline">$1</a>', e($message));
    }

    public function render()
    {
        return view('livewire.staff.chat.staff-chat');
    }
}
