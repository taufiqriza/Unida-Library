<?php

namespace App\Livewire\Staff\Settings;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserRanking extends Component
{
    public $rankings = [];
    public $timeRange = 30;

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
        $this->rankings = User::select([
            'users.id',
            'users.name',
            'users.email',
            'users.branch_id',
            'branches.name as branch_name',
            DB::raw('COUNT(DISTINCT DATE(activity_logs.created_at)) as login_days'),
            DB::raw('COUNT(activity_logs.id) as total_activities'),
            DB::raw('MAX(activity_logs.created_at) as last_activity')
        ])
        ->leftJoin('activity_logs', 'users.id', '=', 'activity_logs.user_id')
        ->leftJoin('branches', 'users.branch_id', '=', 'branches.id')
        ->where('users.role', '!=', 'super_admin')
        ->where('activity_logs.created_at', '>=', now()->subDays($this->timeRange))
        ->groupBy('users.id', 'users.name', 'users.email', 'users.branch_id', 'branches.name')
        ->orderByDesc('total_activities')
        ->orderByDesc('login_days')
        ->get()
        ->map(function($user) {
            $user->activity_score = ($user->login_days * 10) + min($user->total_activities * 2, 200);
            if ($user->last_activity) {
                $user->activity_score += max(0, 50 - now()->diffInDays($user->last_activity) * 2);
            }
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
