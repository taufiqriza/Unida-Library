<!DOCTYPE html>
<html>
<head>
    <title>Cetak Barcode - {{ $items->count() }} Label</title>
    <meta charset="utf-8">
    <style>
        @page { 
            margin: 0.5cm; 
            size: A4 portrait;
        }
        
        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        body { 
            font-family: 'Segoe UI', Arial, sans-serif; 
            font-size: 10pt; 
            background: #f0f2f5;
            padding: 15px;
        }
        
        /* Print Controls */
        .print-controls { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 20px;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
        }
        
        .print-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .print-icon {
            width: 50px;
            height: 50px;
            background: rgba(255,255,255,0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        
        .print-text h2 {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 4px;
        }
        
        .print-text p {
            font-size: 13px;
            opacity: 0.9;
        }
        
        .print-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }
        
        .btn-print {
            background: white;
            color: #667eea;
        }
        
        .btn-print:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        }
        
        .btn-close {
            background: rgba(255,255,255,0.2);
            color: white;
        }
        
        .btn-close:hover {
            background: rgba(255,255,255,0.3);
        }
        
        /* Paper Preview */
        .paper-preview {
            background: white;
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 0.5cm;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            border-radius: 4px;
        }
        
        /* Labels Grid - 2 columns */
        .labels-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 4mm;
        }
        
        /* Label Card - Premium Design */
        .label-card {
            width: 95mm;
            height: 30mm;
            background: #fff;
            border: 0.5pt solid #e0e0e0;
            border-radius: 3mm;
            display: flex;
            overflow: hidden;
            page-break-inside: avoid;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        
        /* Barcode Section */
        .barcode-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 2mm 3mm;
            background: linear-gradient(180deg, #fafafa 0%, #fff 100%);
        }
        
        .book-title {
            font-size: 7pt;
            color: #555;
            text-align: center;
            line-height: 1.3;
            max-height: 2.6em;
            overflow: hidden;
            margin-bottom: 1mm;
            font-weight: 500;
        }
        
        .barcode-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        
        .barcode-image {
            font-family: 'Libre Barcode 128', monospace;
            font-size: 42pt;
            line-height: 1;
            letter-spacing: -1px;
            color: #000;
        }
        
        .barcode-text {
            font-size: 8pt;
            font-weight: 700;
            letter-spacing: 2px;
            color: #333;
            margin-top: 1mm;
            font-family: 'Consolas', 'Monaco', monospace;
        }
        
        /* Call Number Section - Spine Label */
        .callnumber-section {
            width: 25mm;
            border-left: 0.5pt solid #e0e0e0;
            display: flex;
            flex-direction: column;
            background: #fff;
        }
        
        .library-header {
            font-size: 5.5pt;
            font-weight: 700;
            text-align: center;
            padding: 1.5mm 1mm;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            line-height: 1.2;
        }
        
        .callnumber-body {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 1mm;
            background: linear-gradient(180deg, #f8f9fa 0%, #fff 100%);
        }
        
        .callnumber-line {
            font-weight: 800;
            font-size: 10pt;
            text-align: center;
            line-height: 1.25;
            color: #1a1a1a;
        }
        
        .callnumber-line.collection-code {
            font-size: 9pt;
            color: #667eea;
            font-weight: 700;
        }
        
        /* Cut Guide */
        .cut-guide {
            margin-top: 15px;
            padding: 12px 16px;
            background: #fff3cd;
            border: 1px dashed #ffc107;
            border-radius: 8px;
            font-size: 12px;
            color: #856404;
            text-align: center;
        }
        
        @media print { 
            .print-controls, .cut-guide { display: none !important; } 
            body { 
                background: #fff; 
                padding: 0; 
            }
            .paper-preview {
                box-shadow: none;
                border-radius: 0;
            }
            .label-card { 
                border: 0.5pt solid #ccc;
                box-shadow: none;
            }
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Barcode+128&display=swap" rel="stylesheet">
</head>
<body>
    <div class="print-controls">
        <div class="print-info">
            <div class="print-icon">🏷️</div>
            <div class="print-text">
                <h2>Cetak Label Barcode</h2>
                <p>{{ $items->count() }} label • Kertas A4 • 2 kolom × 9 baris</p>
            </div>
        </div>
        <div class="print-actions">
            <button onclick="window.print()" class="btn btn-print">
                <span>🖨️</span> Cetak
            </button>
            <button onclick="history.back()" class="btn btn-close">
                <span>←</span> Kembali
            </button>
        </div>
    </div>

    @php
        $libraryName = \App\Models\Setting::get('library_name', 'PERPUSTAKAAN UNIDA');
    @endphp

    <div class="paper-preview">
        <div class="labels-grid">
            @foreach($items as $item)
            @php
                $book = $item->book;
                $callNumber = $item->call_number ?: $book?->call_number;
                $cnParts = $callNumber ? preg_split('/\s+/', trim($callNumber)) : [];
            @endphp
            <div class="label-card">
                {{-- Barcode Section --}}
                <div class="barcode-section">
                    <div class="book-title">{{ Str::limit($book?->title ?? 'No Title', 45) }}</div>
                    <div class="barcode-wrapper">
                        <div class="barcode-image">{{ $item->barcode }}</div>
                        <div class="barcode-text">{{ $item->barcode }}</div>
                    </div>
                </div>
                
                {{-- Call Number Section (Spine Label) --}}
                <div class="callnumber-section">
                    <div class="library-header">{{ Str::limit($libraryName, 18) }}</div>
                    <div class="callnumber-body">
                        @foreach($cnParts as $index => $part)
                        <div class="callnumber-line {{ $index === 0 && strlen($part) <= 2 ? 'collection-code' : '' }}">{{ $part }}</div>
                        @endforeach
                        @if(empty($cnParts))
                        <div class="callnumber-line">-</div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="cut-guide">
        ✂️ Potong mengikuti garis tepi label. Gunakan penggaris untuk hasil yang rapi.
    </div>
</body>
</html>
