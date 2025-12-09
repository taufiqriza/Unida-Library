<!DOCTYPE html>
<html>
<head>
    <title>Cetak Label</title>
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
            gap: 0.25cm;
        }
        
        .label-card {
            width: 3cm;
            height: 4cm;
            background: #fff;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            page-break-inside: avoid;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }
        
        .library-name {
            font-size: 6pt;
            font-weight: 700;
            text-align: center;
            padding: 0.15cm 0.1cm;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }
        
        .callnumber-parts {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 0.2cm;
            gap: 0.08cm;
            background: linear-gradient(180deg, #fafafa 0%, #fff 100%);
        }
        
        .callnumber-line {
            font-weight: 700;
            text-align: center;
            color: #1e293b;
            line-height: 1.15;
        }
        
        .callnumber-line.collection-code {
            font-size: 14pt;
            color: #667eea;
        }
        
        .callnumber-line.classification {
            font-size: 13pt;
        }
        
        .callnumber-line.author-code {
            font-size: 12pt;
        }
        
        .callnumber-line.title-code {
            font-size: 12pt;
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
            .library-name { 
                background: #333; 
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .callnumber-parts { background: #fff; }
            .callnumber-line.collection-code { color: #333; }
        }
    </style>
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
        @endforeach
    </div>
</body>
</html>
