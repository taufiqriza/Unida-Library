<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>419 - Sesi Berakhir</title>
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
            background: linear-gradient(135deg, #1E40AF 0%, #3B82F6 50%, #60A5FA 100%);
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

        .pulse-ring {
            animation: pulse-ring 2s ease-out infinite;
        }
        
        @keyframes pulse-ring {
            0% { transform: scale(0.8); opacity: 1; }
            100% { transform: scale(1.5); opacity: 0; }
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-sky-50 overflow-hidden">
    {{-- Decorative Blobs --}}
    <div class="blob w-72 h-72 bg-blue-500 top-20 -left-20"></div>
    <div class="blob w-96 h-96 bg-sky-400 -bottom-32 right-20" style="animation-delay: -4s;"></div>

    <div class="relative min-h-screen flex flex-col items-center justify-center px-4">
        {{-- Clock Animation --}}
        <div class="floating mb-8">
            <div class="relative">
                {{-- Clock Icon with Pulse --}}
                <div class="flex items-center justify-center mb-6">
                    <div class="relative">
                        <div class="absolute inset-0 w-32 h-32 bg-blue-400 rounded-full pulse-ring"></div>
                        <div class="relative w-32 h-32 bg-gradient-to-br from-blue-500 to-blue-700 rounded-full flex items-center justify-center shadow-2xl">
                            <i class="fas fa-clock text-5xl text-white"></i>
                        </div>
                    </div>
                </div>
                
                {{-- 419 Number --}}
                <h1 class="text-[150px] md:text-[200px] font-extrabold gradient-text leading-none tracking-tighter">
                    419
                </h1>
                
                {{-- Hourglass icon --}}
                <div class="absolute -right-4 -top-4 w-20 h-20 bg-white rounded-full shadow-xl flex items-center justify-center border-4 border-blue-100">
                    <i class="fas fa-hourglass-end text-3xl text-blue-500"></i>
                </div>
            </div>
        </div>

        {{-- Message --}}
        <div class="text-center max-w-lg mx-auto mb-10">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-4">
                Sesi Anda Telah Berakhir
            </h2>
            <p class="text-gray-500 text-lg mb-2">
                Sesi Anda telah berakhir karena tidak ada aktivitas. Silakan refresh halaman atau login kembali untuk melanjutkan.
            </p>
            <p class="text-gray-400 text-sm">
                Hal ini dilakukan untuk menjaga keamanan akun Anda.
            </p>
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-col sm:flex-row gap-4">
            <button onclick="location.reload()" 
                    class="group inline-flex items-center justify-center gap-3 px-8 py-4 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-2xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
                <i class="fas fa-rotate-right text-lg group-hover:rotate-180 transition-transform duration-500"></i>
                <span>Refresh Halaman</span>
            </button>
            
            <a href="/login" 
               class="group inline-flex items-center justify-center gap-3 px-8 py-4 bg-white hover:bg-blue-50 text-blue-700 font-semibold rounded-2xl shadow-lg hover:shadow-xl border border-blue-200 transform hover:-translate-y-1 transition-all duration-300">
                <i class="fas fa-sign-in-alt text-lg group-hover:translate-x-1 transition-transform"></i>
                <span>Login Kembali</span>
            </a>
        </div>

        {{-- Footer --}}
        <div class="absolute bottom-8 text-center">
            <p class="text-gray-400 text-xs">
                © {{ date('Y') }} Beri IT Perpustakaan UNIDA Gontor. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
