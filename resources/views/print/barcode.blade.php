<!DOCTYPE html>
<html>
<head>
    <title>Cetak Barcode</title>
    <meta charset="utf-8">
    <style>
        @page { margin: 0.5cm; size: auto; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { 
            font-family: Arial, sans-serif; 
            font-size: 10pt; 
            background: #f5f5f5;
            padding: 10px;
        }
        
        .labels-container {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        
        /* Label Card - SLiMS Style */
        .label-card {
            width: 9cm;
            height: 3cm;
            background: #fff;
            border: 1px solid #333;
            display: flex;
            overflow: hidden;
            page-break-inside: avoid;
        }
        
        /* Left Section - Barcode */
        .barcode-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 4px 8px;
            position: relative;
        }
        
        .book-title {
            font-size: 8pt;
            color: #333;
            text-align: center;
            line-height: 1.2;
            max-height: 2.4em;
            overflow: hidden;
            margin-bottom: 2px;
            background: #fff;
            position: relative;
            z-index: 1;
        }
        
        .barcode-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        
        .barcode-image {
            font-family: 'Libre Barcode 128', 'Libre Barcode 39', monospace;
            font-size: 48pt;
            line-height: 1;
            letter-spacing: -1px;
            color: #000;
        }
        
        .barcode-text {
            font-size: 9pt;
            font-weight: bold;
            letter-spacing: 1px;
            color: #000;
            margin-top: 2px;
        }
        
        /* Right Section - Call Number */
        .callnumber-section {
            width: 2.5cm;
            border-left: 1px solid #333;
            display: flex;
            flex-direction: column;
            background: #fff;
        }
        
        .library-header {
            font-size: 7pt;
            font-weight: bold;
            text-align: center;
            padding: 3px 2px;
            border-bottom: 1px solid #333;
            background: #f0f0f0;
            text-transform: uppercase;
            line-height: 1.2;
        }
        
        .callnumber-body {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2px;
        }
        
        .callnumber-line {
            font-weight: bold;
            font-size: 11pt;
            text-align: center;
            line-height: 1.3;
        }
        
        /* Print Controls */
        .no-print { 
            margin-bottom: 10px; 
            padding: 10px 15px;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .no-print button {
            padding: 8px 16px;
            cursor: pointer;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 13px;
            font-weight: bold;
        }
        .no-print button:hover { background: #45a049; }
        .no-print .btn-close {
            background: #666;
        }
        .no-print .btn-close:hover { background: #555; }
        .no-print .label-count {
            color: #666;
            font-size: 13px;
            margin-left: auto;
        }
        
        @media print { 
            .no-print { display: none !important; } 
            body { background: #fff; padding: 0; }
            .label-card { border: 1px solid #000; }
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Barcode+128&family=Libre+Barcode+39&display=swap" rel="stylesheet">
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()">🖨️ Cetak</button>
        <button onclick="window.close()" class="btn-close">✕ Tutup</button>
        <span class="label-count">{{ $items->count() }} label</span>
    </div>

    @php
        $libraryName = \App\Models\Setting::get('library_name', 'PERPUSTAKAAN UNIDA GONTOR');
    @endphp

    <div class="labels-container">
        @foreach($items as $item)
        @php
            $book = $item->book;
            $callNumber = $item->call_number ?: $book?->call_number;
            
            // Parse call number - split by space
            // Format: S 827 TAR Z atau 297.1 MUH k
            $cnParts = $callNumber ? preg_split('/\s+/', trim($callNumber)) : [];
        @endphp
        <div class="label-card">
            {{-- Barcode Section --}}
            <div class="barcode-section">
                <div class="book-title">{{ Str::limit($book?->title ?? 'No Title', 50) }}</div>
                <div class="barcode-wrapper">
                    <div class="barcode-image">{{ $item->barcode }}</div>
                    <div class="barcode-text">{{ $item->barcode }}</div>
                </div>
            </div>
            
            {{-- Call Number Section --}}
            <div class="callnumber-section">
                <div class="library-header">{{ Str::limit($libraryName, 20) }}</div>
                <div class="callnumber-body">
                    @foreach($cnParts as $part)
                    <div class="callnumber-line">{{ $part }}</div>
                    @endforeach
                    @if(empty($cnParts))
                    <div class="callnumber-line">-</div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
    <script>
        // Auto print on load (like SLiMS)
        window.onload = function() {
            // Uncomment below to auto-print
            // window.print();
        }
    </script>
</body>
</html>
