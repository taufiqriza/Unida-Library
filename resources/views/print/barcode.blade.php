<!DOCTYPE html>
<html>
<head>
    <title>Item Barcode Print Result</title>
    <meta charset="utf-8">
    <style>
        @page { margin: 1cm; }
        body { 
            padding: 0; 
            margin: 1cm; 
            font-family: Arial, Verdana, Helvetica, 'Trebuchet MS'; 
            font-size: 11pt; 
            background: #fff; 
        }
        table { margin: 0; padding: 0; border-collapse: collapse; }
        .labelStyle { 
            width: 7cm; 
            height: 5cm; 
            text-align: center; 
            margin: 0.1cm; 
            border: 1px solid #000; 
            vertical-align: top;
            padding: 3px;
            box-sizing: border-box;
        }
        .labelHeaderStyle { 
            background-color: #CCCCCC; 
            font-weight: bold; 
            padding: 5px; 
            margin-bottom: 5px; 
            font-size: 9pt;
        }
        .titleStyle {
            font-size: 7pt;
            margin-bottom: 3px;
            height: 2em;
            overflow: hidden;
        }
        .barcodeImg {
            width: 70%;
            max-height: 2.5cm;
        }
        .barcodeText {
            font-family: 'Libre Barcode 39', 'Free 3 of 9', monospace;
            font-size: 36pt;
            line-height: 1;
        }
        .codeText {
            font-size: 9pt;
            font-weight: bold;
            margin-top: 2px;
        }
        .no-print { margin-bottom: 15px; }
        .no-print a, .no-print button {
            padding: 8px 16px;
            margin-right: 10px;
            cursor: pointer;
            text-decoration: none;
            background: #4a5568;
            color: white;
            border: none;
            border-radius: 4px;
        }
        @media print { 
            .no-print { display: none !important; } 
            body { margin: 0.5cm; }
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Barcode+39&display=swap" rel="stylesheet">
</head>
<body>
    <div class="no-print">
        <a href="#" onclick="window.print(); return false;">🖨️ Cetak</a>
        <a href="#" onclick="window.close(); return false;">✖️ Tutup</a>
    </div>

    @php
        $config = [
            'items_per_row' => 3,
            'include_header' => true,
            'header_text' => config('app.name', 'Perpustakaan'),
            'cut_title' => 50,
        ];
        $chunked = $items->chunk($config['items_per_row']);
    @endphp

    <table cellspacing="0" cellpadding="0">
        @foreach($chunked as $row)
        <tr>
            @foreach($row as $item)
            <td valign="top">
                <div class="labelStyle">
                    @if($config['include_header'])
                    <div class="labelHeaderStyle">{{ $config['header_text'] }}</div>
                    @endif
                    
                    <div class="titleStyle">
                        {{ Str::limit($item->book?->title, $config['cut_title']) }}
                    </div>
                    
                    <div class="barcodeText">*{{ $item->barcode }}*</div>
                    
                    <div class="codeText">{{ $item->barcode }}</div>
                </div>
            </td>
            @endforeach
        </tr>
        @endforeach
    </table>

    <script>
        // Auto print on load (like SLiMS)
        window.onload = function() {
            // Uncomment below to auto-print
            // window.print();
        }
    </script>
</body>
</html>
