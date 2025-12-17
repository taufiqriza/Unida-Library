<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Kesalahan Server</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * { font-family: 'Inter', sans-serif; }
        
        .floating {
            animation: floating 3s ease-in-out infinite;
        }
        
        @keyframes floating {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .gradient-text {
            background: linear-gradient(135deg, #EF4444 0%, #F97316 50%, #FBBF24 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.4;
            animation: blob 8s infinite;
        }
        
        @keyframes blob {
            0%, 100% { transform: translate(0, 0) scale(1); }
            25% { transform: translate(20px, -30px) scale(1.1); }
            50% { transform: translate(-20px, 20px) scale(0.9); }
            75% { transform: translate(30px, 10px) scale(1.05); }
        }

        .gear {
            animation: spin 4s linear infinite;
        }
        
        .gear-reverse {
            animation: spin-reverse 3s linear infinite;
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        @keyframes spin-reverse {
            from { transform: rotate(360deg); }
            to { transform: rotate(0deg); }
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-red-50 via-white to-orange-50 overflow-hidden">
    {{-- Decorative Blobs --}}
    <div class="blob w-72 h-72 bg-red-400 top-20 -left-20"></div>
    <div class="blob w-96 h-96 bg-orange-400 -bottom-32 right-20" style="animation-delay: -4s;"></div>

    <div class="relative min-h-screen flex flex-col items-center justify-center px-4">
        {{-- Gears Animation --}}
        <div class="floating mb-8">
            <div class="relative">
                {{-- Broken Gears --}}
                <div class="flex items-center justify-center mb-6">
                    <i class="fas fa-cog gear text-6xl text-red-300 absolute -left-8 -top-4"></i>
                    <i class="fas fa-cog gear-reverse text-8xl text-orange-300"></i>
                    <i class="fas fa-cog gear text-5xl text-red-400 absolute -right-6 top-4"></i>
                </div>
                
                {{-- 500 Number --}}
                <h1 class="text-[150px] md:text-[200px] font-extrabold gradient-text leading-none tracking-tighter">
                    500
                </h1>
                
                {{-- Warning icon --}}
                <div class="absolute -right-4 -top-4 w-20 h-20 bg-white rounded-full shadow-xl flex items-center justify-center border-4 border-red-100">
                    <i class="fas fa-exclamation-triangle text-3xl text-red-500"></i>
                </div>
            </div>
        </div>

        {{-- Message --}}
        <div class="text-center max-w-lg mx-auto mb-10">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-4">
                Oops! Terjadi Kesalahan Server
            </h2>
            <p class="text-gray-500 text-lg mb-2">
                Sepertinya ada yang tidak beres di sisi kami. Tim teknis sudah diberitahu dan sedang memperbaiki masalah ini.
            </p>
            <p class="text-gray-400 text-sm">
                Silakan coba lagi dalam beberapa saat.
            </p>
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-col sm:flex-row gap-4">
            <a href="/" 
               class="group inline-flex items-center justify-center gap-3 px-8 py-4 bg-gradient-to-r from-red-500 to-orange-500 hover:from-red-600 hover:to-orange-600 text-white font-semibold rounded-2xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
                <i class="fas fa-home text-lg group-hover:scale-110 transition-transform"></i>
                <span>Kembali ke Beranda</span>
            </a>
            
            <button onclick="location.reload()" 
                    class="group inline-flex items-center justify-center gap-3 px-8 py-4 bg-white hover:bg-gray-50 text-gray-700 font-semibold rounded-2xl shadow-lg hover:shadow-xl border border-gray-200 transform hover:-translate-y-1 transition-all duration-300">
                <i class="fas fa-rotate-right text-lg group-hover:rotate-180 transition-transform duration-500"></i>
                <span>Coba Lagi</span>
            </button>
        </div>

        {{-- Footer --}}
        <div class="absolute bottom-8 text-center">
            <p class="text-gray-400 text-xs">
                © {{ date('Y') }} {{ config('app.name', 'Perpustakaan') }}. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
