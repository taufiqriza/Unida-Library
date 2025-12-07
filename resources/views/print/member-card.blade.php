<!DOCTYPE html>
<html>
<head>
    <title>Cetak Kartu Anggota</title>
    <style>
        @page { margin: 5mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 9pt; }
        
        .cards-container {
            display: flex;
            flex-wrap: wrap;
            gap: 5mm;
        }
        
        .card {
            width: 85.6mm;
            height: 53.98mm;
            border: 1px solid #333;
            border-radius: 3mm;
            padding: 3mm;
            page-break-inside: avoid;
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .card-header {
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.3);
            padding-bottom: 2mm;
            margin-bottom: 2mm;
        }
        
        .card-header h1 {
            font-size: 10pt;
            font-weight: bold;
            margin: 0;
        }
        
        .card-header p {
            font-size: 7pt;
            margin: 0;
            opacity: 0.9;
        }
        
        .card-body {
            display: flex;
            gap: 3mm;
        }
        
        .photo {
            width: 22mm;
            height: 28mm;
            background: #fff;
            border: 1px solid #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        
        .photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .photo-placeholder {
            color: #999;
            font-size: 7pt;
            text-align: center;
        }
        
        .info {
            flex: 1;
            font-size: 8pt;
        }
        
        .info table {
            width: 100%;
        }
        
        .info td {
            padding: 0.5mm 0;
            vertical-align: top;
        }
        
        .info td:first-child {
            width: 18mm;
            opacity: 0.9;
        }
        
        .member-name {
            font-size: 10pt;
            font-weight: bold;
            margin-bottom: 1mm;
        }
        
        .card-footer {
            position: absolute;
            bottom: 2mm;
            left: 3mm;
            right: 3mm;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        
        .barcode {
            font-family: 'Libre Barcode 39', 'Free 3 of 9', monospace;
            font-size: 24pt;
            color: #000;
            background: #fff;
            padding: 1mm 2mm;
            border-radius: 1mm;
        }
        
        .expire {
            font-size: 7pt;
            text-align: right;
            opacity: 0.9;
        }
        
        @media print {
            .no-print { display: none; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Barcode+39&display=swap" rel="stylesheet">
</head>
<body>
    <div class="no-print" style="padding: 10px; background: #f0f0f0; margin-bottom: 10px;">
        <button onclick="window.print()" style="padding: 8px 16px; cursor: pointer;">🖨️ Cetak</button>
        <button onclick="window.close()" style="padding: 8px 16px; cursor: pointer;">✕ Tutup</button>
        <span style="margin-left: 10px;">Total: {{ $members->count() }} kartu</span>
    </div>

    <div class="cards-container">
        @foreach($members as $member)
        <div class="card">
            <div class="card-header">
                <h1>PERPUSTAKAAN</h1>
                <p>Kartu Anggota</p>
            </div>
            
            <div class="card-body">
                <div class="photo">
                    @if($member->photo)
                        <img src="{{ asset('storage/' . $member->photo) }}" alt="Foto">
                    @else
                        <div class="photo-placeholder">FOTO<br>3x4</div>
                    @endif
                </div>
                
                <div class="info">
                    <div class="member-name">{{ $member->name }}</div>
                    <table>
                        <tr>
                            <td>No. Anggota</td>
                            <td>: {{ $member->member_id }}</td>
                        </tr>
                        <tr>
                            <td>Tipe</td>
                            <td>: {{ $member->memberType->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Berlaku s/d</td>
                            <td>: {{ $member->expire_date?->format('d/m/Y') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="card-footer">
                <div class="barcode">*{{ $member->member_id }}*</div>
                <div class="expire">
                    Terdaftar: {{ $member->register_date?->format('d/m/Y') }}
                </div>
            </div>
        </div>
        @endforeach
    </div>
</body>
</html>
