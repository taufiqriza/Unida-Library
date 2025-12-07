<x-filament-panels::page>
    <style>
        .gradient-card {
            border-radius: 20px;
            padding: 1.5rem;
            color: white;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 10px 40px -10px rgba(0,0,0,0.3);
        }
        .gradient-card:hover { transform: translateY(-5px); box-shadow: 0 20px 50px -10px rgba(0,0,0,0.4); }
        .gradient-card::before {
            content: '';
            position: absolute;
            top: -20px;
            right: -20px;
            width: 80px;
            height: 80px;
            background: rgba(255,255,255,0.15);
            border-radius: 50%;
        }
        .gradient-blue { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .gradient-green { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
        .gradient-orange { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .gradient-purple { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .gradient-pink { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
        .gradient-teal { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
        .stat-icon {
            width: 3rem; height: 3rem;
            background: rgba(255,255,255,0.2);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
        }
        .stat-icon svg { width: 1.5rem; height: 1.5rem; }
        .stat-value { font-size: 1.75rem; font-weight: 800; line-height: 1; }
        .stat-label { font-size: 0.875rem; opacity: 0.9; margin-top: 0.25rem; }
    </style>

    {{-- Hero --}}
    <div class="gradient-card gradient-blue mb-6">
        <div class="flex items-center gap-4">
            <div class="stat-icon">
                <x-heroicon-o-chart-bar class="text-white" />
            </div>
            <div>
                <h1 class="text-2xl font-bold">Statistik E-Library</h1>
                <p class="opacity-90 text-sm">E-Book & E-Thesis</p>
            </div>
        </div>
    </div>

    {{-- E-Book Stats --}}
    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">E-Book</h3>
    <div class="flex flex-wrap lg:flex-nowrap gap-4 mb-8">
        <div class="gradient-card gradient-blue flex-1 min-w-[180px]">
            <div class="flex items-center gap-4">
                <div class="stat-icon"><x-heroicon-o-document-text class="text-white" /></div>
                <div>
                    <div class="stat-value">{{ $this->getTotalEbooks() }}</div>
                    <div class="stat-label">Total E-Book</div>
                </div>
            </div>
        </div>
        <div class="gradient-card gradient-green flex-1 min-w-[180px]">
            <div class="flex items-center gap-4">
                <div class="stat-icon"><x-heroicon-o-arrow-down-tray class="text-white" /></div>
                <div>
                    <div class="stat-value">{{ $this->getTotalDownloads() }}</div>
                    <div class="stat-label">Total Download</div>
                </div>
            </div>
        </div>
        <div class="gradient-card gradient-orange flex-1 min-w-[180px]">
            <div class="flex items-center gap-4">
                <div class="stat-icon"><x-heroicon-o-calendar class="text-white" /></div>
                <div>
                    <div class="stat-value">{{ $this->getThisMonthDownloads() }}</div>
                    <div class="stat-label">Bulan Ini</div>
                </div>
            </div>
        </div>
        <div class="gradient-card gradient-purple flex-1 min-w-[180px]">
            <div class="flex items-center gap-4">
                <div class="stat-icon"><x-heroicon-o-document-duplicate class="text-white" /></div>
                <div>
                    <div class="stat-value">{{ $this->getPopularFormat() }}</div>
                    <div class="stat-label">Format Populer</div>
                </div>
            </div>
        </div>
    </div>

    {{-- E-Thesis Stats --}}
    @php $ethesisType = $this->getEthesisByType(); @endphp
    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">E-Thesis</h3>
    <div class="flex flex-wrap lg:flex-nowrap gap-4 mb-8">
        <div class="gradient-card gradient-pink flex-1 min-w-[180px]">
            <div class="flex items-center gap-4">
                <div class="stat-icon"><x-heroicon-o-academic-cap class="text-white" /></div>
                <div>
                    <div class="stat-value">{{ $this->getTotalEthesis() }}</div>
                    <div class="stat-label">Total Thesis</div>
                </div>
            </div>
        </div>
        <div class="gradient-card gradient-teal flex-1 min-w-[180px]">
            <div class="flex items-center gap-4">
                <div class="stat-icon"><x-heroicon-o-document-check class="text-white" /></div>
                <div>
                    <div class="stat-value">{{ $ethesisType['skripsi'] }}</div>
                    <div class="stat-label">Skripsi</div>
                </div>
            </div>
        </div>
        <div class="gradient-card" style="background: linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%);">
            <div class="flex items-center gap-4">
                <div class="stat-icon"><x-heroicon-o-document-magnifying-glass class="text-white" /></div>
                <div>
                    <div class="stat-value">{{ $ethesisType['tesis'] }}</div>
                    <div class="stat-label">Tesis</div>
                </div>
            </div>
        </div>
        <div class="gradient-card" style="background: linear-gradient(135deg, #5f72bd 0%, #9b23ea 100%);">
            <div class="flex items-center gap-4">
                <div class="stat-icon"><x-heroicon-o-trophy class="text-white" /></div>
                <div>
                    <div class="stat-value">{{ $ethesisType['disertasi'] }}</div>
                    <div class="stat-label">Disertasi</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Top Downloads Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <h3 class="text-lg font-semibold text-white">E-Book Terpopuler</h3>
            <p class="text-sm text-white/70">Berdasarkan jumlah download</p>
        </div>
        <div class="p-4">
            {{ $this->table }}
        </div>
    </div>
</x-filament-panels::page>
