# 💬 Staff Chat System - Group Chat Concept
## Upgrade dari Direct Message ke Group Chat + Auto Groups

---

## 📋 Executive Summary

**Current State:** Chat hanya mendukung Direct Message (1-to-1)
**Target State:** Support Direct Message + Group Chat dengan Auto Groups

### Auto Groups yang Dibuat:
1. **📢 Semua Staff** - Group untuk seluruh staff di semua cabang
2. **🏢 [Nama Cabang]** - Group per cabang, otomatis terisi member cabang tersebut
3. **👥 Custom Groups** - User bisa buat group sendiri

---

## 1. 🗃️ Database Schema

### Tables yang Diperlukan

```sql
-- ============================================
-- 1. CHAT ROOMS (Conversations)
-- ============================================
CREATE TABLE chat_rooms (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    
    -- Room Type
    type ENUM('direct', 'group', 'branch', 'global') DEFAULT 'direct',
    -- direct: 1-to-1 private chat
    -- group: custom group created by users
    -- branch: auto-created group for branch (1 per branch)
    -- global: single group for all staff
    
    -- Room Info (for groups)
    name VARCHAR(255) NULL,              -- "Tim Sirkulasi", "Perpustakaan Pusat"
    description TEXT NULL,
    icon VARCHAR(50) NULL,                -- fa-users, fa-building, fa-globe
    color VARCHAR(20) NULL,               -- hex color for avatar
    avatar VARCHAR(255) NULL,             -- custom group avatar
    
    -- References
    branch_id BIGINT UNSIGNED NULL,       -- for type='branch'
    created_by BIGINT UNSIGNED NULL,      -- for type='group'
    
    -- Settings
    is_readonly BOOLEAN DEFAULT FALSE,    -- only admins can post
    is_archived BOOLEAN DEFAULT FALSE,
    allow_members_add BOOLEAN DEFAULT TRUE,
    
    -- Metadata
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX (type),
    INDEX (branch_id),
    UNIQUE KEY unique_branch_room (branch_id, type) -- 1 room per branch
);

-- ============================================
-- 2. CHAT ROOM MEMBERS
-- ============================================
CREATE TABLE chat_room_members (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    chat_room_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    
    -- Member Role in Room
    role ENUM('admin', 'moderator', 'member') DEFAULT 'member',
    
    -- Notifications
    is_muted BOOLEAN DEFAULT FALSE,
    muted_until TIMESTAMP NULL,
    
    -- Tracking
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_read_at TIMESTAMP NULL,          -- for unread count
    last_read_message_id BIGINT UNSIGNED NULL,
    
    FOREIGN KEY (chat_room_id) REFERENCES chat_rooms(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_member (chat_room_id, user_id),
    INDEX (user_id)
);

-- ============================================
-- 3. CHAT MESSAGES (Upgraded from staff_messages)
-- ============================================
CREATE TABLE chat_messages (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    chat_room_id BIGINT UNSIGNED NOT NULL,
    sender_id BIGINT UNSIGNED NOT NULL,
    
    -- Message Content
    message TEXT NULL,
    
    -- Reply
    reply_to_id BIGINT UNSIGNED NULL,     -- for threaded replies
    
    -- Attachments
    attachment VARCHAR(255) NULL,
    attachment_type VARCHAR(20) NULL,      -- image, file, audio, video
    attachment_name VARCHAR(255) NULL,     -- original filename
    attachment_size INT UNSIGNED NULL,     -- in bytes
    
    -- Message Type
    type ENUM('text', 'system', 'announcement') DEFAULT 'text',
    -- text: regular message
    -- system: "User joined", "Room created"
    -- announcement: pinned/important messages
    
    -- Status
    is_edited BOOLEAN DEFAULT FALSE,
    edited_at TIMESTAMP NULL,
    is_deleted BOOLEAN DEFAULT FALSE,
    deleted_at TIMESTAMP NULL,
    
    -- Metadata
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (chat_room_id) REFERENCES chat_rooms(id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (reply_to_id) REFERENCES chat_messages(id) ON DELETE SET NULL,
    
    INDEX (chat_room_id, created_at),
    INDEX (sender_id)
);

-- ============================================
-- 4. MESSAGE READ RECEIPTS (Optional, for detailed tracking)
-- ============================================
CREATE TABLE chat_message_reads (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    message_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    read_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (message_id) REFERENCES chat_messages(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_read (message_id, user_id)
);
```

---

## 2. 🔄 Auto Group Creation Logic

### A. Global Group (Semua Staff)

```php
// app/Services/ChatService.php

class ChatService
{
    /**
     * Get or create the global "All Staff" group
     */
    public function getGlobalRoom(): ChatRoom
    {
        return ChatRoom::firstOrCreate(
            ['type' => 'global'],
            [
                'name' => '📢 Semua Staff',
                'description' => 'Group untuk seluruh staff perpustakaan',
                'icon' => 'fa-globe',
                'color' => '#3b82f6', // Blue
                'is_readonly' => false,
            ]
        );
    }
    
    /**
     * Sync all staff to global room
     * Should be called via Observer when user is created/updated
     */
    public function syncGlobalRoomMembers(): void
    {
        $globalRoom = $this->getGlobalRoom();
        
        $staffIds = User::whereIn('role', ['super_admin', 'admin', 'librarian', 'staff'])
            ->pluck('id');
        
        // Add missing members
        foreach ($staffIds as $userId) {
            ChatRoomMember::firstOrCreate([
                'chat_room_id' => $globalRoom->id,
                'user_id' => $userId,
            ], [
                'role' => 'member',
                'joined_at' => now(),
            ]);
        }
        
        // Remove users who are no longer staff
        ChatRoomMember::where('chat_room_id', $globalRoom->id)
            ->whereNotIn('user_id', $staffIds)
            ->delete();
    }
}
```

### B. Branch Group (Per Cabang)

```php
/**
 * Get or create branch-specific group
 */
public function getBranchRoom(Branch $branch): ChatRoom
{
    return ChatRoom::firstOrCreate(
        ['type' => 'branch', 'branch_id' => $branch->id],
        [
            'name' => "🏢 {$branch->name}",
            'description' => "Group khusus staff {$branch->name}",
            'icon' => 'fa-building',
            'color' => $this->getColorForBranch($branch->id),
        ]
    );
}

/**
 * Sync branch staff to branch room
 */
public function syncBranchRoomMembers(Branch $branch): void
{
    $branchRoom = $this->getBranchRoom($branch);
    
    $branchStaffIds = User::where('branch_id', $branch->id)
        ->whereIn('role', ['super_admin', 'admin', 'librarian', 'staff'])
        ->pluck('id');
    
    // Super admins should be in ALL branch rooms
    $superAdminIds = User::where('role', 'super_admin')->pluck('id');
    $allMemberIds = $branchStaffIds->merge($superAdminIds)->unique();
    
    // Sync members
    foreach ($allMemberIds as $userId) {
        $role = $superAdminIds->contains($userId) ? 'admin' : 'member';
        
        ChatRoomMember::updateOrCreate(
            [
                'chat_room_id' => $branchRoom->id,
                'user_id' => $userId,
            ],
            [
                'role' => $role,
                'joined_at' => now(),
            ]
        );
    }
    
    // Remove users who left the branch
    ChatRoomMember::where('chat_room_id', $branchRoom->id)
        ->whereNotIn('user_id', $allMemberIds)
        ->delete();
}
```

### C. User Observer untuk Auto Sync

```php
// app/Observers/UserObserver.php

class UserObserver
{
    protected ChatService $chatService;
    
    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }
    
    public function created(User $user): void
    {
        // Add to global room if staff
        if ($this->isStaff($user)) {
            $this->addToGlobalRoom($user);
            
            if ($user->branch_id) {
                $this->addToBranchRoom($user);
            }
        }
    }
    
    public function updated(User $user): void
    {
        // If branch changed, update room memberships
        if ($user->wasChanged('branch_id')) {
            // Remove from old branch room
            $oldBranchId = $user->getOriginal('branch_id');
            if ($oldBranchId) {
                $this->removeFromBranchRoom($user, $oldBranchId);
            }
            
            // Add to new branch room
            if ($user->branch_id) {
                $this->addToBranchRoom($user);
            }
        }
        
        // If role changed
        if ($user->wasChanged('role')) {
            if ($this->isStaff($user)) {
                $this->addToGlobalRoom($user);
                if ($user->branch_id) {
                    $this->addToBranchRoom($user);
                }
            } else {
                $this->removeFromAllRooms($user);
            }
        }
    }
    
    public function deleted(User $user): void
    {
        // Cascade delete handles this via foreign keys
    }
    
    private function isStaff(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'admin', 'librarian', 'staff']);
    }
}
```

---

## 3. 📱 UI Concept

### Chat List View

```
┌─────────────────────────────────────────────────────────────┐
│  💬 Staff Chat                              [+ Grup Baru]   │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  [🔍 Cari percakapan...]                                   │
│                                                             │
│  ─────────── GRUP ───────────                               │
│                                                             │
│  ┌─────────────────────────────────────────────────────┐    │
│  │ 🌐  📢 Semua Staff                          14:32   │    │
│  │     Ahmad: Rapat besok jam 10 ya...        ● 3 new  │    │
│  └─────────────────────────────────────────────────────┘    │
│                                                             │
│  ┌─────────────────────────────────────────────────────┐    │
│  │ 🏢  Perpustakaan Pusat                      13:15   │    │
│  │     Budi: Buku sudah diproses                       │    │
│  └─────────────────────────────────────────────────────┘    │
│                                                             │
│  ┌─────────────────────────────────────────────────────┐    │
│  │ 🏢  Fak. Kedokteran                         12:00   │    │
│  │     Citra: Stok opname selesai                      │    │
│  └─────────────────────────────────────────────────────┘    │
│                                                             │
│  ─────────── PERSONAL ───────────                           │
│                                                             │
│  ┌─────────────────────────────────────────────────────┐    │
│  │ 👤  Dewi Pustakawati                        11:45   │    │
│  │     Kamu: Oke, nanti saya follow up         ● 1 new │    │
│  └─────────────────────────────────────────────────────┘    │
│                                                             │
│  ┌─────────────────────────────────────────────────────┐    │
│  │ 👤  Eko Admin                               kemarin  │    │
│  │     Eko: Terima kasih                               │    │
│  └─────────────────────────────────────────────────────┘    │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

### Group Chat View

```
┌─────────────────────────────────────────────────────────────┐
│  [←]  🌐 Semua Staff                    [👥 12] [⚙️]       │
│       12 anggota • Online: 5                               │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  ┌─────────────────────────────────────────────────────┐    │
│  │  📌 PENGUMUMAN                                      │    │
│  │  Rapat koordinasi bulanan: Senin, 18 Des 2024       │    │
│  │  Jam 10:00 WIB di Ruang Meeting                     │    │
│  └─────────────────────────────────────────────────────┘    │
│                                                             │
│  ─── Hari Ini ───                                           │
│                                                             │
│      [👤] Ahmad (Pustakawan - Pusat)             14:32     │
│      │ Reminder untuk semua: jangan lupa input            │
│      │ statistik pengunjung harian ya 📊                  │
│                                                             │
│                          [👤] Budi (Admin - FK)            │
│                          14:35                              │
│                          Siap pak! Sudah diinput ✅ │       │
│                                                             │
│      [👤] Citra (Librarian - FKIP)               14:40     │
│      │ @Ahmad untuk FK berapa target bulan ini?           │
│                                                             │
│      [👤] Ahmad (Pustakawan - Pusat)             14:42     │
│      │ @Citra target 500 pengunjung/bulan                 │
│      │                                                      │
│      │ [📎 target_2024.pdf]                                │
│                                                             │
├─────────────────────────────────────────────────────────────┤
│  ┌───────────────────────────────────────────┐ [📎] [📷]  │
│  │ Ketik pesan...                            │            │
│  └───────────────────────────────────────────┘ [➤ Kirim]  │
└─────────────────────────────────────────────────────────────┘
```

---

## 4. 🛠️ Implementation Plan

### Phase 1: Database Migration
```php
// database/migrations/2025_12_16_create_chat_rooms_table.php

Schema::create('chat_rooms', function (Blueprint $table) {
    $table->id();
    $table->enum('type', ['direct', 'group', 'branch', 'global'])->default('direct');
    $table->string('name')->nullable();
    $table->text('description')->nullable();
    $table->string('icon', 50)->nullable();
    $table->string('color', 20)->nullable();
    $table->string('avatar')->nullable();
    $table->foreignId('branch_id')->nullable()->constrained()->cascadeOnDelete();
    $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
    $table->boolean('is_readonly')->default(false);
    $table->boolean('is_archived')->default(false);
    $table->timestamps();
    
    $table->unique(['branch_id', 'type']);
    $table->index('type');
});

Schema::create('chat_room_members', function (Blueprint $table) {
    $table->id();
    $table->foreignId('chat_room_id')->constrained()->cascadeOnDelete();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->enum('role', ['admin', 'moderator', 'member'])->default('member');
    $table->boolean('is_muted')->default(false);
    $table->timestamp('muted_until')->nullable();
    $table->timestamp('joined_at')->useCurrent();
    $table->timestamp('last_read_at')->nullable();
    $table->foreignId('last_read_message_id')->nullable();
    $table->timestamps();
    
    $table->unique(['chat_room_id', 'user_id']);
    $table->index('user_id');
});

Schema::create('chat_messages', function (Blueprint $table) {
    $table->id();
    $table->foreignId('chat_room_id')->constrained()->cascadeOnDelete();
    $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
    $table->text('message')->nullable();
    $table->foreignId('reply_to_id')->nullable();
    $table->string('attachment')->nullable();
    $table->string('attachment_type', 20)->nullable();
    $table->string('attachment_name')->nullable();
    $table->unsignedInteger('attachment_size')->nullable();
    $table->enum('type', ['text', 'system', 'announcement'])->default('text');
    $table->boolean('is_edited')->default(false);
    $table->timestamp('edited_at')->nullable();
    $table->boolean('is_deleted')->default(false);
    $table->timestamp('deleted_at')->nullable();
    $table->timestamps();
    
    $table->index(['chat_room_id', 'created_at']);
    $table->index('sender_id');
});
```

### Phase 2: Seeder for Auto Groups

```php
// database/seeders/ChatRoomSeeder.php

class ChatRoomSeeder extends Seeder
{
    public function run(): void
    {
        $chatService = app(ChatService::class);
        
        // 1. Create Global Room
        $globalRoom = $chatService->getGlobalRoom();
        
        // 2. Create Branch Rooms
        Branch::all()->each(function ($branch) use ($chatService) {
            $chatService->getBranchRoom($branch);
            $chatService->syncBranchRoomMembers($branch);
        });
        
        // 3. Sync Global Room Members
        $chatService->syncGlobalRoomMembers();
        
        // 4. Create welcome messages
        ChatMessage::create([
            'chat_room_id' => $globalRoom->id,
            'sender_id' => User::where('role', 'super_admin')->first()->id,
            'message' => '👋 Selamat datang di Group Chat Staff! Gunakan group ini untuk koordinasi dan pengumuman.',
            'type' => 'announcement',
        ]);
    }
}
```

### Phase 3: Artisan Command

```php
// app/Console/Commands/SyncChatRooms.php

class SyncChatRooms extends Command
{
    protected $signature = 'chat:sync-rooms';
    protected $description = 'Sync auto groups (global, branch) with current staff';
    
    public function handle(ChatService $chatService): void
    {
        $this->info('Syncing Global Room...');
        $chatService->syncGlobalRoomMembers();
        
        $this->info('Syncing Branch Rooms...');
        Branch::all()->each(function ($branch) use ($chatService) {
            $chatService->syncBranchRoomMembers($branch);
            $this->line(" - {$branch->name}");
        });
        
        $this->info('Done!');
    }
}
```

---

## 5. 📊 Models

### ChatRoom Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatRoom extends Model
{
    protected $fillable = [
        'type', 'name', 'description', 'icon', 'color', 'avatar',
        'branch_id', 'created_by', 'is_readonly', 'is_archived'
    ];
    
    // Relationships
    public function branch() { return $this->belongsTo(Branch::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function members() { return $this->hasMany(ChatRoomMember::class); }
    public function users() { return $this->belongsToMany(User::class, 'chat_room_members'); }
    public function messages() { return $this->hasMany(ChatMessage::class); }
    
    // Scopes
    public function scopeForUser($query, $userId)
    {
        return $query->whereHas('members', fn($q) => $q->where('user_id', $userId));
    }
    
    public function scopeGroups($query)
    {
        return $query->whereIn('type', ['group', 'branch', 'global']);
    }
    
    // Helpers
    public function isGlobal(): bool { return $this->type === 'global'; }
    public function isBranch(): bool { return $this->type === 'branch'; }
    public function isDirect(): bool { return $this->type === 'direct'; }
    public function isGroup(): bool { return in_array($this->type, ['group', 'branch', 'global']); }
    
    public function getDisplayNameAttribute(): string
    {
        if ($this->isDirect()) {
            // Return other user's name for direct chats
            $member = $this->members()->where('user_id', '!=', auth()->id())->first();
            return $member?->user?->name ?? 'Unknown';
        }
        return $this->name;
    }
    
    public function getIconClassAttribute(): string
    {
        return match($this->type) {
            'global' => 'fa-globe',
            'branch' => 'fa-building',
            'group' => 'fa-users',
            default => 'fa-user',
        };
    }
    
    public function getColorClassAttribute(): string
    {
        return match($this->type) {
            'global' => 'bg-blue-500',
            'branch' => 'bg-green-500',
            'group' => 'bg-purple-500',
            default => 'bg-gray-500',
        };
    }
    
    public function getUnreadCountFor(int $userId): int
    {
        $member = $this->members()->where('user_id', $userId)->first();
        if (!$member || !$member->last_read_at) {
            return $this->messages()->count();
        }
        
        return $this->messages()
            ->where('sender_id', '!=', $userId)
            ->where('created_at', '>', $member->last_read_at)
            ->count();
    }
}
```

---

## 6. ⚡ Real-time Updates

### With Laravel Echo + Pusher

```javascript
// Join room channel when Opening chat
Echo.private(`chat-room.${roomId}`)
    .listen('NewChatMessage', (e) => {
        // Add message to UI
        this.messages.push(e.message);
        this.scrollToBottom();
    })
    .listen('MessageRead', (e) => {
        // Update read receipts
    });
```

### Alternative: Livewire Polling

```php
// In Livewire component
wire:poll.5s="refreshMessages"
```

---

## 7. ✅ Summary

### Features yang Akan Ada:

| Feature | Direct | Branch Group | Global Group | Custom Group |
|---------|--------|--------------|--------------|--------------|
| Auto Create | ❌ | ✅ | ✅ | ❌ |
| Auto Membership | ❌ | ✅ | ✅ | ❌ |
| Send Message | ✅ | ✅ | ✅ | ✅ |
| Attachments | ✅ | ✅ | ✅ | ✅ |
| Reply/Thread | ✅ | ✅ | ✅ | ✅ |
| Read Receipts | ✅ | ✅ | ✅ | ✅ |
| Mute | ✅ | ✅ | ✅ | ✅ |
| Announcement | ❌ | ✅ | ✅ | ✅ |
| Admin Only Post | ❌ | ⚙️ | ⚙️ | ⚙️ |

### Auto Group Rules:
1. **Global "Semua Staff"**
   - Auto-create saat pertama kali diakses
   - Semua staff otomatis jadi member
   - Super admin = room admin

2. **Branch Groups**
   - Auto-create per branch
   - Staff cabang = member
   - Super admin = admin di semua branch room
   - Admin cabang = moderator

3. **Sync Otomatis**
   - User baru → auto join global + branch room
   - User pindah cabang → leave old, join new
   - User deleted → cascade remove dari semua room

---

*Document Created: December 2024*
*Ready for Implementation*
