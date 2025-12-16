<?php

namespace App\Livewire\Staff\Notification;

use App\Models\StaffNotification;
use App\Services\NotificationService;
use Livewire\Component;
use Livewire\WithPagination;

class NotificationCenter extends Component
{
    use WithPagination;

    public $filter = 'all'; // all, unread, read
    public $category = '';
    public $search = '';
    
    protected $queryString = [
        'filter' => ['except' => 'all'],
        'category' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function setFilter($filter)
    {
        $this->filter = $filter;
        $this->resetPage();
    }

    public function setCategory($category)
    {
        $this->category = $category;
        $this->resetPage();
    }

    public function markAsRead($notificationId)
    {
        $notification = StaffNotification::find($notificationId);
        if ($notification && $notification->notifiable_id === auth()->id()) {
            $notification->markAsRead();
        }
    }

    public function markAllAsRead()
    {
        StaffNotification::forUser(auth()->id())
            ->unread()
            ->update(['read_at' => now()]);
            
        $this->dispatch('refreshNotifications');
    }

    public function deleteNotification($notificationId)
    {
        $notification = StaffNotification::find($notificationId);
        if ($notification && $notification->notifiable_id === auth()->id()) {
            $notification->delete();
        }
    }

    public function deleteAllRead()
    {
        StaffNotification::forUser(auth()->id())
            ->read()
            ->delete();
    }

    public function clickNotification($notificationId)
    {
        $notification = StaffNotification::find($notificationId);
        if ($notification && $notification->notifiable_id === auth()->id()) {
            $notification->markAsClicked();
            
            if ($notification->action_url) {
                return redirect($notification->action_url);
            }
        }
    }

    public function render()
    {
        $user = auth()->user();
        
        $query = StaffNotification::forUser($user->id)->recent();
        
        // Apply filters
        if ($this->filter === 'unread') {
            $query->unread();
        } elseif ($this->filter === 'read') {
            $query->read();
        }
        
        if ($this->category) {
            $query->category($this->category);
        }
        
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('body', 'like', '%' . $this->search . '%');
            });
        }
        
        $notifications = $query->paginate(20);
        
        // Stats
        $stats = [
            'total' => StaffNotification::forUser($user->id)->count(),
            'unread' => StaffNotification::forUser($user->id)->unread()->count(),
            'today' => StaffNotification::forUser($user->id)->whereDate('created_at', today())->count(),
        ];
        
        // Category counts
        $categories = [
            'chat' => ['label' => 'Chat', 'icon' => 'fa-comments', 'color' => 'indigo'],
            'loan' => ['label' => 'Sirkulasi', 'icon' => 'fa-book-reader', 'color' => 'blue'],
            'task' => ['label' => 'Tugas', 'icon' => 'fa-clipboard-list', 'color' => 'violet'],
            'member' => ['label' => 'Anggota', 'icon' => 'fa-user-check', 'color' => 'emerald'],
            'system' => ['label' => 'Sistem', 'icon' => 'fa-cog', 'color' => 'gray'],
            'announcement' => ['label' => 'Pengumuman', 'icon' => 'fa-bullhorn', 'color' => 'amber'],
        ];
        
        return view('livewire.staff.notification.notification-center', [
            'notifications' => $notifications,
            'stats' => $stats,
            'categories' => $categories,
        ])->extends('staff.layouts.app')->section('content');
    }
}
