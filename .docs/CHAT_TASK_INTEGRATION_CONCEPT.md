# 📋 Chat + Task Integration Concept

## Overview

Integrasi fitur **Attach Task** ke Staff Chat yang memungkinkan staff untuk:
1. Mengirim referensi task dalam pesan chat
2. Membuat task baru langsung dari chat
3. Melihat preview task dalam bubble chat
4. Quick action untuk update status task

---

## 🎯 User Stories

1. **Sebagai Staff**, saya ingin bisa attach task ke pesan chat agar rekan bisa langsung lihat detail dan status task
2. **Sebagai Staff**, saya ingin bisa buat task baru dari percakapan chat tanpa buka halaman Kanban
3. **Sebagai Staff**, saya ingin lihat preview task yang di-attach (judul, status, assignee)
4. **Sebagai Staff**, saya ingin bisa quick update status task langsung dari chat

---

## 📊 Database Schema

### Opsi 1: Reference Only (Recommended - Ringan)

Tidak perlu tabel baru. Simpan task_id di kolom existing:

```php
// chat_messages table - tambah kolom
$table->foreignId('task_id')->nullable()->constrained()->nullOnDelete();
```

### Opsi 2: Polymorphic Attachments (Extensible)

```sql
-- Jika ingin support berbagai jenis attachment di masa depan
CREATE TABLE chat_message_attachments (
    id BIGINT PRIMARY KEY,
    chat_message_id BIGINT,
    attachable_type VARCHAR(50),  -- 'App\Models\Task', 'App\Models\Book', etc
    attachable_id BIGINT,
    created_at TIMESTAMP
);
```

**Rekomendasi: Opsi 1** - Lebih ringan, cukup untuk kebutuhan saat ini.

---

## 🔄 Flow Diagram

### Attach Task ke Chat
```
┌─────────────────────────────────────────────────────────────┐
│  Chat Input                                                 │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  [📷 Image] [📎 File] [📋 Task] [📤 Send]                  │
│                          │                                  │
│                          ▼                                  │
│              ┌─────────────────────┐                        │
│              │   Task Picker Modal │                        │
│              ├─────────────────────┤                        │
│              │ 🔍 Search task...   │                        │
│              ├─────────────────────┤                        │
│              │ ○ Task #123 - Fix.. │                        │
│              │ ○ Task #124 - Add.. │                        │
│              │ ○ Task #125 - Bug.. │                        │
│              ├─────────────────────┤                        │
│              │ [+ Buat Task Baru]  │                        │
│              │ [Cancel] [Attach]   │                        │
│              └─────────────────────┘                        │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

### Task Card dalam Chat Bubble
```
┌─────────────────────────────────────────────────────────────┐
│  Ahmad: Ini task yang perlu dikerjakan                      │
│                                                             │
│  ┌─────────────────────────────────────────────────────┐    │
│  │ 📋 TASK-123                              🔵 To Do   │    │
│  ├─────────────────────────────────────────────────────┤    │
│  │ Input Buku Baru Fakultas Kedokteran                 │    │
│  │                                                     │    │
│  │ 👤 Assigned: Budi Pustakawan                        │    │
│  │ 📅 Due: 20 Des 2024                                 │    │
│  │ 🏷️ Priority: High                                   │    │
│  ├─────────────────────────────────────────────────────┤    │
│  │ [👁️ Lihat] [▶️ Mulai] [✓ Selesai]                  │    │
│  └─────────────────────────────────────────────────────┘    │
│                                                   14:32 ✓✓  │
└─────────────────────────────────────────────────────────────┘
```

---

## 💻 Implementation Plan

### Phase 1: Basic Task Reference (Simple)

**Effort: 2-3 jam**

1. **Migration**: Tambah `task_id` ke `chat_messages`
2. **Model**: Update ChatMessage relationship
3. **View**: Task picker modal + task card preview
4. **Component**: Method untuk attach task

### Phase 2: Quick Task Creation (Optional)

**Effort: 2-3 jam**

1. Modal form untuk buat task dari chat
2. Auto-assign ke receiver (jika direct chat)
3. Auto-add link ke chat message

### Phase 3: Quick Actions (Optional)

**Effort: 1-2 jam**

1. Button untuk update status dari chat
2. Real-time update ke Kanban

---

## 📁 Files to Modify/Create

### Migration
```php
// database/migrations/xxxx_add_task_id_to_chat_messages.php
Schema::table('chat_messages', function (Blueprint $table) {
    $table->foreignId('task_id')->nullable()->after('attachment_name')
          ->constrained()->nullOnDelete();
});
```

### Model Update
```php
// app/Models/ChatMessage.php
public function task()
{
    return $this->belongsTo(Task::class);
}

public function hasTask(): bool
{
    return !empty($this->task_id);
}
```

### Livewire Component Update
```php
// app/Livewire/Staff/Chat/StaffChat.php

public $showTaskPicker = false;
public $selectedTaskId = null;
public $taskSearch = '';

public function openTaskPicker()
{
    $this->showTaskPicker = true;
    $this->taskSearch = '';
}

public function attachTask($taskId)
{
    $this->selectedTaskId = $taskId;
    $this->showTaskPicker = false;
}

public function sendMessage()
{
    // ... existing code ...
    
    $chatMessage = ChatMessage::create([
        // ... existing fields ...
        'task_id' => $this->selectedTaskId,
    ]);
    
    $this->selectedTaskId = null;
}

public function getAvailableTasksProperty()
{
    return Task::query()
        ->where(function($q) {
            $q->where('reporter_id', auth()->id())
              ->orWhere('assignee_id', auth()->id());
        })
        ->when($this->taskSearch, fn($q) => 
            $q->where('title', 'like', "%{$this->taskSearch}%")
        )
        ->latest()
        ->take(10)
        ->get();
}
```

### View Components

```blade
{{-- Task Picker Modal --}}
@if($showTaskPicker)
<div class="fixed inset-0 bg-black/50 z-50 flex items-end justify-center">
    <div class="bg-white rounded-t-2xl w-full max-w-md max-h-[60vh] overflow-hidden">
        <div class="p-4 border-b">
            <div class="flex justify-between items-center">
                <h3 class="font-semibold">Pilih Task</h3>
                <button wire:click="$set('showTaskPicker', false)">✕</button>
            </div>
            <input wire:model.live="taskSearch" placeholder="Cari task..." 
                   class="mt-2 w-full px-3 py-2 border rounded-lg">
        </div>
        <div class="max-h-80 overflow-y-auto">
            @foreach($this->availableTasks as $task)
            <button wire:click="attachTask({{ $task->id }})" 
                    class="w-full p-3 flex items-center gap-3 hover:bg-gray-50 border-b">
                <span class="px-2 py-1 rounded text-xs {{ $task->status_color }}">
                    {{ $task->status_label }}
                </span>
                <div class="flex-1 text-left">
                    <p class="font-medium text-sm">{{ $task->title }}</p>
                    <p class="text-xs text-gray-500">{{ $task->assignee?->name }}</p>
                </div>
            </button>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- Task Card in Chat Bubble --}}
@if($msg['task'])
<div class="mt-2 p-3 bg-gray-50 rounded-lg border">
    <div class="flex items-center justify-between mb-2">
        <span class="text-xs font-mono text-gray-500">TASK-{{ $msg['task']['id'] }}</span>
        <span class="px-2 py-0.5 rounded text-xs {{ $msg['task']['status_color'] }}">
            {{ $msg['task']['status_label'] }}
        </span>
    </div>
    <p class="font-medium text-sm">{{ $msg['task']['title'] }}</p>
    <div class="mt-2 flex items-center gap-3 text-xs text-gray-500">
        <span>👤 {{ $msg['task']['assignee_name'] }}</span>
        @if($msg['task']['due_date'])
        <span>📅 {{ $msg['task']['due_date'] }}</span>
        @endif
    </div>
    <div class="mt-2 flex gap-2">
        <a href="{{ route('staff.tasks.show', $msg['task']['id']) }}" 
           class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs">
            👁️ Lihat
        </a>
    </div>
</div>
@endif
```

---

## ⚡ Performance Considerations

### 1. Lazy Loading Task Details
```php
// Hanya load task saat diperlukan
$messages = ChatMessage::with(['sender', 'task:id,title,status,assignee_id'])
    ->where('chat_room_id', $roomId)
    ->get();
```

### 2. Task Picker - Limit Results
```php
// Tampilkan hanya 10 task terakhir yang relevan
->take(10)
```

### 3. Cache Task Data
```php
// Cache task yang sering diakses
Cache::remember("task:{$taskId}", 300, fn() => Task::find($taskId));
```

### 4. No Extra Polling
- Task status update via Kanban tetap di Kanban
- Chat hanya menampilkan snapshot saat send
- Refresh task card hanya saat user klik "Lihat"

---

## 🎨 UI/UX Guidelines

### Task Button di Input Area
- Icon: 📋 atau fa-tasks
- Position: Sebelum tombol Send
- Tooltip: "Attach Task"

### Task Card Style
- Background: `bg-gray-50` atau `bg-slate-50`
- Border: `border border-gray-200`
- Status badge dengan warna sesuai Kanban
- Compact view - tidak terlalu besar

### Task Picker Modal
- Slide up dari bawah (mobile-friendly)
- Search dengan debounce 300ms
- Grouped by status (opsional)
- Recent tasks first

---

## ✅ Acceptance Criteria

### Phase 1 (MVP)
- [ ] User bisa klik tombol Task di input area
- [ ] Modal picker muncul dengan list task
- [ ] User bisa search task
- [ ] Task terpilih ditampilkan sebagai card di chat
- [ ] Card menampilkan: title, status, assignee
- [ ] Tombol "Lihat" redirect ke detail task

### Phase 2 (Enhancement)
- [ ] Quick create task dari chat
- [ ] Auto-assign ke chat partner
- [ ] Notification ke assignee

### Phase 3 (Advanced)
- [ ] Quick status update dari chat
- [ ] Task card auto-refresh status
- [ ] @mention task dengan typing "#123"

---

## 🚀 Quick Start Implementation

Untuk implementasi cepat Phase 1:

```bash
# 1. Create migration
php artisan make:migration add_task_id_to_chat_messages_table

# 2. Run migration
php artisan migrate

# 3. Update model, component, dan view
# (files listed above)

# 4. Test
- Buka chat
- Klik tombol Task
- Pilih task
- Send message
- Verify card muncul
```

---

## 📅 Timeline Estimate

| Phase | Effort | Priority |
|-------|--------|----------|
| Phase 1: Basic Reference | 3 jam | High |
| Phase 2: Quick Create | 3 jam | Medium |
| Phase 3: Quick Actions | 2 jam | Low |

**Total: ~8 jam untuk full implementation**

---

**Mau langsung implementasi Phase 1?** 🚀
