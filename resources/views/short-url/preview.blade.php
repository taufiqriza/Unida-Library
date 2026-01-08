<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $shortUrl->title ?: 'Link Preview' }} - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <!-- Header -->
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-external-link-alt text-blue-600 text-2xl"></i>
                </div>
                <h1 class="text-xl font-bold text-gray-800">Link Preview</h1>
                <p class="text-gray-600 text-sm mt-1">Anda akan diarahkan ke link eksternal</p>
            </div>

            <!-- Link Info -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                @if($shortUrl->title)
                    <h2 class="font-semibold text-gray-800 mb-2">{{ $shortUrl->title }}</h2>
                @endif
                
                @if($shortUrl->description)
                    <p class="text-gray-600 text-sm mb-3">{{ $shortUrl->description }}</p>
                @endif

                <div class="flex items-center text-sm text-gray-500 mb-2">
                    <i class="fas fa-link mr-2"></i>
                    <span class="break-all">{{ Str::limit($shortUrl->original_url, 50) }}</span>
                </div>

                <div class="flex items-center text-sm text-gray-500">
                    <i class="fas fa-eye mr-2"></i>
                    <span>{{ $shortUrl->clicks }} kali diklik</span>
                </div>
            </div>

            <!-- Actions -->
            <div class="space-y-3">
                <a href="{{ route('short.redirect', $shortUrl->code) }}" 
                   class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center">
                    <i class="fas fa-external-link-alt mr-2"></i>
                    Buka Link
                </a>

                <button onclick="copyLink()" 
                        class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center">
                    <i class="fas fa-copy mr-2"></i>
                    Salin Link
                </button>
            </div>

            <!-- Footer -->
            <div class="text-center mt-6 pt-4 border-t border-gray-200">
                <p class="text-xs text-gray-500">
                    Powered by <span class="font-semibold">{{ config('app.name') }}</span>
                </p>
            </div>
        </div>
    </div>

    <script>
        function copyLink() {
            navigator.clipboard.writeText('{{ $shortUrl->original_url }}').then(() => {
                const btn = event.target.closest('button');
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check mr-2"></i>Tersalin!';
                btn.classList.add('bg-green-100', 'text-green-700');
                btn.classList.remove('bg-gray-100', 'text-gray-700');
                
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.classList.remove('bg-green-100', 'text-green-700');
                    btn.classList.add('bg-gray-100', 'text-gray-700');
                }, 2000);
            });
        }
    </script>
</body>
</html>
