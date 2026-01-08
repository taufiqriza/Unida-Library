<x-opac.layout :title="$shortUrl->title ?: 'Link Preview'">
    <div class="min-h-screen bg-gray-50 py-12">
        <div class="max-w-2xl mx-auto px-4">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-6">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-external-link-alt text-white text-2xl"></i>
                        </div>
                        <h1 class="text-2xl font-bold text-white">Link Preview</h1>
                        <p class="text-blue-100 mt-2">Anda akan diarahkan ke link eksternal</p>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-8">
                    <!-- Link Info Card -->
                    <div class="bg-gray-50 rounded-xl p-6 mb-8">
                        @if($shortUrl->title)
                            <h2 class="text-xl font-bold text-gray-800 mb-3">{{ $shortUrl->title }}</h2>
                        @endif
                        
                        @if($shortUrl->description)
                            <p class="text-gray-600 mb-4 leading-relaxed">{{ $shortUrl->description }}</p>
                        @endif

                        <div class="space-y-3">
                            <div class="flex items-center text-sm text-gray-500">
                                <i class="fas fa-link mr-3 text-blue-500"></i>
                                <span class="break-all font-mono bg-white px-2 py-1 rounded border">{{ Str::limit($shortUrl->original_url, 60) }}</span>
                            </div>

                            <div class="flex items-center text-sm text-gray-500">
                                <i class="fas fa-eye mr-3 text-green-500"></i>
                                <span>{{ number_format($shortUrl->clicks) }} kali diklik</span>
                            </div>

                            <div class="flex items-center text-sm text-gray-500">
                                <i class="fas fa-calendar mr-3 text-purple-500"></i>
                                <span>Dibuat {{ $shortUrl->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="space-y-4">
                        <a href="{{ route('short.redirect', $shortUrl->code) }}" 
                           class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold py-4 px-6 rounded-xl transition duration-200 flex items-center justify-center text-lg shadow-lg">
                            <i class="fas fa-external-link-alt mr-3"></i>
                            Buka Link Sekarang
                        </a>

                        <button onclick="copyLink()" 
                                class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 px-6 rounded-xl transition duration-200 flex items-center justify-center">
                            <i class="fas fa-copy mr-3"></i>
                            Salin Link Asli
                        </button>
                    </div>

                    <!-- Warning -->
                    <div class="mt-8 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-triangle text-yellow-500 mt-0.5 mr-3"></i>
                            <div class="text-sm text-yellow-800">
                                <p class="font-medium mb-1">Perhatian</p>
                                <p>Link ini akan mengarahkan Anda ke situs eksternal. Pastikan Anda mempercayai sumber link ini.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 px-8 py-4 border-t border-gray-200">
                    <div class="text-center">
                        <p class="text-sm text-gray-500">
                            Powered by <span class="font-semibold text-blue-600">{{ config('app.name') }}</span>
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            Short URL: <code class="bg-white px-2 py-0.5 rounded">{{ url('/s/' . $shortUrl->code) }}</code>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function copyLink() {
        navigator.clipboard.writeText('{{ $shortUrl->original_url }}').then(() => {
            // Show toast notification
            const toast = document.createElement('div');
            toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center';
            toast.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Link berhasil disalin!';
            document.body.appendChild(toast);
            
            // Auto remove after 3 seconds
            setTimeout(() => {
                if (document.body.contains(toast)) {
                    toast.style.transform = 'translateX(100%)';
                    toast.style.opacity = '0';
                    setTimeout(() => {
                        if (document.body.contains(toast)) {
                            document.body.removeChild(toast);
                        }
                    }, 300);
                }
            }, 3000);
        }).catch(() => {
            alert('Gagal menyalin link. Silakan salin manual.');
        });
    }
    </script>
</x-opac.layout>
