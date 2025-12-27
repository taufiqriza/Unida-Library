@extends('reports.statistics.layout')

@section('title', 'Laporan Lengkap Statistik Perpustakaan')

@section('content')
<div class="report-title">
    <h1>LAPORAN LENGKAP STATISTIK PERPUSTAKAAN</h1>
    <p>Untuk Keperluan Audit Mutu Internal | Periode: {{ $reportPeriod }}</p>
</div>

<div class="meta-box">
    <table class="meta-table">
        <tr>
            <td style="width:33%"><span class="meta-label">Cabang:</span><br><span class="meta-value">{{ $branchName }}</span></td>
            <td style="width:33%"><span class="meta-label">Tanggal Cetak:</span><br><span class="meta-value">{{ $generatedAt }}</span></td>
            <td style="width:33%"><span class="meta-label">Dicetak Oleh:</span><br><span class="meta-value">{{ $generatedBy }}</span></td>
        </tr>
    </table>
</div>

{{-- BAGIAN 1 --}}
<div class="section">
    <div class="section-title">BAGIAN 1: RINGKASAN EKSEKUTIF</div>
    <table class="stats-table">
        <tr>
            <td><div class="stat-box"><div class="stat-value">{{ number_format($stats['total_titles'] ?? 0) }}</div><div class="stat-label">Judul</div></div></td>
            <td><div class="stat-box"><div class="stat-value">{{ number_format($stats['total_items'] ?? 0) }}</div><div class="stat-label">Eksemplar</div></div></td>
            <td><div class="stat-box"><div class="stat-value">{{ number_format($stats['total_members'] ?? 0) }}</div><div class="stat-label">Anggota</div></div></td>
            <td><div class="stat-box"><div class="stat-value">{{ number_format($stats['loans_this_year'] ?? 0) }}</div><div class="stat-label">Pinjam/Tahun</div></div></td>
        </tr>
    </table>
    
    <div class="summary-box">
        <div class="summary-title">EXECUTIVE SUMMARY</div>
        <div class="summary-text">
            Perpustakaan {{ $branchName }} mengelola <strong>{{ number_format($stats['total_titles'] ?? 0) }} judul</strong> 
            dengan <strong>{{ number_format($stats['total_items'] ?? 0) }} eksemplar</strong> koleksi fisik,
            ditambah <strong>{{ number_format($stats['total_ebooks'] ?? 0) }} e-book</strong> dan 
            <strong>{{ number_format($stats['total_ethesis'] ?? 0) }} e-thesis</strong> digital.
            Melayani <strong>{{ number_format($stats['active_members'] ?? 0) }} anggota aktif</strong> 
            dengan rata-rata <strong>{{ number_format(round(($stats['loans_this_year'] ?? 0) / 12)) }} transaksi/bulan</strong>.
        </div>
    </div>
</div>

{{-- BAGIAN 2 --}}
<div class="page-break"></div>
<div class="section">
    <div class="section-title">BAGIAN 2: ANALISIS KOLEKSI</div>
    
    <p style="font-size:9px;font-weight:bold;margin:8px 0 4px;">2.1 Distribusi Klasifikasi (DDC)</p>
    <table class="data-table">
        <tr><th style="width:12%">Kelas</th><th style="width:48%">Deskripsi</th><th style="width:20%">Jumlah</th><th style="width:20%">%</th></tr>
        @php
            $totalClass = array_sum(array_column($byClassification, 'count'));
            $ddcNames = ['0'=>'Karya Umum','1'=>'Filsafat','2'=>'Agama','3'=>'Ilmu Sosial','4'=>'Bahasa','5'=>'Sains','6'=>'Teknologi','7'=>'Seni','8'=>'Sastra','9'=>'Sejarah'];
        @endphp
        @foreach($byClassification as $item)
        <tr>
            <td><strong>{{ $item['classification'] }}xx</strong></td>
            <td>{{ $ddcNames[substr($item['classification'], 0, 1)] ?? '-' }}</td>
            <td class="number">{{ number_format($item['count']) }}</td>
            <td class="number">{{ $totalClass > 0 ? round(($item['count'] / $totalClass) * 100, 1) : 0 }}%</td>
        </tr>
        @endforeach
    </table>

    <table class="two-col">
        <tr>
            <td>
                <p style="font-size:9px;font-weight:bold;margin:10px 0 4px;">2.2 Jenis Media</p>
                <table class="data-table">
                    <tr><th>No</th><th>Jenis</th><th>Jumlah</th></tr>
                    @foreach($byMediaType as $i => $item)
                    <tr><td>{{ $i + 1 }}</td><td>{{ $item['name'] }}</td><td class="number">{{ number_format($item['count']) }}</td></tr>
                    @endforeach
                </table>
            </td>
            <td>
                <p style="font-size:9px;font-weight:bold;margin:10px 0 4px;">2.3 Jenis Koleksi</p>
                <table class="data-table">
                    <tr><th>No</th><th>Jenis</th><th>Jumlah</th></tr>
                    @foreach($byCollectionType as $i => $item)
                    <tr><td>{{ $i + 1 }}</td><td>{{ $item['name'] }}</td><td class="number">{{ number_format($item['count']) }}</td></tr>
                    @endforeach
                </table>
            </td>
        </tr>
    </table>

    <table class="two-col">
        <tr>
            <td>
                <p style="font-size:9px;font-weight:bold;margin:10px 0 4px;">2.4 Bahasa</p>
                <table class="data-table">
                    <tr><th>No</th><th>Bahasa</th><th>Jumlah</th></tr>
                    @foreach($byLanguage as $i => $item)
                    <tr><td>{{ $i + 1 }}</td><td>{{ $item['language'] ?: '-' }}</td><td class="number">{{ number_format($item['count']) }}</td></tr>
                    @endforeach
                </table>
            </td>
            <td>
                <p style="font-size:9px;font-weight:bold;margin:10px 0 4px;">2.5 Tahun Terbit</p>
                <table class="data-table">
                    <tr><th>No</th><th>Tahun</th><th>Jumlah</th></tr>
                    @foreach(array_slice($byYear, 0, 8) as $i => $item)
                    <tr><td>{{ $i + 1 }}</td><td>{{ $item['publish_year'] }}</td><td class="number">{{ number_format($item['count']) }}</td></tr>
                    @endforeach
                </table>
            </td>
        </tr>
    </table>
</div>

{{-- BAGIAN 3 --}}
<div class="page-break"></div>
<div class="section">
    <div class="section-title">BAGIAN 3: STATISTIK SIRKULASI</div>
    
    <p style="font-size:9px;font-weight:bold;margin:8px 0 4px;">3.1 Ringkasan Sirkulasi</p>
    <table class="data-table">
        <tr><th>Indikator</th><th>Nilai</th><th>Status</th></tr>
        <tr><td>Pinjaman Hari Ini</td><td class="number">{{ number_format($stats['loans_today'] ?? 0) }}</td><td>-</td></tr>
        <tr><td>Pinjaman Bulan Ini</td><td class="number">{{ number_format($stats['loans_this_month'] ?? 0) }}</td><td>-</td></tr>
        <tr><td>Pinjaman Tahun Ini</td><td class="number">{{ number_format($stats['loans_this_year'] ?? 0) }}</td><td>-</td></tr>
        <tr><td>Pinjaman Aktif</td><td class="number">{{ number_format($stats['active_loans'] ?? 0) }}</td><td><span class="badge badge-primary">Berjalan</span></td></tr>
        <tr><td>Pinjaman Terlambat</td><td class="number">{{ number_format($stats['overdue_loans'] ?? 0) }}</td><td><span class="badge badge-danger">Tindakan</span></td></tr>
    </table>

    <p style="font-size:9px;font-weight:bold;margin:12px 0 4px;">3.2 Tren 12 Bulan Terakhir</p>
    <table class="data-table">
        <tr><th>Bulan</th><th>Peminjaman</th><th>Pengembalian</th><th>Anggota Baru</th></tr>
        @foreach($monthlyTrend as $trend)
        <tr>
            <td>{{ $trend['month'] }}</td>
            <td class="number">{{ number_format($trend['loans']) }}</td>
            <td class="number">{{ number_format($trend['returns']) }}</td>
            <td class="number">{{ number_format($trend['new_members']) }}</td>
        </tr>
        @endforeach
        @php
            $tLoans = array_sum(array_column($monthlyTrend, 'loans'));
            $tReturns = array_sum(array_column($monthlyTrend, 'returns'));
            $tMembers = array_sum(array_column($monthlyTrend, 'new_members'));
        @endphp
        <tr class="total-row">
            <td><strong>TOTAL</strong></td>
            <td class="number">{{ number_format($tLoans) }}</td>
            <td class="number">{{ number_format($tReturns) }}</td>
            <td class="number">{{ number_format($tMembers) }}</td>
        </tr>
    </table>
</div>

{{-- BAGIAN 4 --}}
<div class="page-break"></div>
<div class="section">
    <div class="section-title">BAGIAN 4: KEANGGOTAAN & DENDA</div>
    
    <table class="two-col">
        <tr>
            <td>
                <p style="font-size:9px;font-weight:bold;margin:8px 0 4px;">4.1 Statistik Keanggotaan</p>
                <table class="data-table">
                    <tr><th>Indikator</th><th>Jumlah</th></tr>
                    <tr><td>Total Anggota</td><td class="number">{{ number_format($stats['total_members'] ?? 0) }}</td></tr>
                    <tr><td>Anggota Aktif</td><td class="number">{{ number_format($stats['active_members'] ?? 0) }}</td></tr>
                    <tr><td>Baru Bulan Ini</td><td class="number">{{ number_format($stats['new_members_month'] ?? 0) }}</td></tr>
                </table>
            </td>
            <td>
                <p style="font-size:9px;font-weight:bold;margin:8px 0 4px;">4.2 Statistik Denda</p>
                <table class="data-table">
                    <tr><th>Kategori</th><th>Nominal</th></tr>
                    <tr><td>Total Denda</td><td class="number">Rp {{ number_format($stats['total_fines'] ?? 0) }}</td></tr>
                    <tr><td>Sudah Dibayar</td><td class="number">Rp {{ number_format($stats['paid_fines'] ?? 0) }}</td></tr>
                    <tr><td>Belum Dibayar</td><td class="number">Rp {{ number_format($stats['unpaid_fines'] ?? 0) }}</td></tr>
                </table>
            </td>
        </tr>
    </table>
</div>

{{-- BAGIAN 5 --}}
<div class="section">
    <div class="section-title">BAGIAN 5: KOLEKSI DIGITAL</div>
    <table class="data-table">
        <tr><th>Jenis</th><th>Jumlah</th><th>Status</th></tr>
        <tr><td>E-Book</td><td class="number">{{ number_format($stats['total_ebooks'] ?? 0) }}</td><td><span class="badge badge-primary">Aktif</span></td></tr>
        <tr><td>E-Thesis</td><td class="number">{{ number_format($stats['total_ethesis'] ?? 0) }}</td><td><span class="badge badge-primary">Aktif</span></td></tr>
    </table>
</div>

{{-- BAGIAN 6 --}}
@if(count($branchStats) > 0)
<div class="section">
    <div class="section-title">BAGIAN 6: STATISTIK PER CABANG</div>
    <table class="data-table">
        <tr><th>No</th><th>Cabang</th><th>Judul</th><th>Eksemplar</th><th>Anggota</th><th>Pinjam/Bln</th></tr>
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

{{-- Tanda Tangan --}}
<table class="signature-table">
    <tr>
        <td>
            <p style="font-size:9px;">Mengetahui,</p>
            <div class="signature-line">
                <p class="signature-name">Kepala Perpustakaan</p>
            </div>
        </td>
        <td>
            <p style="font-size:9px;">{{ $branchName }}, {{ now()->format('d F Y') }}</p>
            <div class="signature-line">
                <p class="signature-name">Petugas</p>
            </div>
        </td>
    </tr>
</table>
@endsection
