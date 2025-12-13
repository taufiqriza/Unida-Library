@php
    $stuckJobs = DB::table('jobs')->where('created_at', '<', now()->subMinutes(5))->count();
    $pendingPlagiarism = DB::table('plagiarism_checks')
        ->whereIn('status', ['pending', 'processing'])
        ->where('created_at', '<', now()->subMinutes(10))
        ->count();
    $showAlert = $stuckJobs > 0 || $pendingPlagiarism > 0;
@endphp

@if($showAlert && in_array(auth()->user()?->role, ['super_admin', 'admin']))
<div class="fi-banner bg-amber-50 border-b border-amber-200 px-4 py-3" x-data="{ show: true }" x-show="show">
    <div class="max-w-7xl mx-auto flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="flex-shrink-0 w-8 h-8 bg-amber-100 rounded-full flex items-center justify-center">
                <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-amber-600" />
            </span>
            <div class="text-sm">
                <span class="font-medium text-amber-800">Background Worker Tidak Aktif</span>
                <span class="text-amber-600 ml-2">
                    @if($stuckJobs > 0){{ $stuckJobs }} job tertunda @endif
                    @if($pendingPlagiarism > 0)• {{ $pendingPlagiarism }} cek plagiasi menunggu @endif
                </span>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <code class="text-xs bg-amber-100 px-2 py-1 rounded font-mono text-amber-800">php artisan queue:work</code>
            <button @click="show = false" class="text-amber-400 hover:text-amber-600">
                <x-heroicon-o-x-mark class="w-5 h-5" />
            </button>
        </div>
    </div>
</div>
@endif
