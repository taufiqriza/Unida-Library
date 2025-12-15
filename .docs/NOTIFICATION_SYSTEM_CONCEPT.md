# 🔔 Notification & Reminder System
## UNIDA Library - Konsep Arsitektur & Implementasi

---

## 1. 📋 Overview

Sistem notifikasi terintegrasi yang menghubungkan semua modul perpustakaan dengan multi-channel delivery (In-App, Email, WhatsApp, Browser Push), mendukung real-time dan scheduled notifications.

---

## 2. 🏗️ Arsitektur Sistem

```
┌─────────────────────────────────────────────────────────────────────┐
│                         EVENT SOURCES                                │
├─────────────┬─────────────┬─────────────┬─────────────┬─────────────┤
│   Loan      │    Task     │   Member    │  Biblio     │   System    │
│  Events     │   Events    │   Events    │  Events     │   Events    │
└──────┬──────┴──────┬──────┴──────┬──────┴──────┬──────┴──────┬──────┘
       │             │             │             │             │
       └─────────────┴─────────────┼─────────────┴─────────────┘
                                   │
                                   ▼
┌─────────────────────────────────────────────────────────────────────┐
│                    NOTIFICATION SERVICE                              │
│  ┌───────────────┐  ┌───────────────┐  ┌───────────────────────┐   │
│  │ Event         │  │ Preference    │  │ Template              │   │
│  │ Listener      │──│ Manager       │──│ Engine                │   │
│  └───────────────┘  └───────────────┘  └───────────────────────┘   │
│                            │                                         │
│                            ▼                                         │
│  ┌───────────────────────────────────────────────────────────────┐  │
│  │                    QUEUE DISPATCHER                            │  │
│  │   (Laravel Horizon / Redis Queue / Database Queue)            │  │
│  └───────────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────────┘
                                   │
       ┌───────────────┬───────────┼───────────┬───────────────┐
       ▼               ▼           ▼           ▼               ▼
┌─────────────┐ ┌─────────────┐ ┌─────────────┐ ┌─────────────┐
│   In-App    │ │    Email    │ │  WhatsApp   │ │ Browser     │
│   Channel   │ │   Channel   │ │  Channel    │ │ Push        │
│ (Database)  │ │ (SMTP/SES)  │ │ (Baileys)   │ │ (Web Push)  │
└─────────────┘ └─────────────┘ └─────────────┘ └─────────────┘
```

---

## 3. 📊 Database Schema

### 3.1 notifications (Laravel Default + Extended)
```sql
CREATE TABLE notifications (
    id CHAR(36) PRIMARY KEY,
    type VARCHAR(255) NOT NULL,                    -- Class notification
    notifiable_type VARCHAR(255) NOT NULL,         -- User, Member, etc
    notifiable_id BIGINT UNSIGNED NOT NULL,
    
    -- Extended fields
    category ENUM('loan', 'task', 'member', 'system', 'announcement') NOT NULL,
    priority ENUM('low', 'normal', 'high', 'urgent') DEFAULT 'normal',
    title VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    action_url VARCHAR(500),
    action_label VARCHAR(100),
    icon VARCHAR(50),
    color VARCHAR(20),
    image_url VARCHAR(500),
    
    -- Metadata
    data JSON,
    
    -- Status tracking
    read_at TIMESTAMP NULL,
    clicked_at TIMESTAMP NULL,
    dismissed_at TIMESTAMP NULL,
    
    -- Delivery tracking
    channels_sent JSON,        -- ["database", "email", "whatsapp"]
    channels_delivered JSON,   -- {"email": "2024-01-01 10:00:00", ...}
    channels_failed JSON,      -- {"whatsapp": "number not found"}
    
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    INDEX idx_notifiable (notifiable_type, notifiable_id),
    INDEX idx_category (category),
    INDEX idx_read_at (read_at),
    INDEX idx_created_at (created_at)
);
```

### 3.2 notification_preferences
```sql
CREATE TABLE notification_preferences (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,           -- For staff
    member_id BIGINT UNSIGNED NULL,             -- For members
    
    -- Channel preferences
    channel_database BOOLEAN DEFAULT TRUE,
    channel_email BOOLEAN DEFAULT TRUE,
    channel_whatsapp BOOLEAN DEFAULT FALSE,
    channel_push BOOLEAN DEFAULT FALSE,
    
    -- Category preferences (JSON for flexibility)
    categories JSON,
    /* Example:
    {
        "loan": {"enabled": true, "channels": ["database", "email", "whatsapp"]},
        "task": {"enabled": true, "channels": ["database", "push"]},
        "announcement": {"enabled": true, "channels": ["database", "email"]}
    }
    */
    
    -- Quiet hours
    quiet_hours_enabled BOOLEAN DEFAULT FALSE,
    quiet_hours_start TIME,                      -- e.g., 22:00
    quiet_hours_end TIME,                        -- e.g., 07:00
    
    -- Digest mode
    digest_mode ENUM('instant', 'hourly', 'daily', 'weekly') DEFAULT 'instant',
    digest_time TIME DEFAULT '08:00:00',
    
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    UNIQUE KEY unique_user (user_id),
    UNIQUE KEY unique_member (member_id)
);
```

### 3.3 notification_templates
```sql
CREATE TABLE notification_templates (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(100) UNIQUE NOT NULL,           -- 'loan.due_reminder'
    name VARCHAR(255) NOT NULL,
    category ENUM('loan', 'task', 'member', 'system', 'announcement'),
    
    -- Templates per channel
    title_template VARCHAR(500) NOT NULL,
    body_template TEXT NOT NULL,
    email_subject VARCHAR(255),
    email_template TEXT,
    whatsapp_template TEXT,
    
    -- Placeholders documentation
    available_placeholders JSON,
    /* Example:
    ["member_name", "book_title", "due_date", "days_remaining", "fine_amount"]
    */
    
    -- Settings
    is_active BOOLEAN DEFAULT TRUE,
    default_priority ENUM('low', 'normal', 'high', 'urgent') DEFAULT 'normal',
    default_channels JSON DEFAULT '["database"]',
    
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### 3.4 notification_schedules (Scheduled/Reminder)
```sql
CREATE TABLE notification_schedules (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    
    -- Target
    notifiable_type VARCHAR(255) NOT NULL,
    notifiable_id BIGINT UNSIGNED NOT NULL,
    
    -- Template & Data
    template_code VARCHAR(100) NOT NULL,
    data JSON,
    
    -- Schedule
    scheduled_at TIMESTAMP NOT NULL,
    timezone VARCHAR(50) DEFAULT 'Asia/Jakarta',
    
    -- Recurrence (optional)
    is_recurring BOOLEAN DEFAULT FALSE,
    recurrence_rule VARCHAR(255),               -- RRULE format
    recurrence_end_at TIMESTAMP NULL,
    last_sent_at TIMESTAMP NULL,
    next_run_at TIMESTAMP NULL,
    
    -- Status
    status ENUM('pending', 'sent', 'failed', 'cancelled') DEFAULT 'pending',
    attempts INT DEFAULT 0,
    last_error TEXT,
    
    -- Reference
    reference_type VARCHAR(255),                 -- 'Loan', 'Task', etc
    reference_id BIGINT UNSIGNED,
    
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    INDEX idx_scheduled_at (scheduled_at),
    INDEX idx_status (status),
    INDEX idx_reference (reference_type, reference_id)
);
```

### 3.5 notification_logs (Audit Trail)
```sql
CREATE TABLE notification_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    notification_id CHAR(36),
    channel VARCHAR(50) NOT NULL,
    
    -- Delivery info
    recipient VARCHAR(255),                      -- email, phone number
    status ENUM('queued', 'sent', 'delivered', 'failed', 'bounced'),
    
    -- Response
    provider_response JSON,
    error_message TEXT,
    
    -- Timing
    queued_at TIMESTAMP,
    sent_at TIMESTAMP,
    delivered_at TIMESTAMP,
    
    created_at TIMESTAMP,
    
    INDEX idx_notification (notification_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);
```

---

## 4. 📨 Notification Types & Templates

### 4.1 Loan Notifications

| Code | Trigger | Channels | Priority |
|------|---------|----------|----------|
| `loan.borrowed` | Saat pinjam | DB, Email | Normal |
| `loan.due_reminder_7d` | H-7 jatuh tempo | DB, Email | Normal |
| `loan.due_reminder_3d` | H-3 jatuh tempo | DB, Email, WA | Normal |
| `loan.due_reminder_1d` | H-1 jatuh tempo | DB, Email, WA, Push | High |
| `loan.due_today` | Hari H jatuh tempo | DB, Email, WA, Push | Urgent |
| `loan.overdue` | Lewat jatuh tempo | DB, Email, WA | Urgent |
| `loan.overdue_weekly` | Update mingguan | DB, Email | High |
| `loan.returned` | Dikembalikan | DB | Low |
| `loan.extended` | Diperpanjang | DB, Email | Normal |
| `loan.fine_reminder` | Denda menunggak | DB, Email, WA | High |
| `loan.reservation_available` | Reservasi tersedia | DB, Email, WA, Push | High |

### 4.2 Task Notifications (Staff)

| Code | Trigger | Channels | Priority |
|------|---------|----------|----------|
| `task.assigned` | Ditugaskan ke staf | DB, Push | Normal |
| `task.due_reminder` | H-1 deadline | DB, Push | High |
| `task.overdue` | Lewat deadline | DB, Push | Urgent |
| `task.comment_added` | Komentar baru | DB | Normal |
| `task.status_changed` | Status berubah | DB | Low |
| `task.mentioned` | Di-mention | DB, Push | Normal |

### 4.3 Member Notifications

| Code | Trigger | Channels | Priority |
|------|---------|----------|----------|
| `member.welcome` | Registrasi baru | DB, Email | Normal |
| `member.approved` | Keanggotaan disetujui | DB, Email, WA | Normal |
| `member.expiring` | H-30 kadaluarsa | DB, Email | Normal |
| `member.expired` | Keanggotaan habis | DB, Email, WA | High |
| `member.renewed` | Perpanjang member | DB, Email | Normal |

### 4.4 System/Announcement

| Code | Trigger | Channels | Priority |
|------|---------|----------|----------|
| `system.maintenance` | Jadwal maintenance | DB, Email | High |
| `announcement.general` | Pengumuman umum | DB, Email | Normal |
| `announcement.event` | Event perpustakaan | DB, Email | Normal |
| `announcement.holiday` | Hari libur | DB, Email | Normal |

---

## 5. 🔧 Service Classes

### 5.1 NotificationService (Main Orchestrator)
```php
class NotificationService
{
    public function send(
        $notifiable,
        string $templateCode,
        array $data = [],
        array $channels = null,
        string $priority = null
    ): Notification;
    
    public function schedule(
        $notifiable,
        string $templateCode,
        Carbon $scheduledAt,
        array $data = [],
        ?string $referenceType = null,
        ?int $referenceId = null
    ): NotificationSchedule;
    
    public function cancelScheduled(
        string $referenceType,
        int $referenceId
    ): int;
    
    public function markAsRead(string $notificationId): void;
    public function markAllAsRead($notifiable): void;
    
    public function getUnreadCount($notifiable): int;
    public function getNotifications($notifiable, int $limit = 20): Collection;
}
```

### 5.2 Channel Drivers

```php
interface NotificationChannelInterface
{
    public function send($notifiable, NotificationPayload $payload): DeliveryResult;
    public function isAvailable($notifiable): bool;
    public function getRecipient($notifiable): ?string;
}

class DatabaseChannel implements NotificationChannelInterface { }
class EmailChannel implements NotificationChannelInterface { }
class WhatsAppChannel implements NotificationChannelInterface { }
class WebPushChannel implements NotificationChannelInterface { }
```

---

## 6. 📱 UI Components

### 6.1 Staff Portal - Notification Bell
- Real-time badge counter
- Dropdown dengan list notifikasi
- Grouped by date (Hari Ini, Kemarin, Minggu Ini)
- Mark as read/unread
- Link to full notification center

### 6.2 Staff Portal - Notification Center Page
- Filter by category, read status
- Bulk actions (mark read, delete)
- Search notifications
- Date range filter

### 6.3 Staff Portal - Notification Settings
- Toggle per channel (Email, WhatsApp, Push)
- Toggle per category
- Quiet hours setting
- Digest mode preference

### 6.4 OPAC - Member Notifications
- Bell icon di header
- Simple dropdown list
- Push notification permission request

---

## 7. ⏰ Scheduled Jobs

### 7.1 Reminder Scheduler (Daily at 06:00)
```php
// app/Console/Commands/ScheduleLoanReminders.php
class ScheduleLoanReminders extends Command
{
    public function handle()
    {
        // H-7 reminders
        Loan::dueIn(7)->each(fn($loan) => 
            NotificationService::schedule($loan->member, 'loan.due_reminder_7d', now()->setTime(8, 0), [
                'book_title' => $loan->item->book->title,
                'due_date' => $loan->due_date->format('d M Y'),
            ], 'Loan', $loan->id)
        );
        
        // H-3 reminders
        // H-1 reminders
        // H-0 (today) reminders
        // Overdue notifications
    }
}
```

### 7.2 Notification Dispatcher (Every Minute)
```php
// Process scheduled notifications
class DispatchScheduledNotifications extends Command
{
    public function handle()
    {
        NotificationSchedule::where('status', 'pending')
            ->where('scheduled_at', '<=', now())
            ->chunk(100, fn($schedules) => 
                $schedules->each->dispatch()
            );
    }
}
```

### 7.3 Digest Sender (Daily at configured time)
```php
// Send digest emails for users with digest mode
class SendNotificationDigests extends Command
{
    public function handle()
    {
        // Collect unread notifications
        // Group by user
        // Send digest email
    }
}
```

---

## 8. 🔌 Integration Points

### 8.1 Model Observers
```php
// LoanObserver
class LoanObserver
{
    public function created(Loan $loan)
    {
        NotificationService::send($loan->member, 'loan.borrowed', [
            'book_title' => $loan->item->book->title,
            'due_date' => $loan->due_date->format('d M Y'),
        ]);
        
        // Schedule reminders
        NotificationService::schedule($loan->member, 'loan.due_reminder_3d', 
            $loan->due_date->subDays(3)->setTime(8, 0), [...], 'Loan', $loan->id);
    }
    
    public function updated(Loan $loan)
    {
        if ($loan->wasChanged('returned_at') && $loan->returned_at) {
            // Cancel scheduled reminders
            NotificationService::cancelScheduled('Loan', $loan->id);
            
            // Send return confirmation
            NotificationService::send($loan->member, 'loan.returned', [...]);
        }
    }
}
```

### 8.2 Task Events
```php
// TaskObserver
class TaskObserver
{
    public function updated(Task $task)
    {
        if ($task->wasChanged('assigned_to') && $task->assigned_to) {
            NotificationService::send($task->assignee, 'task.assigned', [
                'task_title' => $task->title,
                'reporter' => $task->reporter->name,
            ]);
        }
    }
}
```

---

## 9. 📲 WhatsApp Integration (Baileys)

### Option A: Node.js Microservice
```
Laravel App <--HTTP--> Node.js Baileys Server <--WebSocket--> WhatsApp
```

### Option B: PHP WhatsApp API (Vendor)
- Fonnte.com (Indonesian provider)
- Wablas.com
- Ruangwa.id

### Message Format Templates
```
📚 *UNIDA Library*
━━━━━━━━━━━━━━
Halo {member_name},

Buku "{book_title}" akan jatuh tempo pada *{due_date}*.

📍 Kembalikan di perpustakaan {branch_name}.

Balas dengan:
• PERPANJANG - untuk perpanjang pinjaman
• INFO - untuk info pinjaman lainnya

Terima kasih 🙏
```

---

## 10. 🚀 Implementation Phases

### Phase 1: Foundation (Week 1-2)
- [ ] Database migrations
- [ ] Notification models
- [ ] NotificationService class
- [ ] Database channel
- [ ] Notification bell UI (Staff Portal)

### Phase 2: Email Integration (Week 2-3)
- [ ] Email channel driver
- [ ] Email templates (Blade)
- [ ] Template management
- [ ] Preference settings UI

### Phase 3: Loan Reminders (Week 3-4)
- [ ] Loan observers
- [ ] Scheduled reminder jobs
- [ ] Due date notifications
- [ ] Overdue notifications

### Phase 4: Task Notifications (Week 4)
- [ ] Task observers
- [ ] Real-time with Laravel Echo (optional)
- [ ] Task notification preferences

### Phase 5: WhatsApp (Week 5-6)
- [ ] WhatsApp channel driver
- [ ] Provider integration
- [ ] Message templates
- [ ] Opt-in flow

### Phase 6: Browser Push (Week 6-7)
- [ ] Service Worker
- [ ] Push subscription
- [ ] Push notifications
- [ ] OPAC integration

---

## 11. 📁 File Structure

```
app/
├── Notifications/
│   ├── Channels/
│   │   ├── DatabaseChannel.php
│   │   ├── EmailChannel.php
│   │   ├── WhatsAppChannel.php
│   │   └── WebPushChannel.php
│   ├── Loan/
│   │   ├── LoanBorrowedNotification.php
│   │   ├── LoanDueReminderNotification.php
│   │   └── LoanOverdueNotification.php
│   ├── Task/
│   │   ├── TaskAssignedNotification.php
│   │   └── TaskCommentNotification.php
│   └── Member/
│       ├── MemberWelcomeNotification.php
│       └── MemberExpiringNotification.php
├── Services/
│   ├── NotificationService.php
│   ├── NotificationTemplateService.php
│   └── NotificationPreferenceService.php
├── Observers/
│   ├── LoanObserver.php
│   ├── TaskObserver.php
│   └── MemberObserver.php
├── Console/Commands/
│   ├── ScheduleLoanReminders.php
│   ├── DispatchScheduledNotifications.php
│   └── SendNotificationDigests.php
└── Livewire/Staff/
    ├── Notification/
    │   ├── NotificationBell.php
    │   ├── NotificationCenter.php
    │   └── NotificationSettings.php
```

---

## 12. 🎯 Success Metrics

| Metric | Target |
|--------|--------|
| Notification delivery rate | > 99% |
| Email open rate | > 40% |
| WhatsApp read rate | > 80% |
| Reduced overdue books | -30% |
| Member satisfaction | +20% |
| Staff task completion | +15% |

---

*Document Version: 1.0*
*Last Updated: 2024-12-16*
