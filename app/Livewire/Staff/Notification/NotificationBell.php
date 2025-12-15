<?php

namespace App\Livewire\Staff\Notification;

use App\Models\StaffNotification;
use Livewire\Component;

class NotificationBell extends Component
{
    public bool $showDropdown = false;
    public int $unreadCount = 0;

    protected $listeners = ['notification-received' => 'refresh', 'refreshNotifications' => 'refresh'];

    public function mount()
    {
        $this->updateCount();
    }

    public function updateCount()
    {
        $this->unreadCount = StaffNotification::forUser(auth()->id())
            ->unread()
            ->count();
    }

    public function toggleDropdown()
    {
        $this->showDropdown = !$this->showDropdown;
    }

    public function openDropdown()
    {
        $this->showDropdown = true;
    }

    public function closeDropdown()
    {
        $this->showDropdown = false;
    }

    public function markAsRead($notificationId)
    {
        $notification = StaffNotification::find($notificationId);
        if ($notification && $notification->notifiable_id === auth()->id()) {
            $notification->markAsRead();
            $this->updateCount();
        }
    }

    public function markAllAsRead()
    {
        StaffNotification::forUser(auth()->id())
            ->unread()
            ->update(['read_at' => now()]);
            
        $this->updateCount();
    }

    public function clickNotification($notificationId)
    {
        $notification = StaffNotification::find($notificationId);
        if ($notification && $notification->notifiable_id === auth()->id()) {
            $notification->markAsClicked();
            
            $this->showDropdown = false;
            
            if ($notification->action_url) {
                return redirect($notification->action_url);
            }
        }
        
        $this->updateCount();
    }

    public function dismissNotification($notificationId)
    {
        $notification = StaffNotification::find($notificationId);
        if ($notification && $notification->notifiable_id === auth()->id()) {
            $notification->delete();
            $this->updateCount();
        }
    }

    public function refresh()
    {
        $this->updateCount();
    }

    public function render()
    {
        $notifications = [];
        
        if ($this->showDropdown) {
            $notifications = StaffNotification::forUser(auth()->id())
                ->recent()
                ->take(10)
                ->get();
        }
        
        return view('livewire.staff.notification.notification-bell', [
            'notifications' => $notifications,
        ]);
    }
}
