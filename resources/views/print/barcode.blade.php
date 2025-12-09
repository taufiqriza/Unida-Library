<!DOCTYPE html>
<html>
<head>
    <title>Cetak Barcode</title>
    <meta charset="utf-8">
    <style>
        @page { margin: 0.5cm; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { 
            font-family: 'Segoe UI', Arial, sans-serif; 
            font-size: 10pt; 
            background: #f8fafc;
            padding: 0.5cm;
        }
        
        .labels-container {
            display: flex;
            flex-wrap: wrap;
            gap: 0.3cm;
        }
        
        .label-card {
            width: 9cm;
            height: 3.2cm;
            background: #fff;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            display: flex;
            overflow: hidden;
            page-break-inside: avoid;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }
        
        /* Left - Barcode */
        .barcode-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 0.25cm 0.3cm;
            background: linear-gradient(135deg, #fafafa 0%, #f1f5f9 100%);
        }
        
        .title-text {
            font-size: 7pt;
            color: #64748b;
            text-align: center;
            margin-bottom: 0.15cm;
            max-height: 1.3em;
            overflow: hidden;
            width: 100%;
            font-weight: 500;
        }
        
        .barcode-image {
            font-family: 'Libre Barcode 39', monospace;
            font-size: 40pt;
            line-height: 1;
            letter-spacing: -2px;
            color: #1e293b;
        }
        
        .barcode-text {
            font-size: 9pt;
            font-weight: 700;
            margin-top: 0.1cm;
            letter-spacing: 2px;
            color: #334155;
        }
        
        /* Right - Call Number */
        .callnumber-section {
            width: 2.8cm;
            display: flex;
            flex-direction: column;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 0 8px 8px 0;
        }
        
        .library-name {
            font-size: 6.5pt;
            font-weight: 600;
            text-align: center;
            padding: 0.15cm 0.1cm;
            background: rgba(0,0,0,0.15);
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        
        .callnumber-parts {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 0.1cm;
            gap: 0.02cm;
        }
        
        .callnumber-line {
            font-weight: 700;
            text-align: center;
            line-height: 1.2;
            text-shadow: 0 1px 2px rgba(0,0,0,0.2);
        }
        
        .callnumber-line.collection-code {
            font-size: 12pt;
        }
        
        .callnumber-line.classification {
            font-size: 11pt;
        }
        
        .callnumber-line.author-code {
            font-size: 11pt;
        }
        
        .callnumber-line.title-code {
            font-size: 11pt;
        }
        
        /* Print controls */
        .no-print { 
            margin-bottom: 0.4cm; 
            padding: 12px 16px;
            background: #fff;
            border-radius: 10px;
            display: flex;
            gap: 10px;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .no-print button {
            padding: 8px 18px;
            cursor: pointer;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: transform 0.15s, box-shadow 0.15s;
        }
        .no-print button:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(102,126,234,0.4);
        }
        .no-print .btn-secondary {
            background: #64748b;
        }
        .no-print .btn-secondary:hover {
            background: #475569;
            box-shadow: 0 4px 12px rgba(100,116,139,0.4);
        }
        .no-print .label-count {
            color: #64748b;
            font-size: 13px;
            margin-left: auto;
        }
        
        @media print { 
            .no-print { display: none !important; } 
            body { background: #fff; padding: 0.3cm; }
            .label-card { 
                border: 1px solid #333; 
                box-shadow: none;
            }
            .barcode-section { background: #fff; }
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Barcode+39&display=swap" rel="stylesheet">
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()">🖨️ Cetak</button>
        <button onclick="window.close()" class="btn-secondary">✕ Tutup</button>
        <span class="label-count">{{ $items->count() }} label</span>
    </div>

    @php
        use App\Services\CallNumberService;
        $libraryName = config('app.name', 'UNIDA Gontor');
    @endphp

    <div class="labels-container">
        @foreach($items as $item)
        @php
            $book = $item->book;
            $callNumber = $item->call_number ?: $book?->call_number;
            
            // Parse call number (format: S 827 TAR Z)
            $cnParts = $callNumber ? preg_split('/\s+/', trim($callNumber)) : [];
            $parts = [
                'collection_code' => $cnParts[0] ?? ($item->collectionType?->code ?? 'S'),
                'classification' => $cnParts[1] ?? ($book?->classification ?? ''),
                'author_code' => $cnParts[2] ?? '',
                'title_code' => $cnParts[3] ?? '',
            ];
            
            // Generate if missing
            if (empty($parts['author_code']) && $book) {
                $parts['author_code'] = CallNumberService::getAuthorCode($book->sor ?? $book->authors->first()?->name ?? '');
            }
            if (empty($parts['title_code']) && $book) {
                $parts['title_code'] = CallNumberService::getTitleCode($book->title);
            }
        @endphp
        <div class="label-card">
            <div class="barcode-section">
                <div class="title-text">{{ Str::limit($book?->title ?? 'No Title', 45) }}</div>
                <div class="barcode-image">*{{ $item->barcode }}*</div>
                <div class="barcode-text">{{ $item->barcode }}</div>
            </div>
            
            <div class="callnumber-section">
                <div class="library-name">{{ $libraryName }}</div>
                <div class="callnumber-parts">
                    @if($parts['collection_code'])
                    <div class="callnumber-line collection-code">{{ $parts['collection_code'] }}</div>
                    @endif
                    @if($parts['classification'])
                    <div class="callnumber-line classification">{{ $parts['classification'] }}</div>
                    @endif
                    @if($parts['author_code'])
                    <div class="callnumber-line author-code">{{ $parts['author_code'] }}</div>
                    @endif
                    @if($parts['title_code'])
                    <div class="callnumber-line title-code">{{ $parts['title_code'] }}</div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</body>
</html>
