<!DOCTYPE html>
<html>
<head>
    <title>Cetak Kartu Anggota</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Libre+Barcode+39&display=swap" rel="stylesheet">
    <style>
        @page { 
            margin: 8mm; 
            size: auto;
        }
        
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
        }
        
        body { 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f1f5f9;
            min-height: 100vh;
        }
        
        .toolbar {
            padding: 16px 24px;
            background: linear-gradient(135deg, #1e3a5f 0%, #0f172a 100%);
            display: flex;
            align-items: center;
            gap: 16px;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }
        
        .toolbar-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }
        
        .toolbar-btn.primary {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(16,185,129,0.4);
        }
        
        .toolbar-btn.primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16,185,129,0.5);
        }
        
        .toolbar-btn.secondary {
            background: rgba(255,255,255,0.1);
            color: white;
            border: 1px solid rgba(255,255,255,0.2);
        }
        
        .toolbar-btn.secondary:hover {
            background: rgba(255,255,255,0.2);
        }
        
        .toolbar-info {
            color: rgba(255,255,255,0.8);
            font-size: 14px;
            margin-left: auto;
        }
        
        .cards-container {
            display: flex;
            flex-wrap: wrap;
            gap: 24px;
            padding: 32px;
            justify-content: center;
        }
        
        /* Card Design - Credit Card Size 85.6mm x 53.98mm */
        .card {
            width: 85.6mm;
            height: 53.98mm;
            border-radius: 4mm;
            page-break-inside: avoid;
            position: relative;
            overflow: hidden;
            box-shadow: 
                0 10px 40px rgba(0,0,0,0.15),
                0 2px 10px rgba(0,0,0,0.1),
                inset 0 1px 0 rgba(255,255,255,0.1);
        }
        
        /* Front Side */
        .card-front {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #0d9488 100%);
            padding: 4mm;
            display: flex;
            flex-direction: column;
            position: relative;
        }
        
        /* Decorative Elements */
        .card-front::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -30%;
            width: 80%;
            height: 150%;
            background: radial-gradient(ellipse, rgba(255,255,255,0.08) 0%, transparent 70%);
            pointer-events: none;
        }
        
        .card-front::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3mm;
            background: linear-gradient(90deg, #10b981, #06b6d4, #8b5cf6, #ec4899);
        }
        
        /* Header */
        .card-header {
            display: flex;
            align-items: center;
            gap: 2.5mm;
            margin-bottom: 2mm;
        }
        
        .card-logo {
            width: 8mm;
            height: 8mm;
            border-radius: 2mm;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
        }
        
        .card-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        
        .card-title {
            flex: 1;
        }
        
        .card-title h1 {
            font-size: 9pt;
            font-weight: 700;
            color: white;
            letter-spacing: 0.5px;
            line-height: 1.2;
        }
        
        .card-title p {
            font-size: 6pt;
            color: rgba(255,255,255,0.7);
            font-weight: 400;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        
        .card-chip {
            width: 8mm;
            height: 6mm;
            background: linear-gradient(135deg, #fbbf24, #d97706);
            border-radius: 1mm;
            position: relative;
            overflow: hidden;
        }
        
        .card-chip::before {
            content: '';
            position: absolute;
            inset: 1mm;
            border: 0.3mm solid rgba(0,0,0,0.2);
            border-radius: 0.5mm;
        }
        
        /* Body */
        .card-body {
            display: flex;
            gap: 3mm;
            flex: 1;
            position: relative;
            z-index: 1;
        }
        
        .photo-container {
            width: 18mm;
            height: 23mm;
            border-radius: 2mm;
            overflow: hidden;
            background: white;
            border: 0.5mm solid rgba(255,255,255,0.3);
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        
        .photo-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .photo-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #e2e8f0, #cbd5e1);
            color: #64748b;
            font-size: 6pt;
            font-weight: 500;
        }
        
        .photo-placeholder svg {
            width: 8mm;
            height: 8mm;
            margin-bottom: 1mm;
            opacity: 0.5;
        }
        
        .member-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .member-name {
            font-size: 9pt;
            font-weight: 700;
            color: white;
            margin-bottom: 1.5mm;
            line-height: 1.2;
            text-shadow: 0 1px 2px rgba(0,0,0,0.2);
        }
        
        .member-id {
            font-size: 11pt;
            font-weight: 600;
            color: #10b981;
            letter-spacing: 1px;
            margin-bottom: 2mm;
            font-family: 'Courier New', monospace;
        }
        
        .member-details {
            font-size: 6.5pt;
            color: rgba(255,255,255,0.8);
            line-height: 1.5;
        }
        
        .member-details span {
            display: block;
        }
        
        .member-details .label {
            color: rgba(255,255,255,0.5);
            font-size: 5.5pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        /* Footer */
        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            padding-top: 2mm;
            position: relative;
            z-index: 1;
        }
        
        .barcode-container {
            background: white;
            padding: 1mm 2mm;
            border-radius: 1mm;
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        }
        
        .barcode {
            font-family: 'Libre Barcode 39', monospace;
            font-size: 18pt;
            color: #0f172a;
            line-height: 1;
        }
        
        .barcode-text {
            font-family: 'Courier New', monospace;
            font-size: 5pt;
            color: #64748b;
            text-align: center;
            margin-top: 0.3mm;
            letter-spacing: 1px;
        }
        
        .valid-info {
            text-align: right;
            font-size: 5.5pt;
            color: rgba(255,255,255,0.7);
        }
        
        .valid-info .date {
            font-size: 7pt;
            color: white;
            font-weight: 600;
        }
        
        /* Print Styles */
        @media print {
            .toolbar { display: none !important; }
            
            body { 
                background: white;
                -webkit-print-color-adjust: exact !important; 
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            
            .cards-container {
                padding: 0;
                gap: 5mm;
            }
            
            .card {
                box-shadow: none;
                border: 0.3mm solid #e2e8f0;
            }
        }
    </style>
</head>
<body>
    <div class="toolbar no-print">
        <button onclick="window.print()" class="toolbar-btn primary">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2M6 14h12v8H6z"/>
            </svg>
            Cetak Kartu
        </button>
        <button onclick="window.close()" class="toolbar-btn secondary">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M6 18L18 6M6 6l12 12"/>
            </svg>
            Tutup
        </button>
        <div class="toolbar-info">
            <strong>{{ $members->count() }}</strong> kartu siap dicetak
        </div>
    </div>

    <div class="cards-container">
        @foreach($members as $member)
        <div class="card">
            <div class="card-front">
                {{-- Header --}}
                <div class="card-header">
                    <div class="card-logo">
                        <img src="{{ asset('storage/logo-portal.png') }}" alt="Logo" onerror="this.parentElement.innerHTML='📚'">
                    </div>
                    <div class="card-title">
                        <h1>UNIDA Library</h1>
                        <p>Member Card</p>
                    </div>
                    <div class="card-chip"></div>
                </div>
                
                {{-- Body --}}
                <div class="card-body">
                    <div class="photo-container">
                        @if($member->photo)
                            <img src="{{ asset('storage/' . $member->photo) }}" alt="Foto">
                        @else
                            <div class="photo-placeholder">
                                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                                </svg>
                                FOTO
                            </div>
                        @endif
                    </div>
                    
                    <div class="member-info">
                        <div class="member-name">{{ Str::limit($member->name, 25) }}</div>
                        <div class="member-id">{{ $member->member_id }}</div>
                        <div class="member-details">
                            <span class="label">Tipe Anggota</span>
                            <span>{{ $member->memberType->name ?? 'Umum' }}</span>
                        </div>
                    </div>
                </div>
                
                {{-- Footer --}}
                <div class="card-footer">
                    <div class="barcode-container">
                        <div class="barcode">*{{ $member->member_id }}*</div>
                        <div class="barcode-text">{{ $member->member_id }}</div>
                    </div>
                    <div class="valid-info">
                        <span>Berlaku s/d</span>
                        <div class="date">{{ $member->expire_date?->format('d/m/Y') ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</body>
</html>
