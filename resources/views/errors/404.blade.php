<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan</title>
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
            opacity: 0.5;
            animation: blob 8s infinite;
        }
        
        @keyframes blob {
            0%, 100% { transform: translate(0, 0) scale(1); }
            25% { transform: translate(20px, -30px) scale(1.1); }
            50% { transform: translate(-20px, 20px) scale(0.9); }
            75% { transform: translate(30px, 10px) scale(1.05); }
        }
        
        .pattern-bg {
            background-image: 
                radial-gradient(circle at 25% 25%, rgba(59, 130, 246, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 75% 75%, rgba(30, 64, 175, 0.08) 0%, transparent 50%);
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-sky-50 pattern-bg overflow-hidden">
    {{-- Decorative Blobs --}}
    <div class="blob w-72 h-72 bg-blue-500 top-20 -left-20"></div>
    <div class="blob w-96 h-96 bg-sky-400 -bottom-32 right-20" style="animation-delay: -4s;"></div>
    <div class="blob w-64 h-64 bg-indigo-400 top-1/2 right-1/4" style="animation-delay: -2s;"></div>

    <div class="relative min-h-screen flex flex-col items-center justify-center px-4">
        {{-- Floating 404 --}}
        <div class="floating mb-8">
            <div class="relative">
                {{-- Book Stack Illustration --}}
                <div class="flex items-end justify-center gap-2 mb-6">
                    <div class="w-8 h-24 bg-gradient-to-b from-blue-400 to-blue-600 rounded-t-sm transform -rotate-6 shadow-lg"></div>
                    <div class="w-10 h-32 bg-gradient-to-b from-sky-400 to-sky-600 rounded-t-sm shadow-lg"></div>
                    <div class="w-8 h-20 bg-gradient-to-b from-indigo-400 to-indigo-600 rounded-t-sm transform rotate-6 shadow-lg"></div>
                    <div class="w-6 h-16 bg-gradient-to-b from-blue-300 to-blue-500 rounded-t-sm transform rotate-12 shadow-lg"></div>
                </div>
                
                {{-- 404 Number --}}
                <h1 class="text-[150px] md:text-[200px] font-extrabold gradient-text leading-none tracking-tighter">
                    404
                </h1>
                
                {{-- Magnifying glass icon --}}
                <div class="absolute -right-4 -top-4 w-20 h-20 bg-white rounded-full shadow-xl flex items-center justify-center border-4 border-blue-100">
                    <i class="fas fa-search text-3xl text-blue-500"></i>
                </div>
            </div>
        </div>

        {{-- Message --}}
        <div class="text-center max-w-lg mx-auto mb-10">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-4">
                Oops! Halaman Tidak Ditemukan
            </h2>
            <p class="text-gray-500 text-lg mb-2">
                Sepertinya halaman yang Anda cari sudah dipindahkan, dihapus, atau mungkin tidak pernah ada.
            </p>
            <p class="text-gray-400 text-sm">
                Jangan khawatir, mari kita bantu Anda kembali ke jalan yang benar.
            </p>
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-col sm:flex-row gap-4">
            <a href="/" 
               class="group inline-flex items-center justify-center gap-3 px-8 py-4 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-2xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
                <i class="fas fa-home text-lg group-hover:scale-110 transition-transform"></i>
                <span>Kembali ke Beranda</span>
            </a>
            
            <button onclick="history.back()" 
                    class="group inline-flex items-center justify-center gap-3 px-8 py-4 bg-white hover:bg-blue-50 text-blue-700 font-semibold rounded-2xl shadow-lg hover:shadow-xl border border-blue-200 transform hover:-translate-y-1 transition-all duration-300">
                <i class="fas fa-arrow-left text-lg group-hover:-translate-x-1 transition-transform"></i>
                <span>Halaman Sebelumnya</span>
            </button>
        </div>

        {{-- Quick Links --}}
        <div class="mt-16 text-center">
            <p class="text-gray-400 text-sm mb-4">Atau kunjungi halaman populer:</p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="/search" class="px-4 py-2 bg-white/80 hover:bg-white text-gray-600 hover:text-blue-600 rounded-xl text-sm font-medium border border-blue-100 hover:border-blue-300 transition-all">
                    <i class="fas fa-search mr-2"></i>Pencarian
                </a>
                <a href="/shamela" class="px-4 py-2 bg-white/80 hover:bg-white text-gray-600 hover:text-blue-600 rounded-xl text-sm font-medium border border-blue-100 hover:border-blue-300 transition-all">
                    <i class="fas fa-book-quran mr-2"></i>Shamela
                </a>
                <a href="/e-resources" class="px-4 py-2 bg-white/80 hover:bg-white text-gray-600 hover:text-blue-600 rounded-xl text-sm font-medium border border-blue-100 hover:border-blue-300 transition-all">
                    <i class="fas fa-globe mr-2"></i>E-Resources
                </a>
                <a href="/login" class="px-4 py-2 bg-white/80 hover:bg-white text-gray-600 hover:text-blue-600 rounded-xl text-sm font-medium border border-blue-100 hover:border-blue-300 transition-all">
                    <i class="fas fa-sign-in-alt mr-2"></i>Login
                </a>
            </div>
        </div>

        {{-- Footer --}}
        <div class="absolute bottom-8 text-center">
            <p class="text-gray-400 text-xs">
                © {{ date('Y') }} {{ config('app.name', 'Perpustakaan') }}. All rights reserved.
            </p>
        </div>
    </div>

    {{-- Particles Effect --}}
    <script>
        // Create floating particles
        const particleCount = 20;
        const container = document.body;
        
        for (let i = 0; i < particleCount; i++) {
            const particle = document.createElement('div');
            particle.className = 'absolute w-2 h-2 bg-blue-400/30 rounded-full';
            particle.style.left = Math.random() * 100 + 'vw';
            particle.style.top = Math.random() * 100 + 'vh';
            particle.style.animationDuration = (3 + Math.random() * 4) + 's';
            particle.style.animationDelay = Math.random() * 2 + 's';
            particle.style.animation = `floating ${3 + Math.random() * 4}s ease-in-out infinite`;
            container.appendChild(particle);
        }
    </script>
</body>
</html>
