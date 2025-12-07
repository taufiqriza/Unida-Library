<x-filament-panels::page>
    <style>
        .gradient-card {
            border-radius: 12px;
            padding: 0.875rem;
            color: white;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 8px 30px -10px rgba(0,0,0,0.25);
        }
        @media (min-width: 640px) {
            .gradient-card { border-radius: 16px; padding: 1.25rem; }
        }
        .gradient-card:hover { transform: translateY(-3px); }
        .gradient-card::before {
            content: '';
            position: absolute;
            top: -15px;
            right: -15px;
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.15);
            border-radius: 50%;
        }
        @media (min-width: 640px) {
            .gradient-card::before { width: 60px; height: 60px; }
        }
        .gradient-blue { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .gradient-green { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
        .gradient-orange { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .gradient-purple { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .gradient-red { background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%); }
        .gradient-indigo { background: linear-gradient(135deg, #5f72bd 0%, #9b23ea 100%); }
        
        .stat-icon {
            width: 2.25rem; height: 2.25rem;
            background: rgba(255,255,255,0.2);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        @media (min-width: 640px) {
            .stat-icon { width: 2.75rem; height: 2.75rem; border-radius: 10px; }
        }
        .stat-icon svg { width: 1rem; height: 1rem; }
        @media (min-width: 640px) {
            .stat-icon svg { width: 1.25rem; height: 1.25rem; }
        }
        .stat-value { font-size: 1.25rem; font-weight: 800; line-height: 1; }
        @media (min-width: 640px) {
            .stat-value { font-size: 1.5rem; }
        }
        .stat-label { font-size: 0.65rem; opacity: 0.9; margin-top: 0.125rem; }
        @media (min-width: 640px) {
            .stat-label { font-size: 0.75rem; }
        }
        
        .section-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        }
        @media (min-width: 640px) {
            .section-card { border-radius: 16px; }
        }
        .dark .section-card { background: rgb(31 41 55); }
        
        .section-header {
            padding: 0.625rem 0.875rem;
            color: white;
            font-weight: 600;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .section-header svg { width: 1rem; height: 1rem; }
        
        .alert-row {
            padding: 0.625rem 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            transition: all 0.2s ease;
        }
        .dark .alert-row { border-color: rgba(255,255,255,0.05); }
        .alert-row:last-child { border-bottom: none; }
        .alert-row:hover { background: rgba(0,0,0,0.02); }
        .dark .alert-row:hover { background: rgba(255,255,255,0.02); }
        
        .alert-icon {
            width: 2rem; height: 2rem;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .alert-icon svg { width: 1rem; height: 1rem; }
        
        .loan-card {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 1px solid #bae6fd;
        }
        .dark .loan-card {
            background: linear-gradient(135deg, rgba(59,130,246,0.15) 0%, rgba(59,130,246,0.1) 100%);
            border-color: rgba(59,130,246,0.3);
        }
    </style>

    @php
        $branch = $this->getBranch();
        $greeting = $this->getGreeting();
        $main = $this->getMainStats();
        $alerts = $this->getAlertStats();
        $digital = $this->getDigitalStats();
        $chart = $this->getLoanChartData();
        $recentLoans = $this->getRecentLoans();
    @endphp

    {{-- Hero --}}
    <div class="gradient-card gradient-blue mb-2 sm:mb-4">
        <div class="flex items-center gap-2 sm:gap-3">
            <div class="stat-icon"><x-heroicon-o-sparkles class="text-white" /></div>
            <div class="min-w-0">
                <h1 class="text-sm sm:text-lg font-bold truncate">{{ $greeting }}, {{ auth()->user()->name }}!</h1>
                <p class="opacity-90 text-xs truncate">{{ $branch ? $branch->name : 'Semua Cabang' }} • {{ now()->format('d M Y') }}</p>
            </div>
        </div>
    </div>

    {{-- Main Stats - Always 4 columns horizontal --}}
    <div class="flex gap-2 sm:gap-4 mb-2 sm:mb-4">
        <div class="gradient-card gradient-blue flex-1 !p-2 sm:!p-4">
            <div class="flex items-center gap-2 sm:gap-3">
                <div class="stat-icon !w-7 !h-7 sm:!w-11 sm:!h-11"><x-heroicon-o-book-open class="text-white !w-4 !h-4 sm:!w-5 sm:!h-5" /></div>
                <div><div class="stat-value">{{ number_format($main['books']) }}</div><div class="stat-label">Judul</div></div>
            </div>
        </div>
        <div class="gradient-card gradient-green flex-1 !p-2 sm:!p-4">
            <div class="flex items-center gap-2 sm:gap-3">
                <div class="stat-icon !w-7 !h-7 sm:!w-11 sm:!h-11"><x-heroicon-o-archive-box class="text-white !w-4 !h-4 sm:!w-5 sm:!h-5" /></div>
                <div><div class="stat-value">{{ number_format($main['items']) }}</div><div class="stat-label">Eksemplar</div></div>
            </div>
        </div>
        <div class="gradient-card gradient-purple flex-1 !p-2 sm:!p-4">
            <div class="flex items-center gap-2 sm:gap-3">
                <div class="stat-icon !w-7 !h-7 sm:!w-11 sm:!h-11"><x-heroicon-o-users class="text-white !w-4 !h-4 sm:!w-5 sm:!h-5" /></div>
                <div><div class="stat-value">{{ number_format($main['members']) }}</div><div class="stat-label">Anggota</div></div>
            </div>
        </div>
        <div class="gradient-card gradient-orange flex-1 !p-2 sm:!p-4">
            <div class="flex items-center gap-2 sm:gap-3">
                <div class="stat-icon !w-7 !h-7 sm:!w-11 sm:!h-11"><x-heroicon-o-arrow-path class="text-white !w-4 !h-4 sm:!w-5 sm:!h-5" /></div>
                <div><div class="stat-value">{{ number_format($main['active_loans']) }}</div><div class="stat-label">Dipinjam</div></div>
            </div>
        </div>
    </div>

    {{-- Alerts + Digital --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 sm:gap-4 mb-4">
        <div class="section-card">
            <div class="section-header gradient-red"><x-heroicon-o-bell-alert /><span>Perlu Perhatian</span></div>
            <div>
                <a href="{{ route('filament.admin.pages.overdue-loans') }}" class="alert-row">
                    <div class="alert-icon" style="background:#fee2e2;"><x-heroicon-o-clock style="color:#dc2626;" /></div>
                    <div class="flex-1 text-sm text-gray-700 dark:text-gray-200">Terlambat</div>
                    <div class="text-lg font-bold text-red-600 dark:text-red-300">{{ $alerts['overdue'] }}</div>
                </a>
                <a href="{{ route('filament.admin.pages.expired-members') }}" class="alert-row">
                    <div class="alert-icon" style="background:#fef3c7;"><x-heroicon-o-user-minus style="color:#d97706;" /></div>
                    <div class="flex-1 text-sm text-gray-700 dark:text-gray-200">Kadaluarsa</div>
                    <div class="text-lg font-bold text-amber-600 dark:text-amber-300">{{ $alerts['expired_members'] }}</div>
                </a>
                <a href="{{ route('filament.admin.resources.fines.index') }}" class="alert-row">
                    <div class="alert-icon" style="background:#e0e7ff;"><x-heroicon-o-banknotes style="color:#4f46e5;" /></div>
                    <div class="flex-1 text-sm text-gray-700 dark:text-gray-200">Denda</div>
                    <div class="text-lg font-bold text-indigo-600 dark:text-indigo-300">{{ $alerts['unpaid_fines'] }}</div>
                </a>
            </div>
        </div>

        <div class="section-card">
            <div class="section-header gradient-indigo"><x-heroicon-o-globe-alt /><span>Koleksi Digital</span></div>
            <div>
                <a href="{{ route('filament.admin.resources.ebooks.index') }}" class="alert-row">
                    <div class="alert-icon" style="background:#dbeafe;"><x-heroicon-o-document-text style="color:#2563eb;" /></div>
                    <div class="flex-1 text-sm text-gray-700 dark:text-gray-200">E-Book</div>
                    <div class="text-lg font-bold text-blue-600 dark:text-blue-300">{{ $digital['ebooks'] }}</div>
                </a>
                <a href="{{ route('filament.admin.resources.etheses.index') }}" class="alert-row">
                    <div class="alert-icon" style="background:#f3e8ff;"><x-heroicon-o-academic-cap style="color:#9333ea;" /></div>
                    <div class="flex-1 text-sm text-gray-700 dark:text-gray-200">E-Thesis</div>
                    <div class="text-lg font-bold text-purple-600 dark:text-purple-300">{{ $digital['ethesis'] }}</div>
                </a>
                <a href="{{ route('filament.admin.resources.news.index') }}" class="alert-row">
                    <div class="alert-icon" style="background:#d1fae5;"><x-heroicon-o-newspaper style="color:#059669;" /></div>
                    <div class="flex-1 text-sm text-gray-700 dark:text-gray-200">Berita</div>
                    <div class="text-lg font-bold text-emerald-600 dark:text-emerald-300">{{ $digital['news'] }}</div>
                </a>
            </div>
        </div>
    </div>

    {{-- Chart Full Width --}}
    <div class="section-card mb-2 sm:mb-4">
        <div class="section-header gradient-blue"><x-heroicon-o-chart-bar /><span>Sirkulasi 7 Hari Terakhir</span></div>
        <div class="p-2 sm:p-4" style="height: 200px;"><canvas id="loanChart"></canvas></div>
    </div>

    {{-- Recent Loans --}}
    <div class="section-card">
        <div class="section-header gradient-green"><x-heroicon-o-clock /><span>Peminjaman Terbaru</span></div>
        <div class="p-2 sm:p-3 flex flex-wrap gap-2">
            @forelse($recentLoans as $loan)
            <div class="loan-card flex items-center gap-2 p-2 rounded-lg flex-1 min-w-[140px] max-w-[200px]">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold text-xs flex-shrink-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    {{ substr($loan['member']['name'] ?? 'N', 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-xs font-semibold text-gray-800 dark:text-gray-100 truncate">{{ Str::limit($loan['item']['book']['title'] ?? '-', 18) }}</div>
                    <div class="text-xs text-gray-600 dark:text-gray-400 truncate">{{ Str::limit($loan['member']['name'] ?? '-', 12) }}</div>
                </div>
            </div>
            @empty
            <div class="w-full text-center py-6 text-gray-400 text-sm">Belum ada peminjaman</div>
            @endforelse
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            new Chart(document.getElementById('loanChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: @json($chart['labels']),
                    datasets: [
                        { label: 'Pinjam', data: @json($chart['loans']), backgroundColor: 'rgba(99,102,241,0.8)', borderRadius: 6 },
                        { label: 'Kembali', data: @json($chart['returns']), backgroundColor: 'rgba(16,185,129,0.8)', borderRadius: 6 }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, padding: 15 } } },
                    scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
                }
            });
        });
    </script>
</x-filament-panels::page>
