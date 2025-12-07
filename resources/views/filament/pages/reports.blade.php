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
        .gradient-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 50px -10px rgba(0,0,0,0.4);
        }
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
        
        .stat-icon {
            width: 3rem;
            height: 3rem;
            background: rgba(255,255,255,0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .stat-icon svg { width: 1.5rem; height: 1.5rem; }
        .stat-value { font-size: 1.75rem; font-weight: 800; line-height: 1; }
        .stat-label { font-size: 0.875rem; opacity: 0.9; margin-top: 0.25rem; }
        
        .section-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            border: 1px solid rgba(0,0,0,0.05);
        }
        .dark .section-card { background: rgb(31 41 55); border-color: rgba(255,255,255,0.05); }
        
        .section-header {
            padding: 1rem 1.5rem;
            color: white;
            font-weight: 600;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .section-header svg { width: 1.25rem; height: 1.25rem; }
        .header-blue { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .header-green { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
        .header-orange { background: linear-gradient(135deg, #f5af19 0%, #f12711 100%); }
        .header-pink { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
        
        .data-box {
            text-align: center;
            padding: 1.25rem 1rem;
            border-radius: 16px;
            transition: all 0.2s ease;
        }
        .data-box:hover { transform: translateY(-2px); }
        .data-box .value { font-size: 1.5rem; font-weight: 700; line-height: 1; }
        .data-box .label { font-size: 0.75rem; color: #6b7280; margin-top: 0.5rem; font-weight: 500; }
        .dark .data-box .label { color: #9ca3af; }
        
        .period-select {
            background: rgba(255,255,255,0.2);
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 12px;
            padding: 0.5rem 2rem 0.5rem 1rem;
            font-weight: 600;
            color: white;
            cursor: pointer;
            font-size: 0.875rem;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='white'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.5rem center;
            background-size: 1.25rem;
        }
        .period-select:focus { outline: none; border-color: rgba(255,255,255,0.6); }
        .period-select option { color: #374151; background: white; }
    </style>

    {{-- Hero with Period Selector --}}
    <div class="gradient-card gradient-blue mb-8">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-4">
                <div class="stat-icon">
                    <x-heroicon-o-document-chart-bar class="text-white" />
                </div>
                <div>
                    <h1 class="text-2xl font-bold">Ringkasan Laporan</h1>
                    <p class="opacity-90 text-sm">
                        @if($branch = $this->getCurrentBranch())
                            {{ $branch->name }}
                        @else
                            Semua Cabang
                        @endif
                        • Statistik berdasarkan periode
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <x-heroicon-o-calendar class="w-5 h-5 opacity-80" />
                <select wire:model.live="period" class="period-select">
                    <option value="today">Hari Ini</option>
                    <option value="week">Minggu Ini</option>
                    <option value="month">Bulan Ini</option>
                    <option value="year">Tahun Ini</option>
                </select>
            </div>
        </div>
    </div>

    @php
        $collection = $this->getCollectionStats();
        $member = $this->getMemberStats();
        $loan = $this->getLoanStats();
        $fine = $this->getFineStats();
    @endphp

    {{-- Stats Cards --}}
    <div class="flex flex-wrap lg:flex-nowrap gap-6 mb-8">
        <div class="gradient-card gradient-blue flex-1 min-w-[200px]">
            <div class="flex items-center gap-4">
                <div class="stat-icon"><x-heroicon-o-book-open class="text-white" /></div>
                <div>
                    <div class="stat-value">{{ number_format($collection['books']) }}</div>
                    <div class="stat-label">Judul Buku</div>
                </div>
            </div>
        </div>
        <div class="gradient-card gradient-green flex-1 min-w-[200px]">
            <div class="flex items-center gap-4">
                <div class="stat-icon"><x-heroicon-o-archive-box class="text-white" /></div>
                <div>
                    <div class="stat-value">{{ number_format($collection['items']) }}</div>
                    <div class="stat-label">Eksemplar</div>
                </div>
            </div>
        </div>
        <div class="gradient-card gradient-orange flex-1 min-w-[200px]">
            <div class="flex items-center gap-4">
                <div class="stat-icon"><x-heroicon-o-device-tablet class="text-white" /></div>
                <div>
                    <div class="stat-value">{{ number_format($collection['ebooks']) }}</div>
                    <div class="stat-label">E-Book</div>
                </div>
            </div>
        </div>
        <div class="gradient-card gradient-purple flex-1 min-w-[200px]">
            <div class="flex items-center gap-4">
                <div class="stat-icon"><x-heroicon-o-users class="text-white" /></div>
                <div>
                    <div class="stat-value">{{ number_format($member['total']) }}</div>
                    <div class="stat-label">Anggota</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Detail Sections --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Keanggotaan --}}
        <div class="section-card">
            <div class="section-header header-blue">
                <x-heroicon-o-user-group />
                <span>Keanggotaan</span>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-3 gap-4">
                    <div class="data-box" style="background: linear-gradient(135deg, #d1fae5 0%, #ecfdf5 100%);">
                        <div class="value" style="color: #059669;">{{ number_format($member['active']) }}</div>
                        <div class="label">Aktif</div>
                    </div>
                    <div class="data-box" style="background: linear-gradient(135deg, #e0e7ff 0%, #eef2ff 100%);">
                        <div class="value" style="color: #4f46e5;">{{ number_format($member['new']) }}</div>
                        <div class="label">Baru</div>
                    </div>
                    <div class="data-box" style="background: linear-gradient(135deg, #fee2e2 0%, #fef2f2 100%);">
                        <div class="value" style="color: #dc2626;">{{ number_format($member['expired']) }}</div>
                        <div class="label">Kadaluarsa</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sirkulasi --}}
        <div class="section-card">
            <div class="section-header header-green">
                <x-heroicon-o-arrow-path />
                <span>Sirkulasi</span>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-4 gap-3">
                    <div class="data-box" style="background: linear-gradient(135deg, #e0e7ff 0%, #eef2ff 100%);">
                        <div class="value" style="color: #4f46e5;">{{ number_format($loan['total']) }}</div>
                        <div class="label">Pinjam</div>
                    </div>
                    <div class="data-box" style="background: linear-gradient(135deg, #d1fae5 0%, #ecfdf5 100%);">
                        <div class="value" style="color: #059669;">{{ number_format($loan['returned']) }}</div>
                        <div class="label">Kembali</div>
                    </div>
                    <div class="data-box" style="background: linear-gradient(135deg, #fef3c7 0%, #fffbeb 100%);">
                        <div class="value" style="color: #d97706;">{{ number_format($loan['active']) }}</div>
                        <div class="label">Aktif</div>
                    </div>
                    <div class="data-box" style="background: linear-gradient(135deg, #fee2e2 0%, #fef2f2 100%);">
                        <div class="value" style="color: #dc2626;">{{ number_format($loan['overdue']) }}</div>
                        <div class="label">Terlambat</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Denda --}}
        <div class="section-card">
            <div class="section-header header-orange">
                <x-heroicon-o-banknotes />
                <span>Denda</span>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-3 gap-4">
                    <div class="data-box" style="background: linear-gradient(135deg, #f3f4f6 0%, #f9fafb 100%);">
                        <div class="value" style="color: #374151;">Rp {{ number_format($fine['total']/1000, 0) }}K</div>
                        <div class="label">Total</div>
                    </div>
                    <div class="data-box" style="background: linear-gradient(135deg, #d1fae5 0%, #ecfdf5 100%);">
                        <div class="value" style="color: #059669;">Rp {{ number_format($fine['paid']/1000, 0) }}K</div>
                        <div class="label">Terbayar</div>
                    </div>
                    <div class="data-box" style="background: linear-gradient(135deg, #fee2e2 0%, #fef2f2 100%);">
                        <div class="value" style="color: #dc2626;">Rp {{ number_format($fine['unpaid']/1000, 0) }}K</div>
                        <div class="label">Belum Bayar</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Info --}}
        <div class="section-card">
            <div class="section-header header-pink">
                <x-heroicon-o-sparkles />
                <span>Info Cepat</span>
            </div>
            <div class="p-4">
                <table class="w-full">
                    <tbody>
                        <tr class="border-b border-gray-100 dark:border-gray-700">
                            <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-300">Anggota Aktif</td>
                            <td class="py-3 px-4 text-sm font-bold text-right" style="color: #059669;">{{ number_format($member['active']) }} orang</td>
                        </tr>
                        <tr class="border-b border-gray-100 dark:border-gray-700">
                            <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-300">Peminjaman Aktif</td>
                            <td class="py-3 px-4 text-sm font-bold text-right" style="color: #d97706;">{{ number_format($loan['active']) }} buku</td>
                        </tr>
                        <tr class="border-b border-gray-100 dark:border-gray-700">
                            <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-300">Terlambat</td>
                            <td class="py-3 px-4 text-sm font-bold text-right" style="color: #dc2626;">{{ number_format($loan['overdue']) }} buku</td>
                        </tr>
                        <tr>
                            <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-300">Denda Belum Bayar</td>
                            <td class="py-3 px-4 text-sm font-bold text-right" style="color: #dc2626;">Rp {{ number_format($fine['unpaid']) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
