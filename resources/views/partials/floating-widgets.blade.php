@php
    $whatsapp = \App\Models\Setting::get('contact_whatsapp');
    $appName = \App\Models\Setting::get('app_name', 'Perpustakaan UNIDA');
    
    // Format nomor ke internasional (62)
    $waNumber = preg_replace('/[^0-9]/', '', $whatsapp);
    if (str_starts_with($waNumber, '0')) {
        $waNumber = '62' . substr($waNumber, 1);
    } elseif (!str_starts_with($waNumber, '62') && $waNumber) {
        $waNumber = '62' . $waNumber;
    }
@endphp

{{-- WhatsApp Floating Widget --}}
@if($whatsapp)
<div id="wa-widget" x-data="{ open: false }">
    <style>
        #wa-widget { 
            position: fixed; 
            bottom: 24px; 
            right: 20px; 
            z-index: 99998; 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
        }
        @media (max-width: 1023px) {
            #wa-widget { bottom: 100px; }
        }
        #wa-btn { 
            width: 56px; 
            height: 56px; 
            border-radius: 16px; 
            background: linear-gradient(135deg, #25D366 0%, #128C7E 100%); 
            border: none; 
            cursor: pointer; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            box-shadow: 0 4px 20px rgba(37, 211, 102, 0.4); 
            transition: all 0.3s ease; 
            position: relative; 
        }
        #wa-btn:hover { 
            transform: translateY(-3px) scale(1.05); 
            box-shadow: 0 8px 30px rgba(37, 211, 102, 0.5); 
        }
        #wa-btn svg { 
            width: 28px; 
            height: 28px; 
            fill: #fff; 
            transition: all 0.3s ease; 
        }
        #wa-btn .close-icon { 
            position: absolute; 
            opacity: 0; 
            transform: rotate(-90deg); 
        }
        #wa-btn.active { 
            background: linear-gradient(135deg, #374151 0%, #1f2937 100%);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }
        #wa-btn.active .wa-icon { opacity: 0; transform: rotate(90deg); }
        #wa-btn.active .close-icon { opacity: 1; transform: rotate(0); }
        #wa-pulse { 
            position: absolute; 
            inset: 0; 
            border-radius: 16px; 
            background: linear-gradient(135deg, #25D366 0%, #128C7E 100%); 
            animation: wa-ping 2s cubic-bezier(0,0,0.2,1) infinite; 
        }
        @keyframes wa-ping { 
            0% { transform: scale(1); opacity: 0.5; } 
            100% { transform: scale(1.6); opacity: 0; } 
        }
        #wa-popup { 
            position: absolute; 
            bottom: 70px; 
            right: 0; 
            width: 340px; 
            background: #fff; 
            border-radius: 20px; 
            box-shadow: 0 15px 50px rgba(0,0,0,0.2); 
            overflow: hidden; 
            opacity: 0; 
            visibility: hidden; 
            transform: translateY(20px) scale(0.95); 
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        #wa-popup.active { 
            opacity: 1; 
            visibility: visible; 
            transform: translateY(0) scale(1); 
        }
        #wa-header { 
            background: linear-gradient(135deg, #25D366 0%, #128C7E 100%); 
            color: #fff; 
            padding: 20px; 
            display: flex; 
            align-items: center; 
            gap: 14px; 
        }
        #wa-avatar { 
            width: 50px; 
            height: 50px; 
            background: rgba(255,255,255,0.2); 
            border-radius: 50%; 
            display: flex; 
            align-items: center; 
            justify-content: center;
            backdrop-filter: blur(4px);
        }
        #wa-avatar svg { width: 26px; height: 26px; fill: #fff; }
        #wa-info h4 { margin: 0; font-size: 15px; font-weight: 600; color: #fff; }
        #wa-info p { margin: 5px 0 0; font-size: 12px; color: rgba(255,255,255,0.85); display: flex; align-items: center; gap: 6px; }
        #wa-status { width: 8px; height: 8px; background: #fff; border-radius: 50%; animation: pulse-status 2s infinite; }
        @keyframes pulse-status {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        #wa-body { 
            padding: 24px; 
            background: linear-gradient(180deg, #f0f2f5 0%, #e4e6eb 100%); 
        }
        #wa-bubble { 
            background: #fff; 
            padding: 14px 18px; 
            border-radius: 0 16px 16px 16px; 
            box-shadow: 0 2px 8px rgba(0,0,0,0.08); 
            max-width: 90%; 
            position: relative; 
        }
        #wa-bubble::before { 
            content: ''; 
            position: absolute; 
            left: -8px; 
            top: 0; 
            border-width: 0 8px 8px 0; 
            border-style: solid; 
            border-color: transparent #fff transparent transparent; 
        }
        #wa-bubble p { margin: 0; font-size: 14px; color: #303030; line-height: 1.6; }
        #wa-bubble span { display: block; text-align: right; font-size: 11px; color: #8696a0; margin-top: 8px; }
        #wa-footer { 
            padding: 20px; 
            background: #fff; 
            border-top: 1px solid #e5e7eb; 
        }
        #wa-cta { 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            gap: 10px; 
            width: 100%; 
            padding: 16px; 
            background: linear-gradient(135deg, #25D366 0%, #128C7E 100%); 
            color: #fff; 
            border: none; 
            border-radius: 14px; 
            font-size: 15px; 
            font-weight: 600; 
            cursor: pointer; 
            transition: all 0.3s ease; 
            text-decoration: none; 
            box-shadow: 0 4px 15px rgba(37, 211, 102, 0.3);
        }
        #wa-cta:hover { 
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(37, 211, 102, 0.4);
        }
        #wa-cta svg { width: 20px; height: 20px; fill: #fff; }
        @media (max-width: 400px) { 
            #wa-popup { width: calc(100vw - 40px); right: 0; } 
        }
    </style>

    <!-- Popup -->
    <div id="wa-popup" :class="{ 'active': open }">
        <div id="wa-header">
            <div id="wa-avatar">
                <svg viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            </div>
            <div id="wa-info">
                <h4>{{ $appName }}</h4>
                <p><span id="wa-status"></span>Online - Siap membantu</p>
            </div>
        </div>
        <div id="wa-body">
            <div id="wa-bubble">
                <p>Halo! 👋<br>Selamat datang di {{ $appName }}. Ada yang bisa kami bantu hari ini?</p>
                <span>{{ now()->format('H:i') }}</span>
            </div>
        </div>
        <div id="wa-footer">
            <a id="wa-cta" href="https://wa.me/{{ $waNumber }}?text={{ urlencode('Halo ' . $appName . ', saya ingin bertanya tentang...') }}" target="_blank">
                <svg viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                Mulai Chat
            </a>
        </div>
    </div>

    <!-- Floating Button -->
    <button id="wa-btn" @click="open = !open" :class="{ 'active': open }">
        <span id="wa-pulse" x-show="!open"></span>
        <svg class="wa-icon" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
        <svg class="close-icon" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" fill="#fff"/></svg>
    </button>
</div>
@endif

{{-- Scroll to Top Button --}}
<div id="scroll-top-widget">
    <style>
        #scroll-top-btn {
            position: fixed;
            visibility: hidden;
            opacity: 0;
            right: 20px;
            bottom: 94px;
            z-index: 99997;
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            width: 48px;
            height: 48px;
            border-radius: 14px;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 20px rgba(59, 130, 246, 0.4);
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            transform: translateY(20px);
        }
        @media (max-width: 1023px) {
            #scroll-top-btn { bottom: 170px; }
        }
        #scroll-top-btn i {
            font-size: 22px;
            color: #fff;
            line-height: 0;
        }
        #scroll-top-btn:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(59, 130, 246, 0.5);
        }
        #scroll-top-btn.active {
            visibility: visible;
            opacity: 1;
            transform: translateY(0);
        }
    </style>
    
    <button id="scroll-top-btn" onclick="window.scrollTo({ top: 0, behavior: 'smooth' })">
        <i class="fas fa-arrow-up"></i>
    </button>
    
    <script>
        (function() {
            const scrollTopBtn = document.getElementById('scroll-top-btn');
            if (!scrollTopBtn) return;
            
            function toggleScrollTop() {
                if (window.scrollY > 300) {
                    scrollTopBtn.classList.add('active');
                } else {
                    scrollTopBtn.classList.remove('active');
                }
            }
            
            window.addEventListener('load', toggleScrollTop);
            document.addEventListener('scroll', toggleScrollTop, { passive: true });
        })();
    </script>
</div>
