<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\ChatRoomMember;
use App\Models\User;

class ChatService
{
    /**
     * Staff roles that should be in chat groups
     */
    protected array $staffRoles = ['super_admin', 'admin', 'librarian', 'staff', 'pustakawan'];

    /**
     * Get or create the global "All Staff" room
     */
    public function getGlobalRoom(): ChatRoom
    {
        return ChatRoom::firstOrCreate(
            ['type' => 'global'],
            [
                'name' => 'Semua Staff',
                'description' => 'Group untuk seluruh staff perpustakaan',
                'icon' => 'fa-globe',
                'color' => '#3b82f6',
            ]
        );
    }

    /**
     * Get or create branch-specific room
     */
    public function getBranchRoom(Branch $branch): ChatRoom
    {
        return ChatRoom::firstOrCreate(
            ['type' => 'branch', 'branch_id' => $branch->id],
            [
                'name' => $branch->name,
                'description' => "Group khusus staff {$branch->name}",
                'icon' => 'fa-building',
                'color' => $this->getColorForBranch($branch->id),
            ]
        );
    }

    /**
     * Get or create direct chat room between two users
     */
    public function getDirectRoom(int $userId1, int $userId2): ChatRoom
    {
        // Check if room already exists
        $room = ChatRoom::where('type', 'direct')
            ->whereHas('members', fn($q) => $q->where('user_id', $userId1))
            ->whereHas('members', fn($q) => $q->where('user_id', $userId2))
            ->first();

        if ($room) {
            return $room;
        }

        // Create new direct room
        $room = ChatRoom::create(['type' => 'direct']);

        // Add both users
        ChatRoomMember::create([
            'chat_room_id' => $room->id,
            'user_id' => $userId1,
            'role' => 'member',
            'joined_at' => now(),
        ]);

        ChatRoomMember::create([
            'chat_room_id' => $room->id,
            'user_id' => $userId2,
            'role' => 'member',
            'joined_at' => now(),
        ]);

        return $room;
    }

    /**
     * Sync all staff to global room
     */
    public function syncGlobalRoomMembers(): void
    {
        $globalRoom = $this->getGlobalRoom();

        $staffIds = User::whereIn('role', $this->staffRoles)->pluck('id');

        // Add missing members
        foreach ($staffIds as $userId) {
            $this->addMemberToRoom($globalRoom, $userId, $this->getRoleForUser($userId));
        }

        // Remove users who are no longer staff
        ChatRoomMember::where('chat_room_id', $globalRoom->id)
            ->whereNotIn('user_id', $staffIds)
            ->delete();
    }

    /**
     * Sync branch staff to branch room
     */
    public function syncBranchRoomMembers(Branch $branch): void
    {
        $branchRoom = $this->getBranchRoom($branch);

        // Staff in this branch
        $branchStaffIds = User::where('branch_id', $branch->id)
            ->whereIn('role', $this->staffRoles)
            ->pluck('id');

        // Super admins should be in ALL branch rooms
        $superAdminIds = User::where('role', 'super_admin')->pluck('id');
        
        $allMemberIds = $branchStaffIds->merge($superAdminIds)->unique();

        // Sync members
        foreach ($allMemberIds as $userId) {
            $role = $superAdminIds->contains($userId) ? 'admin' : 'member';
            $this->addMemberToRoom($branchRoom, $userId, $role);
        }

        // Remove users who left the branch
        ChatRoomMember::where('chat_room_id', $branchRoom->id)
            ->whereNotIn('user_id', $allMemberIds)
            ->delete();
    }

    /**
     * Sync all rooms for all branches
     */
    public function syncAllRooms(): void
    {
        // Sync global room
        $this->syncGlobalRoomMembers();

        // Sync all branch rooms
        Branch::all()->each(function ($branch) {
            $this->syncBranchRoomMembers($branch);
        });
    }

    /**
     * Add user to staff chat rooms (called when user created/updated)
     */
    public function addUserToRooms(User $user): void
    {
        if (!$this->isStaff($user)) {
            return;
        }

        // Add to global room
        $globalRoom = $this->getGlobalRoom();
        $this->addMemberToRoom($globalRoom, $user->id, $this->getRoleForUser($user->id));

        // Add to branch room if has branch
        if ($user->branch_id) {
            $branch = Branch::find($user->branch_id);
            if ($branch) {
                $branchRoom = $this->getBranchRoom($branch);
                $role = $user->role === 'super_admin' ? 'admin' : 'member';
                $this->addMemberToRoom($branchRoom, $user->id, $role);
            }
        }

        // If super admin, add to ALL branch rooms
        if ($user->role === 'super_admin') {
            Branch::all()->each(function ($branch) use ($user) {
                $branchRoom = $this->getBranchRoom($branch);
                $this->addMemberToRoom($branchRoom, $user->id, 'admin');
            });
        }
    }

    /**
     * Remove user from branch room (when branch changed)
     */
    public function removeUserFromBranchRoom(User $user, int $oldBranchId): void
    {
        $room = ChatRoom::where('type', 'branch')
            ->where('branch_id', $oldBranchId)
            ->first();

        if ($room && $user->role !== 'super_admin') {
            ChatRoomMember::where('chat_room_id', $room->id)
                ->where('user_id', $user->id)
                ->delete();
        }
    }

    /**
     * Remove user from all rooms (when no longer staff)
     */
    public function removeUserFromAllRooms(User $user): void
    {
        ChatRoomMember::where('user_id', $user->id)->delete();
    }

    /**
     * Get all rooms for a user
     */
    public function getRoomsForUser(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        return ChatRoom::forUser($userId)
            ->with(['latestMessage.sender', 'members' => fn($q) => $q->where('user_id', $userId)])
            ->get()
            ->sortByDesc(fn($room) => $room->latestMessage?->created_at ?? $room->created_at);
    }

    /**
     * Send message to room
     */
    public function sendMessage(int $roomId, int $senderId, ?string $message, ?string $attachment = null, ?string $attachmentType = null, ?string $attachmentName = null): ChatMessage
    {
        $chatMessage = ChatMessage::create([
            'chat_room_id' => $roomId,
            'sender_id' => $senderId,
            'message' => $message,
            'attachment' => $attachment,
            'attachment_type' => $attachmentType,
            'attachment_name' => $attachmentName,
            'type' => 'text',
        ]);

        // Update sender's last_read_at
        ChatRoomMember::where('chat_room_id', $roomId)
            ->where('user_id', $senderId)
            ->update(['last_read_at' => now()]);

        return $chatMessage;
    }

    /**
     * Create system message (user joined, etc)
     */
    public function sendSystemMessage(int $roomId, string $message): ChatMessage
    {
        // Use first admin as sender
        $room = ChatRoom::find($roomId);
        $admin = $room->members()->where('role', 'admin')->first();
        $senderId = $admin?->user_id ?? $room->members()->first()?->user_id ?? 1;

        return ChatMessage::create([
            'chat_room_id' => $roomId,
            'sender_id' => $senderId,
            'message' => $message,
            'type' => 'system',
        ]);
    }

    /**
     * Mark room as read for user
     */
    public function markAsRead(int $roomId, int $userId): void
    {
        ChatRoomMember::where('chat_room_id', $roomId)
            ->where('user_id', $userId)
            ->update(['last_read_at' => now()]);
    }

    /**
     * Get total unread count for user across all rooms
     */
    public function getTotalUnreadCount(int $userId): int
    {
        $rooms = ChatRoom::forUser($userId)->get();
        
        return $rooms->sum(fn($room) => $room->getUnreadCountFor($userId));
    }

    // Private helpers

    private function addMemberToRoom(ChatRoom $room, int $userId, string $role = 'member'): void
    {
        ChatRoomMember::firstOrCreate(
            ['chat_room_id' => $room->id, 'user_id' => $userId],
            ['role' => $role, 'joined_at' => now()]
        );
    }

    private function isStaff(User $user): bool
    {
        return in_array($user->role, $this->staffRoles);
    }

    private function getRoleForUser(int $userId): string
    {
        $user = User::find($userId);
        return $user && $user->role === 'super_admin' ? 'admin' : 'member';
    }

    private function getColorForBranch(int $branchId): string
    {
        $colors = [
            '#10b981', // emerald
            '#8b5cf6', // violet
            '#f59e0b', // amber
            '#ef4444', // red
            '#06b6d4', // cyan
            '#ec4899', // pink
        ];

        return $colors[$branchId % count($colors)];
    }
}
