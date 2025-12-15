<?php

namespace App\Livewire\Staff\Notification;

use App\Models\NotificationPreference;
use Livewire\Component;

class NotificationSettings extends Component
{
    public $channelDatabase = true;
    public $channelEmail = true;
    public $channelWhatsapp = false;
    public $channelPush = false;
    
    public $quietHoursEnabled = false;
    public $quietHoursStart = '22:00';
    public $quietHoursEnd = '07:00';
    
    public $digestMode = 'instant';

    public function mount()
    {
        $preference = NotificationPreference::getOrCreateForUser(auth()->id());
        
        $this->channelDatabase = $preference->channel_database;
        $this->channelEmail = $preference->channel_email;
        $this->channelWhatsapp = $preference->channel_whatsapp;
        $this->channelPush = $preference->channel_push;
        $this->quietHoursEnabled = $preference->quiet_hours_enabled;
        $this->quietHoursStart = $preference->quiet_hours_start?->format('H:i') ?? '22:00';
        $this->quietHoursEnd = $preference->quiet_hours_end?->format('H:i') ?? '07:00';
        $this->digestMode = $preference->digest_mode;
    }

    public function save()
    {
        $preference = NotificationPreference::getOrCreateForUser(auth()->id());
        
        $preference->update([
            'channel_database' => (bool) $this->channelDatabase,
            'channel_email' => (bool) $this->channelEmail,
            'channel_whatsapp' => (bool) $this->channelWhatsapp,
            'channel_push' => (bool) $this->channelPush,
            'quiet_hours_enabled' => (bool) $this->quietHoursEnabled,
            'quiet_hours_start' => $this->quietHoursStart ?: '22:00',
            'quiet_hours_end' => $this->quietHoursEnd ?: '07:00',
            'digest_mode' => $this->digestMode ?: 'instant',
        ]);
        
        session()->flash('success', 'Pengaturan notifikasi berhasil disimpan!');
    }

    public function render()
    {
        return view('livewire.staff.notification.notification-settings')
            ->extends('staff.layouts.app')
            ->section('content');
    }
}
