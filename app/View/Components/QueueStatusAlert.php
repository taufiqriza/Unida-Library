<?php

namespace App\View\Components;

use Illuminate\Support\Facades\DB;
use Illuminate\View\Component;

class QueueStatusAlert extends Component
{
    public bool $showAlert = false;
    public int $stuckJobs = 0;
    public int $pendingPlagiarism = 0;

    public function __construct()
    {
        // Only show for super_admin
        if (!auth()->check() || auth()->user()->role !== 'super_admin') {
            return;
        }

        // Check for jobs stuck more than 5 minutes (indicates worker not running)
        $this->stuckJobs = DB::table('jobs')
            ->where('created_at', '<', now()->subMinutes(5))
            ->count();

        // Check pending plagiarism checks
        $this->pendingPlagiarism = DB::table('plagiarism_checks')
            ->whereIn('status', ['pending', 'processing'])
            ->where('created_at', '<', now()->subMinutes(10))
            ->count();

        $this->showAlert = $this->stuckJobs > 0 || $this->pendingPlagiarism > 0;
    }

    public function render()
    {
        return view('components.queue-status-alert');
    }
}
