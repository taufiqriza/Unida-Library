<div>
    {{-- Splash Modal for New Updates --}}
    @if($showSplashModal && $updates->isNotEmpty())
    <template x-teleport="body">
        <div class="fixed inset-0 z-[99999] flex items-center justify-center p-4" x-data x-init="document.body.classList.add('overflow-hidden')" x-destroy="document.body.classList.remove('overflow-hidden')">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-900/90 via-purple-900/90 to-indigo-900/90 backdrop-blur-sm"></div>
            
            <div class="relative bg-white rounded-3xl shadow-2xl max-w-2xl w-full max-h-[85vh] overflow-hidden">
                {{-- Header --}}
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                                <i class="fas fa-sparkles text-2xl"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold">Pembaruan Sistem Terbaru</h2>
                                <p class="text-blue-100 text-sm">{{ $updates->count() }} fitur dan perbaikan baru</p>
                            </div>
                        </div>
                        <button wire:click="closeSplash" class="p-2 hover:bg-white/20 rounded-lg transition">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                {{-- Content --}}
                <div class="p-6 max-h-96 overflow-y-auto">
                    <div class="space-y-4">
                        @foreach($updates->take(5) as $update)
                        <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-xl">
                            <div class="w-10 h-10 bg-{{ $update->color }}-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-{{ $update->icon ?? 'star' }} text-{{ $update->color }}-600"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-semibold text-gray-900 mb-1">{{ $update->title }}</h4>
                                <p class="text-sm text-gray-600 leading-relaxed">{{ $update->description }}</p>
                                <span class="inline-block mt-2 px-2 py-1 bg-{{ $update->color }}-100 text-{{ $update->color }}-700 text-xs font-medium rounded-full">
                                    {{ ucfirst($update->type) }}
                                </span>
                            </div>
                        </div>
                        @endforeach

                        @if($updates->count() > 5)
                        <div class="text-center py-2">
                            <p class="text-sm text-gray-500">Dan {{ $updates->count() - 5 }} pembaruan lainnya...</p>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Footer --}}
                <div class="p-6 border-t border-gray-200 bg-gray-50 flex justify-between items-center">
                    <p class="text-sm text-gray-600">
                        <i class="fas fa-info-circle mr-1"></i>
                        Pembaruan ini dapat dilihat kembali di dashboard
                    </p>
                    <div class="flex gap-3">
                        <button wire:click="closeSplash" class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium transition">
                            Nanti Saja
                        </button>
                        <button wire:click="dismissAll" class="px-6 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-medium rounded-xl hover:from-blue-700 hover:to-purple-700 transition">
                            <i class="fas fa-check mr-1"></i>
                            Mengerti
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
    @endif

    {{-- Dashboard Cards --}}
    <div class="space-y-4">
        {{-- Member Linking Info Card --}}
        @if($memberLinkingInfo)
        <div class="bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200 rounded-2xl p-6">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-link text-emerald-600 text-xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-emerald-900 mb-2">{{ $memberLinkingInfo['title'] }}</h3>
                    <p class="text-emerald-700 mb-4">{{ $memberLinkingInfo['description'] }}</p>
                    
                    <div class="grid md:grid-cols-2 gap-3 mb-4">
                        @foreach($memberLinkingInfo['benefits'] as $benefit)
                        <div class="flex items-center gap-2 text-sm text-emerald-700">
                            <i class="fas fa-check-circle text-emerald-500"></i>
                            <span>{{ $benefit }}</span>
                        </div>
                        @endforeach
                    </div>
                    
                    <a href="{{ route('staff.profile') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white font-medium rounded-xl hover:bg-emerald-700 transition">
                        <i class="fas fa-user-plus"></i>
                        <span>Hubungkan Sekarang</span>
                    </a>
                </div>
            </div>
        </div>
        @endif

        {{-- System Updates Cards --}}
        @foreach($updates->take(3) as $update)
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-start gap-4 flex-1">
                    <div class="w-10 h-10 bg-{{ $update->color }}-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-{{ $update->icon ?? 'star' }} text-{{ $update->color }}-600"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-2">
                            <h4 class="font-semibold text-gray-900">{{ $update->title }}</h4>
                            <span class="px-2 py-1 bg-{{ $update->color }}-100 text-{{ $update->color }}-700 text-xs font-medium rounded-full">
                                {{ ucfirst($update->type) }}
                            </span>
                        </div>
                        <p class="text-gray-600 text-sm leading-relaxed">{{ $update->description }}</p>
                        <p class="text-xs text-gray-400 mt-2">
                            <i class="fas fa-clock mr-1"></i>
                            {{ $update->published_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
                
                @if($update->is_dismissible)
                <button wire:click="dismissUpdate({{ $update->id }})" 
                        class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition flex-shrink-0"
                        title="Tutup">
                    <i class="fas fa-times"></i>
                </button>
                @endif
            </div>
        </div>
        @endforeach

        @if($updates->count() > 3)
        <div class="text-center py-4">
            <button wire:click="$set('showSplashModal', true)" class="text-blue-600 hover:text-blue-700 font-medium text-sm">
                <i class="fas fa-plus-circle mr-1"></i>
                Lihat {{ $updates->count() - 3 }} pembaruan lainnya
            </button>
        </div>
        @endif
    </div>
</div>
