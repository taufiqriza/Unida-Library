<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-gradient-to-br from-red-500 to-rose-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-red-500/30">
                <i class="fas fa-shield-alt text-2xl"></i>
            </div>
            <div>
                <h1 class="text-xl lg:text-2xl font-bold text-gray-900">Security Dashboard</h1>
                <p class="text-sm text-gray-500">Monitor keamanan dan proteksi sistem</p>
            </div>
        </div>
        <div class="flex gap-2">
            <button wire:click="runScan" wire:loading.attr="disabled" class="px-4 py-2 bg-red-600 text-white rounded-xl text-sm font-medium hover:bg-red-700 flex items-center gap-2">
                <i class="fas fa-search" wire:loading.class="fa-spin" wire:target="runScan"></i> Scan Keamanan
            </button>
            <button wire:click="runIntegrityCheck" class="px-4 py-2 bg-amber-600 text-white rounded-xl text-sm font-medium hover:bg-amber-700 flex items-center gap-2">
                <i class="fas fa-file-shield"></i> Cek Integritas
            </button>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-gray-500 text-xs font-medium">Request Diblokir</span>
                <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-ban text-red-500 text-sm"></i>
                </div>
            </div>
            <p class="text-3xl font-black text-gray-900">{{ $stats['blocked_requests'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-gray-500 text-xs font-medium">Rate Limited</span>
                <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-tachometer-alt text-amber-500 text-sm"></i>
                </div>
            </div>
            <p class="text-3xl font-black text-gray-900">{{ $stats['rate_limited'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-gray-500 text-xs font-medium">Honeypot</span>
                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-robot text-purple-500 text-sm"></i>
                </div>
            </div>
            <p class="text-3xl font-black text-gray-900">{{ $stats['honeypot_triggered'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-gray-500 text-xs font-medium">Bot Diblokir</span>
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-spider text-blue-500 text-sm"></i>
                </div>
            </div>
            <p class="text-3xl font-black text-gray-900">{{ $stats['suspicious_agents'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-gray-500 text-xs font-medium">Login Gagal</span>
                <div class="w-8 h-8 bg-rose-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-times text-rose-500 text-sm"></i>
                </div>
            </div>
            <p class="text-3xl font-black text-gray-900">{{ $stats['failed_logins'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-gray-500 text-xs font-medium">Scan Terakhir</span>
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-green-500 text-sm"></i>
                </div>
            </div>
            <p class="text-sm font-bold text-gray-900">{{ $stats['last_scan'] ?? 'Belum pernah' }}</p>
        </div>
    </div>

    {{-- Protection Status --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
            <i class="fas fa-check-circle text-green-500"></i> Status Proteksi
        </h3>
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="flex items-center gap-3 p-3 bg-green-50 rounded-xl">
                <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-filter text-white"></i>
                </div>
                <div>
                    <p class="font-semibold text-gray-900">Content Filter</p>
                    <p class="text-xs text-green-600">Aktif - Blokir konten judi</p>
                </div>
            </div>
            <div class="flex items-center gap-3 p-3 bg-green-50 rounded-xl">
                <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-tachometer-alt text-white"></i>
                </div>
                <div>
                    <p class="font-semibold text-gray-900">Rate Limiting</p>
                    <p class="text-xs text-green-600">Aktif - 60 req/menit</p>
                </div>
            </div>
            <div class="flex items-center gap-3 p-3 bg-green-50 rounded-xl">
                <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-robot text-white"></i>
                </div>
                <div>
                    <p class="font-semibold text-gray-900">Bot Protection</p>
                    <p class="text-xs text-green-600">Aktif - Honeypot & UA filter</p>
                </div>
            </div>
            <div class="flex items-center gap-3 p-3 bg-green-50 rounded-xl">
                <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-lock text-white"></i>
                </div>
                <div>
                    <p class="font-semibold text-gray-900">Security Headers</p>
                    <p class="text-xs text-green-600">Aktif - CSP, XSS, CSRF</p>
                </div>
            </div>
            <div class="flex items-center gap-3 p-3 bg-blue-50 rounded-xl">
                <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-filter text-white"></i>
                </div>
                <div>
                    <p class="font-semibold text-gray-900">Content Filter</p>
                    <p class="text-xs text-blue-600">Aktif - Spam & Profanity Detection</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        {{-- Scan Result --}}
        @if($scanResult)
        <div class="bg-slate-900 rounded-2xl p-6 text-white lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold flex items-center gap-2">
                    <i class="fas fa-terminal text-green-400"></i> Hasil Scan
                </h3>
                <button wire:click="$set('scanResult', '')" class="text-slate-400 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <pre class="text-sm text-green-400 font-mono whitespace-pre-wrap overflow-x-auto max-h-96">{{ $scanResult }}</pre>
        </div>
        @endif

        {{-- Recent Security Logs --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden {{ $scanResult ? '' : 'lg:col-span-2' }}">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-history text-blue-500"></i> Log Keamanan Terbaru
                </h3>
                <button wire:click="loadRecentLogs" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
            <div class="divide-y divide-gray-50 max-h-96 overflow-y-auto">
                @forelse($recentLogs as $log)
                <div class="px-6 py-3 hover:bg-gray-50">
                    <div class="flex items-start gap-3">
                        <span class="px-2 py-0.5 text-xs font-bold rounded {{ $log['level'] === 'WARNING' ? 'bg-amber-100 text-amber-700' : ($log['level'] === 'ERROR' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700') }}">
                            {{ $log['level'] }}
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-900 truncate">{{ $log['message'] }}</p>
                            <p class="text-xs text-gray-500">{{ $log['time'] }}</p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="px-6 py-8 text-center text-gray-400">
                    <i class="fas fa-shield-alt text-4xl mb-2"></i>
                    <p>Tidak ada log keamanan</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
            <i class="fas fa-tools text-gray-500"></i> Aksi Cepat
        </h3>
        <div class="flex flex-wrap gap-3">
            <button wire:click="initializeBaseline" class="px-4 py-2 bg-blue-100 text-blue-700 rounded-xl text-sm font-medium hover:bg-blue-200 flex items-center gap-2">
                <i class="fas fa-database"></i> Buat Baseline Baru
            </button>
            <button wire:click="clearLogs" onclick="return confirm('Hapus semua log?')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-200 flex items-center gap-2">
                <i class="fas fa-trash"></i> Bersihkan Log
            </button>
            <a href="{{ route('staff.analytics.index') }}" class="px-4 py-2 bg-purple-100 text-purple-700 rounded-xl text-sm font-medium hover:bg-purple-200 flex items-center gap-2">
                <i class="fas fa-chart-line"></i> Lihat Analytics
            </a>
        </div>
    </div>

    {{-- Content Filter Stats --}}
    @if(isset($contentStats) && ($contentStats['total_violations'] > 0 || $contentStats['today_violations'] > 0))
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-filter text-blue-500"></i> Content Filter Statistics
            </h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-3 gap-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $contentStats['total_violations'] }}</div>
                    <div class="text-sm text-gray-600">Total Violations</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-orange-600">{{ $contentStats['today_violations'] }}</div>
                    <div class="text-sm text-gray-600">Today</div>
                </div>
                <div class="text-center">
                    <div class="text-sm font-medium text-gray-900">{{ $contentStats['most_common_field'] ?? 'N/A' }}</div>
                    <div class="text-sm text-gray-600">Most Common Field</div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Failed Logins Detail --}}
    @if(count($failedLogins) > 0)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-user-times text-rose-500"></i> Percobaan Login Gagal
                <span class="px-2 py-0.5 bg-rose-100 text-rose-700 text-xs font-bold rounded-full">{{ count($failedLogins) }}</span>
            </h3>
            <button wire:click="loadFailedLogins" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-sync-alt"></i>
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Waktu</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">IP Address</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Email/ID</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Browser</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($failedLogins as $login)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $login['time'] }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs font-mono rounded">{{ $login['ip'] }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ Str::limit($login['identifier'], 30) }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $login['user_agent'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
