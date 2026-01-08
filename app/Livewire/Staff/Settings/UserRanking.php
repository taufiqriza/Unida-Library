<?php

namespace App\Livewire\Staff\Settings;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserRanking extends Component
{
    public $rankings = [];
    public $timeRange = 30;
    public $totalStats = [];

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
        // Get all users except super_admin with their activity data
        $this->rankings = User::select([
            'users.id',
            'users.name',
            'users.email',
            'users.role',
            'users.branch_id',
            'users.last_seen_at',
            'branches.name as branch_name',
            DB::raw('COUNT(DISTINCT DATE(activity_logs.created_at)) as login_days'),
            DB::raw('COUNT(activity_logs.id) as total_activities'),
            DB::raw('MAX(activity_logs.created_at) as last_activity'),
            DB::raw('COUNT(DISTINCT activity_logs.module) as modules_used'),
            DB::raw('SUM(CASE WHEN activity_logs.action = "login" THEN 1 ELSE 0 END) as login_count')
        ])
        ->leftJoin('activity_logs', function($join) {
            $join->on('users.id', '=', 'activity_logs.user_id')
                 ->where('activity_logs.created_at', '>=', now()->subDays($this->timeRange));
        })
        ->leftJoin('branches', 'users.branch_id', '=', 'branches.id')
        ->where('users.role', '!=', 'super_admin')
        ->groupBy('users.id', 'users.name', 'users.email', 'users.role', 'users.branch_id', 'users.last_seen_at', 'branches.name')
        ->get()
        ->map(function($user) {
            // Calculate comprehensive activity score
            $loginScore = $user->login_days * 15; // 15 points per login day
            $activityScore = min($user->total_activities * 3, 300); // 3 points per activity, max 300
            $moduleScore = $user->modules_used * 5; // 5 points per unique module used
            $recencyScore = $user->last_activity ? max(0, 100 - now()->diffInDays($user->last_activity) * 3) : 0;
            
            $user->activity_score = $loginScore + $activityScore + $moduleScore + $recencyScore;
            $user->role_label = User::getRoles()[$user->role] ?? $user->role;
            
            return $user;
        })
        ->sortByDesc('activity_score')
        ->values();

        // Calculate summary stats
        $this->totalStats = [
            'total_users' => $this->rankings->count(),
            'total_login_days' => $this->rankings->sum('login_days'),
            'total_activities' => $this->rankings->sum('total_activities'),
            'avg_score' => $this->rankings->avg('activity_score'),
            'active_users' => $this->rankings->where('total_activities', '>', 0)->count()
        ];
    }

    public function render()
    {
        return view('livewire.staff.settings.user-ranking');
    }
}
