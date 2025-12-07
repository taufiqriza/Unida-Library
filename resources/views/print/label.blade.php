<!DOCTYPE html>
<html>
<head>
    <title>Document Label Print Result</title>
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
            width: 8cm; 
            height: 3.3cm; 
            text-align: center; 
            margin: 0.05cm; 
            padding: 0; 
            border: 1px solid #000; 
            vertical-align: top;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .labelHeaderStyle { 
            background-color: #CCCCCC; 
            font-weight: bold; 
            padding: 5px; 
            margin-bottom: 5px; 
            width: 100%;
            box-sizing: border-box;
            font-size: 9pt;
        }
        .callNumberStyle {
            font-size: 14pt;
            font-weight: bold;
            line-height: 1.4;
            padding: 5px;
        }
        .no-print { margin-bottom: 15px; }
        .no-print a {
            padding: 8px 16px;
            margin-right: 10px;
            cursor: pointer;
            text-decoration: none;
            background: #4a5568;
            color: white;
            border: none;
            border-radius: 4px;
            display: inline-block;
        }
        @media print { 
            .no-print { display: none !important; } 
            body { margin: 0.5cm; }
        }
    </style>
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
                    
                    <div class="callNumberStyle">
                        @php
                            // Get call number from item or book
                            $callNumber = $item->call_number ?: $item->book?->call_number;
                            // Split by space and display each part on new line (SLiMS style)
                            $callNumber = preg_replace('!\s+!', ' ', trim($callNumber ?? ''));
                            $parts = explode(' ', $callNumber);
                        @endphp
                        @foreach($parts as $part)
                            {{ $part }}<br>
                        @endforeach
                    </div>
                </div>
            </td>
            @endforeach
        </tr>
        @endforeach
    </table>

    <script>
        window.onload = function() {
            // Uncomment to auto-print
            // window.print();
        }
    </script>
</body>
</html>
