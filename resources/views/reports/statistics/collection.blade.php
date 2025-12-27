@extends('reports.statistics.layout')

@section('title', 'Laporan Analisis Koleksi')

@section('content')
<div class="report-title">
    <h1>LAPORAN ANALISIS KOLEKSI PERPUSTAKAAN</h1>
    <p>Periode: {{ $reportPeriod }} | Cabang: {{ $branchName }}</p>
</div>

<div class="meta-box">
    <table class="meta-table">
        <tr>
            <td style="width:33%"><span class="meta-label">Tanggal Cetak:</span><br><span class="meta-value">{{ $generatedAt }}</span></td>
            <td style="width:33%"><span class="meta-label">Dicetak Oleh:</span><br><span class="meta-value">{{ $generatedBy }}</span></td>
            <td style="width:33%"><span class="meta-label">Jenis Laporan:</span><br><span class="meta-value">Analisis Koleksi</span></td>
        </tr>
    </table>
</div>

<div class="section">
    <div class="section-title">RINGKASAN KOLEKSI</div>
    <table class="stats-table">
        <tr>
            <td><div class="stat-box"><div class="stat-value">{{ number_format($stats['total_titles'] ?? 0) }}</div><div class="stat-label">Judul</div></div></td>
            <td><div class="stat-box"><div class="stat-value">{{ number_format($stats['total_items'] ?? 0) }}</div><div class="stat-label">Eksemplar</div></div></td>
            <td><div class="stat-box"><div class="stat-value">{{ number_format($stats['total_authors'] ?? 0) }}</div><div class="stat-label">Pengarang</div></div></td>
            <td><div class="stat-box"><div class="stat-value">{{ number_format($stats['total_publishers'] ?? 0) }}</div><div class="stat-label">Penerbit</div></div></td>
        </tr>
    </table>
</div>

<div class="section">
    <div class="section-title">DISTRIBUSI KLASIFIKASI (DDC)</div>
    <table class="data-table">
        <tr>
            <th style="width:12%">Kelas</th>
            <th style="width:48%">Deskripsi</th>
            <th style="width:20%">Jumlah</th>
            <th style="width:20%">%</th>
        </tr>
        @php
            $totalClass = array_sum(array_column($byClassification, 'count'));
            $ddcNames = ['0'=>'Karya Umum','1'=>'Filsafat & Psikologi','2'=>'Agama','3'=>'Ilmu Sosial','4'=>'Bahasa','5'=>'Sains','6'=>'Teknologi','7'=>'Seni & Rekreasi','8'=>'Sastra','9'=>'Sejarah & Geografi'];
        @endphp
        @forelse($byClassification as $item)
        <tr>
            <td><strong>{{ $item['classification'] }}xx</strong></td>
            <td>{{ $ddcNames[substr($item['classification'], 0, 1)] ?? 'Lainnya' }}</td>
            <td class="number">{{ number_format($item['count']) }}</td>
            <td class="number">{{ $totalClass > 0 ? round(($item['count'] / $totalClass) * 100, 1) : 0 }}%</td>
        </tr>
        @empty
        <tr><td colspan="4" style="text-align:center;color:#6b7280;">Tidak ada data</td></tr>
        @endforelse
        @if($totalClass > 0)
        <tr class="total-row">
            <td colspan="2"><strong>TOTAL</strong></td>
            <td class="number">{{ number_format($totalClass) }}</td>
            <td class="number">100%</td>
        </tr>
        @endif
    </table>
</div>

<table class="two-col">
    <tr>
        <td>
            <div class="section">
                <div class="section-title">JENIS MEDIA</div>
                <table class="data-table">
                    <tr><th>No</th><th>Jenis Media</th><th>Jumlah</th></tr>
                    @php $totalMedia = array_sum(array_column($byMediaType, 'count')); @endphp
                    @forelse($byMediaType as $i => $item)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $item['name'] }}</td>
                        <td class="number">{{ number_format($item['count']) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" style="text-align:center">-</td></tr>
                    @endforelse
                </table>
            </div>
        </td>
        <td>
            <div class="section">
                <div class="section-title">JENIS KOLEKSI</div>
                <table class="data-table">
                    <tr><th>No</th><th>Jenis Koleksi</th><th>Jumlah</th></tr>
                    @forelse($byCollectionType as $i => $item)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $item['name'] }}</td>
                        <td class="number">{{ number_format($item['count']) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" style="text-align:center">-</td></tr>
                    @endforelse
                </table>
            </div>
        </td>
    </tr>
</table>

<table class="two-col">
    <tr>
        <td>
            <div class="section">
                <div class="section-title">BAHASA KOLEKSI</div>
                <table class="data-table">
                    <tr><th>No</th><th>Bahasa</th><th>Jumlah</th></tr>
                    @forelse($byLanguage as $i => $item)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $item['language'] ?: 'Tidak Diketahui' }}</td>
                        <td class="number">{{ number_format($item['count']) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" style="text-align:center">-</td></tr>
                    @endforelse
                </table>
            </div>
        </td>
        <td>
            <div class="section">
                <div class="section-title">TAHUN TERBIT</div>
                <table class="data-table">
                    <tr><th>No</th><th>Tahun</th><th>Jumlah</th></tr>
                    @forelse(array_slice($byYear, 0, 10) as $i => $item)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $item['publish_year'] }}</td>
                        <td class="number">{{ number_format($item['count']) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" style="text-align:center">-</td></tr>
                    @endforelse
                </table>
            </div>
        </td>
    </tr>
</table>

<div class="page-break"></div>

<div class="section">
    <div class="section-title">TOP 10 PENERBIT</div>
    <table class="data-table">
        <tr><th style="width:8%">No</th><th style="width:72%">Nama Penerbit</th><th style="width:20%">Jumlah Judul</th></tr>
        @forelse($byPublisher as $i => $item)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $item['name'] }}</td>
            <td class="number">{{ number_format($item['count']) }}</td>
        </tr>
        @empty
        <tr><td colspan="3" style="text-align:center">Tidak ada data</td></tr>
        @endforelse
    </table>
</div>

<table class="two-col">
    <tr>
        <td>
            <div class="section">
                <div class="section-title">TOP 12 PENGARANG</div>
                <table class="data-table">
                    <tr><th>No</th><th>Nama</th><th>Judul</th></tr>
                    @forelse($byAuthor as $i => $item)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $item['name'] }}</td>
                        <td class="number">{{ number_format($item['count']) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" style="text-align:center">-</td></tr>
                    @endforelse
                </table>
            </div>
        </td>
        <td>
            <div class="section">
                <div class="section-title">TOP 12 SUBJEK</div>
                <table class="data-table">
                    <tr><th>No</th><th>Subjek</th><th>Judul</th></tr>
                    @forelse($bySubject as $i => $item)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $item['name'] }}</td>
                        <td class="number">{{ number_format($item['count']) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" style="text-align:center">-</td></tr>
                    @endforelse
                </table>
            </div>
        </td>
    </tr>
</table>

@if(count($byDepartment) > 0 || count($byInputYear) > 0)
<div class="page-break"></div>

@if(count($byDepartment) > 0)
<div class="section">
    <div class="section-title">KOLEKSI PER PRODI/SUBJEK</div>
    <table class="data-table">
        <tr>
            <th style="width:8%">No</th>
            <th style="width:62%">Program Studi / Subjek</th>
            <th style="width:15%">Jumlah</th>
            <th style="width:15%">%</th>
        </tr>
        @php $totalDept = array_sum(array_column($byDepartment, 'count')); @endphp
        @foreach($byDepartment as $i => $item)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $item['department'] }}</td>
            <td class="number">{{ number_format($item['count']) }}</td>
            <td class="number">{{ $totalDept > 0 ? round(($item['count'] / $totalDept) * 100, 1) : 0 }}%</td>
        </tr>
        @endforeach
        <tr class="total-row">
            <td colspan="2"><strong>TOTAL</strong></td>
            <td class="number">{{ number_format($totalDept) }}</td>
            <td class="number">100%</td>
        </tr>
    </table>
</div>
@endif

@if(count($byInputYear) > 0)
<div class="section">
    <div class="section-title">KOLEKSI PER TAHUN INPUT</div>
    <p style="font-size:8px;color:#666;margin-bottom:8px;">Data berdasarkan tahun buku diinput ke sistem</p>
    <table class="data-table">
        <tr>
            <th style="width:8%">No</th>
            <th style="width:30%">Tahun Input</th>
            <th style="width:30%">Jumlah Buku</th>
            <th style="width:32%">%</th>
        </tr>
        @php $totalInput = array_sum(array_column($byInputYear, 'count')); @endphp
        @foreach($byInputYear as $i => $item)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $item['input_year'] }}</td>
            <td class="number">{{ number_format($item['count']) }}</td>
            <td class="number">{{ $totalInput > 0 ? round(($item['count'] / $totalInput) * 100, 1) : 0 }}%</td>
        </tr>
        @endforeach
        <tr class="total-row">
            <td colspan="2"><strong>TOTAL</strong></td>
            <td class="number">{{ number_format($totalInput) }}</td>
            <td class="number">100%</td>
        </tr>
    </table>
</div>
@endif
@endif
@endsection
