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
    public $pinnedMessages = [];
    public $searchQuery = '';
    public $typingUsers = []; // Users currently typing
    public $replyTo = null; // Message being replied to
    public $forwardingMessage = null; // Message being forwarded
    public $showForwardModal = false;
    public $showReactionPicker = null; // Message ID for reaction picker
    
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

    public function canAccessSupportChat(): bool
    {
        return in_array(auth()->user()->role, ['super_admin', 'admin', 'librarian']);
    }

    public function openRoom($roomId)
    {
        $room = ChatRoom::find($roomId);
        
        // Staff hanya bisa lihat list support, tidak bisa buka chat
        if ($room && $room->type === 'support' && !$this->canAccessSupportChat()) {
            return;
        }

        $this->activeRoomId = $roomId;
        $this->activeRoom = ChatRoom::with([
            'branch',
            'members.user',
            'member' => fn($q) => $q->withoutGlobalScope('branch'),
            'member.branch'
        ])->find($roomId);
        $this->activeView = 'chat';
        $this->loadMessages();
        $this->markAsRead();
        
        // Add staff to support room if not member yet
        if ($this->activeRoom && $this->activeRoom->type === 'support') {
            ChatRoomMember::firstOrCreate([
                'chat_room_id' => $roomId,
                'user_id' => auth()->id(),
            ], ['role' => 'staff']);
            
            // Mark support notifications as read
            StaffNotification::where('notifiable_id', auth()->id())
                ->where('notifiable_type', User::class)
                ->where('type', 'support_message')
                ->whereNull('read_at')
                ->whereJsonContains('data->room_id', $roomId)
                ->update(['read_at' => now()]);
        }
        
        $this->dispatch('scrollToBottom');
    }

    public function markSupportResolved()
    {
        if ($this->activeRoom && $this->activeRoom->type === 'support') {
            $this->activeRoom->update(['status' => 'resolved']);
            ChatMessage::create([
                'chat_room_id' => $this->activeRoomId,
                'sender_id' => null,
                'message' => 'Percakapan ditandai selesai oleh ' . auth()->user()->name,
                'type' => 'system',
            ]);
            $this->loadMessages();
        }
    }

    public function reopenSupport()
    {
        if ($this->activeRoom && $this->activeRoom->type === 'support') {
            $this->activeRoom->update(['status' => 'open']);
        }
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

        // Get latest 50 messages
        $messages = ChatMessage::where('chat_room_id', $this->activeRoomId)
            ->select(['id', 'chat_room_id', 'sender_id', 'message', 'attachment', 'attachment_type', 'attachment_name', 'task_id', 'book_id', 'reply_to_id', 'type', 'voice_path', 'voice_duration', 'is_deleted', 'is_pinned', 'forwarded_from_id', 'created_at'])
            ->with([
                'sender:id,name,photo,branch_id',
                'sender.branch:id,name',
                'task:id,title,status_id,assigned_to,due_date',
                'task.assignee:id,name',
                'task.status:id,name,color',
                'book:id,title,isbn,image',
                'book.authors:id,name',
                'replyTo:id,sender_id,message,attachment_type',
                'replyTo.sender:id,name',
                'reactions:id,message_id,user_id,emoji',
                'reactions.user:id,name',
                'forwardedFrom:id,sender_id,message',
                'forwardedFrom.sender:id,name',
            ])
            ->withCount('reads')
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get()
            ->reverse()
            ->values();
        
        $roomMemberCount = ChatRoomMember::where('chat_room_id', $this->activeRoomId)->count();
        
        $this->messages = $messages->map(function($msg) use ($roomMemberCount) {
            $arr = $msg->toArray();
            $arr['reads_count'] = $msg->reads_count;
            $arr['total_recipients'] = $roomMemberCount - 1;
            // Group reactions by emoji
            $arr['reactions_grouped'] = collect($msg->reactions)->groupBy('emoji')->map(fn($r) => [
                'count' => $r->count(),
                'users' => $r->pluck('user.name')->toArray(),
                'user_ids' => $r->pluck('user_id')->toArray(),
            ])->toArray();
            return $arr;
        })->toArray();
        
        // Load pinned messages
        $this->loadPinnedMessages();
        
        // Mark messages as read
        $this->markMessagesAsRead();
    }
    
    public function loadPinnedMessages()
    {
        if (!$this->activeRoomId) return;
        
        $this->pinnedMessages = ChatMessage::where('chat_room_id', $this->activeRoomId)
            ->where('is_pinned', true)
            ->with(['sender:id,name', 'pinnedByUser:id,name'])
            ->orderBy('pinned_at', 'desc')
            ->get()
            ->toArray();
    }
    
    public function markMessagesAsRead()
    {
        if (!$this->activeRoomId) return;
        
        $userId = auth()->id();
        
        $unreadMessageIds = ChatMessage::where('chat_room_id', $this->activeRoomId)
            ->where('sender_id', '!=', $userId)
            ->whereDoesntHave('reads', fn($q) => $q->where('user_id', $userId))
            ->pluck('id');
        
        if ($unreadMessageIds->isEmpty()) return;
        
        $reads = $unreadMessageIds->map(fn($id) => [
            'message_id' => $id,
            'user_id' => $userId,
            'read_at' => now(),
        ])->toArray();
        
        \App\Models\ChatMessageRead::insert($reads);
        
        // Mark mentions as read
        \App\Models\ChatMention::whereIn('message_id', $unreadMessageIds)
            ->where('user_id', $userId)
            ->update(['is_read' => true]);
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

        // Create message with optional task/book/reply
        $chatMessage = ChatMessage::create([
            'chat_room_id' => $this->activeRoomId,
            'sender_id' => auth()->id(),
            'message' => trim($this->message) ?: null,
            'attachment' => $attachmentPath,
            'attachment_type' => $attachmentType,
            'attachment_name' => $attachmentName,
            'task_id' => $this->selectedTaskId,
            'book_id' => $this->selectedBookId,
            'reply_to_id' => $this->replyTo['id'] ?? null,
            'type' => 'text',
        ]);
        
        // Process mentions (@username)
        $this->processMentions($chatMessage);

        // Update sender's last_read_at
        ChatRoomMember::where('chat_room_id', $this->activeRoomId)
            ->where('user_id', auth()->id())
            ->update(['last_read_at' => now()]);
            
        // Update last_staff_id for support chat and notify member
        if ($this->activeRoom && $this->activeRoom->type === 'support') {
            $this->activeRoom->update(['last_staff_id' => auth()->id(), 'status' => 'open']);
            $this->notifyMember($chatMessage);
        }

        // Send notification to other room members
        $this->sendChatNotifications($chatMessage);

        // Clear room cache to show updated latest message
        cache()->forget("chat_rooms_" . auth()->id());

        $this->message = '';
        $this->attachment = null;
        $this->replyTo = null;
        $this->selectedTaskId = null;
        $this->selectedBookId = null;
        $this->loadMessages();
        $this->dispatch('scrollToBottom');
    }
    
    protected function processMentions(ChatMessage $message)
    {
        if (!$message->message) return;
        
        // Find @mentions in message
        preg_match_all('/@(\w+)/', $message->message, $matches);
        if (empty($matches[1])) return;
        
        // Get room members
        $memberIds = ChatRoomMember::where('chat_room_id', $message->chat_room_id)
            ->where('user_id', '!=', auth()->id())
            ->pluck('user_id');
        
        // Find mentioned users
        $mentionedUsers = User::whereIn('id', $memberIds)
            ->where(function($q) use ($matches) {
                foreach ($matches[1] as $name) {
                    $q->orWhere('name', 'like', $name . '%');
                }
            })
            ->pluck('id');
        
        // Create mention records
        foreach ($mentionedUsers as $userId) {
            \App\Models\ChatMention::create([
                'message_id' => $message->id,
                'user_id' => $userId,
                'created_at' => now(),
            ]);
        }
    }
    
    protected function notifyMember(ChatMessage $message)
    {
        $member = $this->activeRoom->member;
        if (!$member || !$member->email) return;
        
        try {
            $member->notify(new \App\Notifications\SupportReplyNotification(
                auth()->user()->name,
                \Str::limit($message->message, 100),
                $this->activeRoom->topic
            ));
        } catch (\Exception $e) {
            \Log::error('Failed to send support reply notification: ' . $e->getMessage());
        }
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
            
            // Send push notification
            $user = User::find($userId);
            if ($user) {
                try {
                    $user->notify(new \App\Notifications\ChatPushNotification(
                        $roomName,
                        $room->isDirect() ? $msgPreview : "{$sender->name}: {$msgPreview}",
                        $room->id
                    ));
                } catch (\Exception $e) {}
            }
        }
    }

    public function replyToMessage($messageId)
    {
        $msg = collect($this->messages)->firstWhere('id', $messageId);
        if ($msg) {
            $this->replyTo = $msg;
            $this->dispatch('focusInput');
        }
    }

    public function cancelReply()
    {
        $this->replyTo = null;
    }

    public function deleteMessage($messageId)
    {
        $message = ChatMessage::where('id', $messageId)
            ->where('chat_room_id', $this->activeRoomId)
            ->where('sender_id', auth()->id())
            ->first();

        if ($message) {
            $message->update(['is_deleted' => true, 'message' => null]);
            $this->loadMessages();
        }
    }
    
    // Pin/Unpin Message
    public function togglePin($messageId)
    {
        $message = ChatMessage::where('id', $messageId)
            ->where('chat_room_id', $this->activeRoomId)
            ->first();
            
        if (!$message) return;
        
        if ($message->is_pinned) {
            $message->update(['is_pinned' => false, 'pinned_by' => null, 'pinned_at' => null]);
        } else {
            $message->update(['is_pinned' => true, 'pinned_by' => auth()->id(), 'pinned_at' => now()]);
        }
        
        $this->loadMessages();
    }
    
    // Reaction
    public function toggleReaction($messageId, $emoji)
    {
        $userId = auth()->id();
        $existing = \App\Models\ChatMessageReaction::where('message_id', $messageId)
            ->where('user_id', $userId)
            ->where('emoji', $emoji)
            ->first();
            
        if ($existing) {
            $existing->delete();
        } else {
            \App\Models\ChatMessageReaction::create([
                'message_id' => $messageId,
                'user_id' => $userId,
                'emoji' => $emoji,
                'created_at' => now(),
            ]);
        }
        
        $this->showReactionPicker = null;
        $this->loadMessages();
    }
    
    // Forward Message
    public function openForwardModal($messageId)
    {
        $this->forwardingMessage = ChatMessage::with('sender:id,name')->find($messageId);
        $this->showForwardModal = true;
    }
    
    public function forwardTo($roomId)
    {
        if (!$this->forwardingMessage) return;
        
        ChatMessage::create([
            'chat_room_id' => $roomId,
            'sender_id' => auth()->id(),
            'message' => $this->forwardingMessage->message,
            'attachment' => $this->forwardingMessage->attachment,
            'attachment_type' => $this->forwardingMessage->attachment_type,
            'attachment_name' => $this->forwardingMessage->attachment_name,
            'forwarded_from_id' => $this->forwardingMessage->id,
            'type' => 'message',
        ]);
        
        $this->showForwardModal = false;
        $this->forwardingMessage = null;
        
        // If forwarding to current room, reload
        if ($roomId == $this->activeRoomId) {
            $this->loadMessages();
        }
    }
    
    public function cancelForward()
    {
        $this->showForwardModal = false;
        $this->forwardingMessage = null;
    }

    public function removeAttachment()
    {
        $this->attachment = null;
    }

    // @codingStandardsIgnoreStart - base64 is required for voice note upload from browser
    public function sendVoice($base64, $duration)
    {
        if (!$this->activeRoomId || !$base64) return;
        
        // Sanitize: Remove data URL prefix (handles codecs like audio/webm;codecs=opus)
        $base64Data = preg_replace('/^data:audio\/[^;,]+[^,]*,/', '', $base64);
        $data = base64_decode($base64Data);
        
        if (!$data || strlen($data) < 100 || strlen($data) > 10485760) {
            return; // Invalid or too large (max 10MB)
        }
        
        // Detect format from actual content (WebM starts with 0x1A45DFA3)
        $ext = (substr($data, 0, 4) === "\x1A\x45\xDF\xA3") ? 'webm' : 'mp4';
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
                ->where('type', '!=', 'support')
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
        
        // Support rooms - show all, but prioritize connected ones
        $support = ChatRoom::where('type', 'support')
            ->with(['member' => fn($q) => $q->withoutGlobalScope('branch'), 'member.branch:id,name', 'latestMessage:id,chat_room_id,sender_id,message,type,created_at'])
            ->orderByDesc('connected_to_staff')
            ->orderByDesc('updated_at')
            ->get();
        
        $support = $support
            ->map(function ($room) use ($userId) {
                $staffMember = ChatRoomMember::where('chat_room_id', $room->id)
                    ->where('user_id', $userId)
                    ->first();
                
                // Only count unread if connected to staff
                if ($room->connected_to_staff) {
                    if ($staffMember && $staffMember->last_read_at) {
                        $room->unread_count = ChatMessage::where('chat_room_id', $room->id)
                            ->whereNull('sender_id')
                            ->where('type', 'text')
                            ->where('created_at', '>', $staffMember->last_read_at)
                            ->count();
                    } else {
                        $room->unread_count = ChatMessage::where('chat_room_id', $room->id)
                            ->whereNull('sender_id')
                            ->where('type', 'text')
                            ->count();
                    }
                } else {
                    $room->unread_count = 0;
                }
                return $room;
            });

        return [
            'groups' => $groups->values(),
            'directs' => $directs->values(),
            'support' => $support,
        ];
    }
    
    public function getForwardRoomsProperty()
    {
        // Get all rooms for forward modal (simpler query)
        return ChatRoom::whereHas('members', fn($q) => $q->where('user_id', auth()->id()))
            ->whereIn('type', ['direct', 'group', 'branch'])
            ->with(['members.user:id,name'])
            ->orderBy('name')
            ->get();
    }
    
    public function getUnreadCountProperty()
    {
        $rooms = $this->rooms;
        $total = $rooms['directs']->sum('unread_count') 
               + $rooms['groups']->sum('unread_count')
               + $rooms['support']->sum('unread_count');
        return $total;
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

    public function refreshMessages()
    {
        if ($this->activeRoomId && $this->activeView === 'chat') {
            $oldCount = count($this->messages);
            $this->loadMessages();
            $this->loadTypingUsers();
            $this->updateOnlineStatus();
            
            // Play sound if new message from others
            if (count($this->messages) > $oldCount && $this->soundEnabled) {
                $lastMsg = end($this->messages);
                if ($lastMsg && ($lastMsg['sender_id'] ?? null) !== auth()->id()) {
                    $this->dispatch('playNewMessageSound');
                }
            }
        }
    }

    public function startTyping()
    {
        if (!$this->activeRoomId) return;
        
        cache()->put(
            "typing:{$this->activeRoomId}:" . auth()->id(),
            auth()->user()->name,
            now()->addSeconds(5)
        );
    }

    public function stopTyping()
    {
        if (!$this->activeRoomId) return;
        cache()->forget("typing:{$this->activeRoomId}:" . auth()->id());
    }

    protected function loadTypingUsers()
    {
        if (!$this->activeRoomId) {
            $this->typingUsers = [];
            return;
        }

        $typing = [];
        $members = ChatRoomMember::where('chat_room_id', $this->activeRoomId)
            ->where('user_id', '!=', auth()->id())
            ->pluck('user_id');

        foreach ($members as $userId) {
            $name = cache()->get("typing:{$this->activeRoomId}:{$userId}");
            if ($name) {
                $typing[] = $name;
            }
        }

        $this->typingUsers = $typing;
    }

    public function render()
    {
        return view('livewire.staff.chat.staff-chat');
    }
}
