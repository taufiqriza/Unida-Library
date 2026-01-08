<?php

namespace App\Livewire\Staff\Settings;

use App\Models\User;
use App\Models\ActivityLog;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class UserRanking extends Component
{
    public $timeRange = '30'; // days
    public $rankings = [];

    public function mount()
    {
        $this->loadRankings();
    }

    public function updatedTimeRange()
    {
        $this->loadRankings();
    }

    public function loadRankings()
    {
        $days = (int) $this->timeRange;
        $startDate = now()->subDays($days);

        // Get user statistics
        $this->rankings = User::select([
            'users.id',
            'users.name',
            'users.email',
            'users.role',
            'users.branch_id',
            'users.last_seen_at',
            'users.created_at'
        ])
        ->selectRaw('
            COUNT(DISTINCT DATE(activity_logs.created_at)) as login_days,
            COUNT(activity_logs.id) as total_activities,
            MAX(activity_logs.created_at) as last_activity,
            TIMESTAMPDIFF(HOUR, users.created_at, NOW()) as account_age_hours
        ')
        ->leftJoin('activity_logs', function($join) use ($startDate) {
            $join->on('users.id', '=', 'activity_logs.user_id')
                 ->where('activity_logs.created_at', '>=', $startDate);
        })
        ->with('branch')
        ->where('users.role', '!=', 'super_admin')
        ->where('users.is_active', true)
        ->groupBy('users.id')
        ->get()
        ->map(function($user) {
            // Calculate activity score
            $loginScore = $user->login_days * 10;
            $activityScore = min($user->total_activities * 2, 200);
            $recentScore = $user->last_activity ? 
                max(0, 50 - now()->diffInDays($user->last_activity) * 2) : 0;
            
            $user->activity_score = $loginScore + $activityScore + $recentScore;
            $user->login_frequency = $user->login_days > 0 ? 
                round($user->total_activities / $user->login_days, 1) : 0;
            
            return $user;
        })
        ->sortByDesc('activity_score')
        ->values();
    }

    public function render()
    {
        return view('livewire.staff.settings.user-ranking');
    }
}
