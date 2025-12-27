@extends('reports.statistics.layout')

@section('title', 'Laporan Sirkulasi & Keanggotaan')

@section('content')
<div class="report-title">
    <h1>LAPORAN SIRKULASI & KEANGGOTAAN</h1>
    <p>Periode: {{ $reportPeriod }} | Cabang: {{ $branchName }}</p>
</div>

<div class="meta-box">
    <table class="meta-table">
        <tr>
            <td style="width:33%"><span class="meta-label">Tanggal Cetak:</span><br><span class="meta-value">{{ $generatedAt }}</span></td>
            <td style="width:33%"><span class="meta-label">Dicetak Oleh:</span><br><span class="meta-value">{{ $generatedBy }}</span></td>
            <td style="width:33%"><span class="meta-label">Jenis Laporan:</span><br><span class="meta-value">Sirkulasi & Keanggotaan</span></td>
        </tr>
    </table>
</div>

<div class="section">
    <div class="section-title">INDIKATOR KINERJA SIRKULASI</div>
    <table class="stats-table">
        <tr>
            <td><div class="stat-box"><div class="stat-value">{{ number_format($stats['loans_today'] ?? 0) }}</div><div class="stat-label">Hari Ini</div></div></td>
            <td><div class="stat-box"><div class="stat-value">{{ number_format($stats['loans_this_month'] ?? 0) }}</div><div class="stat-label">Bulan Ini</div></div></td>
            <td><div class="stat-box"><div class="stat-value">{{ number_format($stats['loans_this_year'] ?? 0) }}</div><div class="stat-label">Tahun Ini</div></div></td>
            <td><div class="stat-box"><div class="stat-value" style="color:#dc2626;">{{ number_format($stats['overdue_loans'] ?? 0) }}</div><div class="stat-label">Terlambat</div></div></td>
        </tr>
    </table>
</div>

<table class="two-col">
    <tr>
        <td>
            <div class="section">
                <div class="section-title">STATUS PINJAMAN</div>
                <table class="data-table">
                    <tr><th>Status</th><th>Jumlah</th><th>Ket</th></tr>
                    <tr>
                        <td>Pinjaman Aktif</td>
                        <td class="number">{{ number_format($stats['active_loans'] ?? 0) }}</td>
                        <td><span class="badge badge-primary">Normal</span></td>
                    </tr>
                    <tr>
                        <td>Pinjaman Terlambat</td>
                        <td class="number">{{ number_format($stats['overdue_loans'] ?? 0) }}</td>
                        <td><span class="badge badge-danger">Tindakan</span></td>
                    </tr>
                </table>
            </div>
        </td>
        <td>
            <div class="section">
                <div class="section-title">STATISTIK ANGGOTA</div>
                <table class="data-table">
                    <tr><th>Indikator</th><th>Jumlah</th></tr>
                    <tr>
                        <td>Total Anggota</td>
                        <td class="number">{{ number_format($stats['total_members'] ?? 0) }}</td>
                    </tr>
                    <tr>
                        <td>Anggota Aktif</td>
                        <td class="number">{{ number_format($stats['active_members'] ?? 0) }}</td>
                    </tr>
                    <tr>
                        <td>Baru Bulan Ini</td>
                        <td class="number">+{{ number_format($stats['new_members_month'] ?? 0) }}</td>
                    </tr>
                </table>
            </div>
        </td>
    </tr>
</table>

<div class="section">
    <div class="section-title">STATISTIK DENDA</div>
    <table class="data-table">
        <tr>
            <th style="width:50%">Kategori</th>
            <th style="width:30%">Nominal</th>
            <th style="width:20%">Status</th>
        </tr>
        <tr>
            <td>Total Denda</td>
            <td class="number">Rp {{ number_format($stats['total_fines'] ?? 0) }}</td>
            <td>-</td>
        </tr>
        <tr>
            <td>Denda Sudah Dibayar</td>
            <td class="number">Rp {{ number_format($stats['paid_fines'] ?? 0) }}</td>
            <td><span class="badge badge-success">Lunas</span></td>
        </tr>
        <tr>
            <td>Denda Belum Dibayar</td>
            <td class="number">Rp {{ number_format($stats['unpaid_fines'] ?? 0) }}</td>
            <td><span class="badge badge-danger">Outstanding</span></td>
        </tr>
    </table>
</div>

@if(($stats['unpaid_fines'] ?? 0) > 0)
<div class="alert-box danger">
    <strong>PERHATIAN:</strong> Terdapat denda belum dibayar sebesar <strong>Rp {{ number_format($stats['unpaid_fines'] ?? 0) }}</strong>
</div>
@endif

<div class="section">
    <div class="section-title">TREN SIRKULASI 12 BULAN TERAKHIR</div>
    <table class="data-table">
        <tr>
            <th style="width:25%">Bulan</th>
            <th style="width:25%">Peminjaman</th>
            <th style="width:25%">Pengembalian</th>
            <th style="width:25%">Anggota Baru</th>
        </tr>
        @forelse($monthlyTrend as $trend)
        <tr>
            <td>{{ $trend['month'] }}</td>
            <td class="number">{{ number_format($trend['loans']) }}</td>
            <td class="number">{{ number_format($trend['returns']) }}</td>
            <td class="number">{{ number_format($trend['new_members']) }}</td>
        </tr>
        @empty
        <tr><td colspan="4" style="text-align:center">Tidak ada data</td></tr>
        @endforelse
        @if(count($monthlyTrend) > 0)
        @php
            $totalLoans = array_sum(array_column($monthlyTrend, 'loans'));
            $totalReturns = array_sum(array_column($monthlyTrend, 'returns'));
            $totalNewMembers = array_sum(array_column($monthlyTrend, 'new_members'));
        @endphp
        <tr class="total-row">
            <td><strong>TOTAL</strong></td>
            <td class="number">{{ number_format($totalLoans) }}</td>
            <td class="number">{{ number_format($totalReturns) }}</td>
            <td class="number">{{ number_format($totalNewMembers) }}</td>
        </tr>
        <tr style="background:#f0f9ff;">
            <td>RATA-RATA/BULAN</td>
            <td class="number">{{ number_format(round($totalLoans / 12)) }}</td>
            <td class="number">{{ number_format(round($totalReturns / 12)) }}</td>
            <td class="number">{{ number_format(round($totalNewMembers / 12)) }}</td>
        </tr>
        @endif
    </table>
</div>

@if(count($branchStats) > 0)
<div class="page-break"></div>
<div class="section">
    <div class="section-title">STATISTIK PER CABANG</div>
    <table class="data-table">
        <tr>
            <th style="width:5%">No</th>
            <th style="width:35%">Cabang</th>
            <th style="width:15%">Judul</th>
            <th style="width:15%">Eksemplar</th>
            <th style="width:15%">Anggota</th>
            <th style="width:15%">Pinjam/Bln</th>
        </tr>
        @foreach($branchStats as $i => $branch)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $branch['name'] }}</td>
            <td class="number">{{ number_format($branch['titles']) }}</td>
            <td class="number">{{ number_format($branch['items']) }}</td>
            <td class="number">{{ number_format($branch['members']) }}</td>
            <td class="number">{{ number_format($branch['loans_month']) }}</td>
        </tr>
        @endforeach
        <tr class="total-row">
            <td colspan="2"><strong>TOTAL</strong></td>
            <td class="number">{{ number_format(array_sum(array_column($branchStats, 'titles'))) }}</td>
            <td class="number">{{ number_format(array_sum(array_column($branchStats, 'items'))) }}</td>
            <td class="number">{{ number_format(array_sum(array_column($branchStats, 'members'))) }}</td>
            <td class="number">{{ number_format(array_sum(array_column($branchStats, 'loans_month'))) }}</td>
        </tr>
    </table>
</div>
@endif
@endsection
