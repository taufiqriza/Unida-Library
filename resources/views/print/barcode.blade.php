<!DOCTYPE html>
<html>
<head>
    <title>Item Barcode Print Result</title>
    <meta charset="utf-8">
    <style>
        @page { margin: 0.5cm; }
        * { box-sizing: border-box; }
        body { 
            padding: 0; 
            margin: 0.5cm; 
            font-family: Arial, Helvetica, sans-serif; 
            font-size: 10pt; 
            background: #fff; 
        }
        
        .labels-container {
            display: flex;
            flex-wrap: wrap;
            gap: 0.2cm;
        }
        
        .label-card {
            width: 9cm;
            height: 3.5cm;
            border: 1px solid #333;
            border-radius: 4px;
            display: flex;
            overflow: hidden;
            page-break-inside: avoid;
        }
        
        /* Left side - Barcode */
        .barcode-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 0.2cm;
            border-right: 1px solid #ccc;
        }
        
        .title-text {
            font-size: 7pt;
            font-style: italic;
            text-align: center;
            margin-bottom: 0.15cm;
            max-height: 1.2em;
            overflow: hidden;
            width: 100%;
        }
        
        .barcode-image {
            font-family: 'Libre Barcode 39', 'Free 3 of 9', monospace;
            font-size: 42pt;
            line-height: 1;
            letter-spacing: -2px;
        }
        
        .barcode-text {
            font-size: 9pt;
            font-weight: bold;
            margin-top: 0.1cm;
            letter-spacing: 1px;
        }
        
        /* Right side - Call Number */
        .callnumber-section {
            width: 3cm;
            display: flex;
            flex-direction: column;
            padding: 0.15cm;
        }
        
        .library-name {
            font-size: 8pt;
            font-weight: bold;
            text-align: center;
            padding-bottom: 0.1cm;
            border-bottom: 1px solid #333;
            margin-bottom: 0.1cm;
        }
        
        .callnumber-parts {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 0.05cm;
        }
        
        .callnumber-line {
            font-size: 11pt;
            font-weight: bold;
            text-align: left;
            padding-left: 0.2cm;
        }
        
        .callnumber-line.collection-code {
            font-size: 10pt;
        }
        
        .callnumber-line.classification {
            font-size: 12pt;
        }
        
        .callnumber-line.author-code {
            font-size: 11pt;
        }
        
        .callnumber-line.title-code {
            font-size: 11pt;
            text-transform: lowercase;
        }
        
        /* Print controls */
        .no-print { 
            margin-bottom: 15px; 
            padding: 10px;
            background: #f3f4f6;
            border-radius: 8px;
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .no-print a, .no-print button {
            padding: 8px 16px;
            cursor: pointer;
            text-decoration: none;
            background: #4f46e5;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .no-print a:hover, .no-print button:hover {
            background: #4338ca;
        }
        .no-print .btn-secondary {
            background: #6b7280;
        }
        .no-print .btn-secondary:hover {
            background: #4b5563;
        }
        
        @media print { 
            .no-print { display: none !important; } 
            body { margin: 0.3cm; }
            .label-card { border: 1px solid #000; }
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Barcode+39&display=swap" rel="stylesheet">
</head>
<body>
    <div class="no-print">
        <a href="#" onclick="window.print(); return false;">🖨️ Cetak</a>
        <a href="#" onclick="window.close(); return false;" class="btn-secondary">✖️ Tutup</a>
        <span style="color: #6b7280; font-size: 13px;">{{ $items->count() }} label</span>
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
            
            // Parse call number atau generate dari data
            if ($callNumber && str_contains($callNumber, "\n")) {
                $parts = CallNumberService::parse($callNumber);
            } else {
                // Fallback: split by space
                $cnParts = $callNumber ? preg_split('/\s+/', trim($callNumber)) : [];
                $parts = [
                    'collection_code' => $cnParts[0] ?? ($item->collectionType?->code ?? ''),
                    'classification' => $cnParts[1] ?? ($book?->classification ?? ''),
                    'author_code' => $cnParts[2] ?? '',
                    'title_code' => $cnParts[3] ?? '',
                ];
                
                // Generate author & title code if missing
                if (empty($parts['author_code']) && $book) {
                    $authorName = $book->authors->first()?->name ?? $book->sor ?? '';
                    $parts['author_code'] = CallNumberService::getAuthorCode($authorName);
                }
                if (empty($parts['title_code']) && $book) {
                    $parts['title_code'] = CallNumberService::getTitleCode($book->title);
                }
            }
        @endphp
        <div class="label-card">
            {{-- Left: Barcode --}}
            <div class="barcode-section">
                <div class="title-text">{{ Str::limit($book?->title ?? 'No Title', 40) }}</div>
                <div class="barcode-image">*{{ $item->barcode }}*</div>
                <div class="barcode-text">{{ $item->barcode }}</div>
            </div>
            
            {{-- Right: Call Number --}}
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
