@extends('staff.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card">
            <div class="flex items-center gap-3">
                <div class="stat-icon gradient-blue text-white">
                    <i class="fas fa-arrow-right-arrow-left"></i>
                </div>
                <div>
                    <div class="stat-value text-blue-600">{{ $stats['loans_today'] }}</div>
                    <div class="stat-label">Pinjam Hari Ini</div>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="flex items-center gap-3">
                <div class="stat-icon gradient-green text-white">
                    <i class="fas fa-rotate-left"></i>
                </div>
                <div>
                    <div class="stat-value text-green-600">{{ $stats['returns_today'] }}</div>
                    <div class="stat-label">Kembali Hari Ini</div>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="flex items-center gap-3">
                <div class="stat-icon gradient-red text-white">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <div class="stat-value text-red-600">{{ $stats['overdue'] }}</div>
                    <div class="stat-label">Terlambat</div>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="flex items-center gap-3">
                <div class="stat-icon gradient-amber text-white">
                    <i class="fas fa-coins"></i>
                </div>
                <div>
                    <div class="stat-value text-amber-600">{{ number_format($stats['unpaid_fines']/1000, 0) }}K</div>
                    <div class="stat-label">Denda Belum Bayar</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="section-card">
        <div class="section-header">
            <i class="fas fa-bolt"></i>
            <span>Aksi Cepat</span>
        </div>
        <div class="p-4 grid grid-cols-3 lg:grid-cols-6 gap-3">
            <button class="quick-action">
                <div class="action-icon gradient-blue"><i class="fas fa-qrcode text-lg"></i></div>
                <span class="action-label">Scan Pinjam</span>
            </button>
            <button class="quick-action">
                <div class="action-icon gradient-green"><i class="fas fa-rotate-left text-lg"></i></div>
                <span class="action-label">Scan Kembali</span>
            </button>
            <button class="quick-action">
                <div class="action-icon gradient-purple"><i class="fas fa-user-plus text-lg"></i></div>
                <span class="action-label">Member Baru</span>
            </button>
            <button class="quick-action">
                <div class="action-icon gradient-amber"><i class="fas fa-search text-lg"></i></div>
                <span class="action-label">Cari Buku</span>
            </button>
            <button class="quick-action">
                <div class="action-icon gradient-indigo"><i class="fas fa-id-card text-lg"></i></div>
                <span class="action-label">Cetak Kartu</span>
            </button>
            <button class="quick-action">
                <div class="action-icon gradient-red"><i class="fas fa-receipt text-lg"></i></div>
                <span class="action-label">Bayar Denda</span>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Recent Loans --}}
        <div class="section-card">
            <div class="section-header">
                <i class="fas fa-clock-rotate-left"></i>
                <span>Transaksi Terbaru</span>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Member</th>
                            <th>Buku</th>
                            <th>Tgl Pinjam</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentLoans as $loan)
                        <tr>
                            <td class="font-medium">{{ Str::limit($loan->member?->name, 15) }}</td>
                            <td>{{ Str::limit($loan->item?->book?->title, 20) }}</td>
                            <td>{{ $loan->loan_date?->format('d/m') }}</td>
                            <td>
                                @if($loan->is_returned)
                                    <span class="badge badge-success">Kembali</span>
                                @elseif($loan->due_date < now())
                                    <span class="badge badge-danger">Terlambat</span>
                                @else
                                    <span class="badge badge-info">Dipinjam</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-slate-400 py-8">Belum ada transaksi</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Overdue Loans --}}
        <div class="section-card">
            <div class="section-header">
                <i class="fas fa-exclamation-triangle text-red-500"></i>
                <span>Peminjaman Terlambat</span>
                @if($stats['overdue'] > 0)
                    <span class="ml-auto badge badge-danger">{{ $stats['overdue'] }}</span>
                @endif
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Member</th>
                            <th>Buku</th>
                            <th>Jatuh Tempo</th>
                            <th>Hari</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($overdueLoans as $loan)
                        <tr>
                            <td class="font-medium">{{ Str::limit($loan->member?->name, 15) }}</td>
                            <td>{{ Str::limit($loan->item?->book?->title, 20) }}</td>
                            <td>{{ $loan->due_date?->format('d/m') }}</td>
                            <td>
                                <span class="badge badge-danger">{{ $loan->due_date?->diffInDays(now()) }} hari</span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-green-500 py-8"><i class="fas fa-check-circle mr-2"></i>Tidak ada yang terlambat</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Collection Stats --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="stat-card">
            <div class="flex items-center gap-3">
                <div class="stat-icon bg-slate-100 text-slate-600">
                    <i class="fas fa-book"></i>
                </div>
                <div>
                    <div class="stat-value text-slate-700">{{ number_format($stats['total_books']) }}</div>
                    <div class="stat-label">Total Judul</div>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="flex items-center gap-3">
                <div class="stat-icon bg-slate-100 text-slate-600">
                    <i class="fas fa-layer-group"></i>
                </div>
                <div>
                    <div class="stat-value text-slate-700">{{ number_format($stats['total_items']) }}</div>
                    <div class="stat-label">Total Eksemplar</div>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="flex items-center gap-3">
                <div class="stat-icon bg-slate-100 text-slate-600">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <div class="stat-value text-slate-700">{{ number_format($stats['total_members']) }}</div>
                    <div class="stat-label">Total Anggota</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
