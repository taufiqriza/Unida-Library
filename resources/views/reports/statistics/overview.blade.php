@extends('reports.statistics.layout')

@section('title', 'Laporan Ringkasan Statistik')

@section('content')
<div class="report-title">
    <h1>LAPORAN RINGKASAN STATISTIK PERPUSTAKAAN</h1>
    <p>Periode: {{ $reportPeriod }} | Cabang: {{ $branchName }}</p>
</div>

<div class="meta-box">
    <table class="meta-table">
        <tr>
            <td style="width:33%"><span class="meta-label">Tanggal Cetak:</span><br><span class="meta-value">{{ $generatedAt }}</span></td>
            <td style="width:33%"><span class="meta-label">Dicetak Oleh:</span><br><span class="meta-value">{{ $generatedBy }}</span></td>
            <td style="width:33%"><span class="meta-label">Jenis Laporan:</span><br><span class="meta-value">Ringkasan Umum</span></td>
        </tr>
    </table>
</div>

<div class="section">
    <div class="section-title">INDIKATOR KINERJA UTAMA (KPI)</div>
    <table class="stats-table">
        <tr>
            <td>
                <div class="stat-box">
                    <div class="stat-value">{{ number_format($stats['total_titles'] ?? 0) }}</div>
                    <div class="stat-label">Total Judul</div>
                </div>
            </td>
            <td>
                <div class="stat-box">
                    <div class="stat-value">{{ number_format($stats['total_items'] ?? 0) }}</div>
                    <div class="stat-label">Total Eksemplar</div>
                </div>
            </td>
            <td>
                <div class="stat-box">
                    <div class="stat-value">{{ number_format($stats['total_members'] ?? 0) }}</div>
                    <div class="stat-label">Total Anggota</div>
                </div>
            </td>
            <td>
                <div class="stat-box">
                    <div class="stat-value">{{ number_format($stats['active_loans'] ?? 0) }}</div>
                    <div class="stat-label">Pinjaman Aktif</div>
                </div>
            </td>
        </tr>
    </table>
</div>

<div class="section">
    <div class="section-title">STATUS KOLEKSI</div>
    <table class="data-table">
        <tr>
            <th style="width:50%">Indikator</th>
            <th style="width:25%">Jumlah</th>
            <th style="width:25%">Persentase</th>
        </tr>
        <tr>
            <td>Eksemplar Tersedia</td>
            <td class="number">{{ number_format($stats['available_items'] ?? 0) }}</td>
            <td class="number">{{ ($stats['total_items'] ?? 0) > 0 ? round((($stats['available_items'] ?? 0) / $stats['total_items']) * 100, 1) : 0 }}%</td>
        </tr>
        <tr>
            <td>Eksemplar Dipinjam</td>
            <td class="number">{{ number_format($stats['on_loan_items'] ?? 0) }}</td>
            <td class="number">{{ ($stats['total_items'] ?? 0) > 0 ? round((($stats['on_loan_items'] ?? 0) / $stats['total_items']) * 100, 1) : 0 }}%</td>
        </tr>
    </table>
</div>

<div class="section">
    <div class="section-title">STATUS KEANGGOTAAN</div>
    <table class="data-table">
        <tr>
            <th style="width:50%">Indikator</th>
            <th style="width:25%">Jumlah</th>
            <th style="width:25%">Keterangan</th>
        </tr>
        <tr>
            <td>Total Anggota Terdaftar</td>
            <td class="number">{{ number_format($stats['total_members'] ?? 0) }}</td>
            <td>-</td>
        </tr>
        <tr>
            <td>Anggota Aktif (Belum Expired)</td>
            <td class="number">{{ number_format($stats['active_members'] ?? 0) }}</td>
            <td><span class="badge badge-success">Aktif</span></td>
        </tr>
        <tr>
            <td>Anggota Baru Bulan Ini</td>
            <td class="number">{{ number_format($stats['new_members_month'] ?? 0) }}</td>
            <td><span class="badge badge-primary">Baru</span></td>
        </tr>
    </table>
</div>

<div class="section">
    <div class="section-title">STATISTIK SIRKULASI</div>
    <table class="data-table">
        <tr>
            <th style="width:50%">Periode</th>
            <th style="width:25%">Peminjaman</th>
            <th style="width:25%">Status</th>
        </tr>
        <tr>
            <td>Hari Ini</td>
            <td class="number">{{ number_format($stats['loans_today'] ?? 0) }}</td>
            <td>-</td>
        </tr>
        <tr>
            <td>Bulan Ini</td>
            <td class="number">{{ number_format($stats['loans_this_month'] ?? 0) }}</td>
            <td>-</td>
        </tr>
        <tr>
            <td>Tahun Ini</td>
            <td class="number">{{ number_format($stats['loans_this_year'] ?? 0) }}</td>
            <td>-</td>
        </tr>
        <tr>
            <td>Pinjaman Terlambat</td>
            <td class="number">{{ number_format($stats['overdue_loans'] ?? 0) }}</td>
            <td><span class="badge badge-danger">Perlu Tindakan</span></td>
        </tr>
    </table>
</div>

@if(($stats['unpaid_fines'] ?? 0) > 0)
<div class="alert-box danger">
    <strong>PERHATIAN - DENDA BELUM DIBAYAR:</strong> Rp {{ number_format($stats['unpaid_fines'] ?? 0) }}
</div>
@endif

<div class="section">
    <div class="section-title">KOLEKSI DIGITAL</div>
    <table class="data-table">
        <tr>
            <th style="width:50%">Jenis Koleksi</th>
            <th style="width:25%">Jumlah</th>
            <th style="width:25%">Status</th>
        </tr>
        <tr>
            <td>E-Book</td>
            <td class="number">{{ number_format($stats['total_ebooks'] ?? 0) }}</td>
            <td><span class="badge badge-primary">Aktif</span></td>
        </tr>
        <tr>
            <td>E-Thesis / Tugas Akhir</td>
            <td class="number">{{ number_format($stats['total_ethesis'] ?? 0) }}</td>
            <td><span class="badge badge-primary">Aktif</span></td>
        </tr>
    </table>
</div>

<div class="summary-box">
    <div class="summary-title">RINGKASAN EKSEKUTIF</div>
    <div class="summary-text">
        Perpustakaan {{ $branchName }} memiliki <strong>{{ number_format($stats['total_titles'] ?? 0) }} judul</strong> 
        dengan <strong>{{ number_format($stats['total_items'] ?? 0) }} eksemplar</strong>. 
        Saat ini terdapat <strong>{{ number_format($stats['active_members'] ?? 0) }} anggota aktif</strong> 
        dengan <strong>{{ number_format($stats['active_loans'] ?? 0) }} pinjaman aktif</strong>.
        @if(($stats['overdue_loans'] ?? 0) > 0)
        Terdapat <strong>{{ number_format($stats['overdue_loans'] ?? 0) }} pinjaman terlambat</strong> yang memerlukan tindak lanjut.
        @endif
    </div>
</div>
@endsection
