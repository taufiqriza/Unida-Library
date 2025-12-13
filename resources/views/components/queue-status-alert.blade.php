@if($showAlert)
<div class="bg-amber-50 border-l-4 border-amber-400 p-4 mb-4 rounded-r-lg" x-data="{ show: true }" x-show="show">
    <div class="flex items-start gap-3">
        <div class="flex-shrink-0">
            <i class="fas fa-exclamation-triangle text-amber-500 text-lg"></i>
        </div>
        <div class="flex-1">
            <h3 class="text-sm font-semibold text-amber-800">
                <i class="fas fa-server mr-1"></i> Perhatian: Background Worker
            </h3>
            <div class="mt-1 text-sm text-amber-700">
                @if($stuckJobs > 0)
                <p>
                    <i class="fas fa-clock mr-1"></i>
                    Ada <strong>{{ $stuckJobs }} tugas</strong> yang tertunda lebih dari 5 menit.
                </p>
                @endif
                @if($pendingPlagiarism > 0)
                <p>
                    <i class="fas fa-shield-alt mr-1"></i>
                    Ada <strong>{{ $pendingPlagiarism }} cek plagiasi</strong> yang belum diproses.
                </p>
                @endif
                <p class="mt-2 text-amber-600">
                    Pastikan queue worker sedang berjalan:
                </p>
                <code class="block mt-1 bg-amber-100 px-2 py-1 rounded text-xs font-mono">
                    php artisan queue:work --daemon
                </code>
            </div>
        </div>
        <button @click="show = false" class="text-amber-400 hover:text-amber-600">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endif
