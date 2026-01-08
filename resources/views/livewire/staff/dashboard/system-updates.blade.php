<div>
    {{-- Splash Modal for New Updates --}}
    @if($showSplashModal && $updates->isNotEmpty())
    <template x-teleport="body">
        <div class="fixed inset-0 z-[99999] flex items-center justify-center p-4" x-data x-init="document.body.classList.add('overflow-hidden')" x-destroy="document.body.classList.remove('overflow-hidden')">
            <div class="absolute inset-0 bg-white/80 backdrop-blur-sm"></div>
            
            <div class="relative bg-gradient-to-br from-amber-50 via-yellow-50 to-orange-50 border border-amber-200/60 rounded-3xl shadow-2xl max-w-2xl w-full max-h-[85vh] overflow-hidden">
                {{-- Header --}}
                <div class="bg-gradient-to-r from-amber-400 via-yellow-500 to-orange-500 p-6 text-white relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-r from-amber-600/20 via-yellow-600/20 to-orange-600/20"></div>
                    <div class="relative flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm border border-white/30">
                                <i class="fas fa-crown text-2xl text-yellow-100"></i>
                            </div>
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <h2 class="text-xl font-bold">Pembaruan Sistem Terbaru</h2>
                                    <span class="px-2 py-1 bg-white/20 text-yellow-100 text-xs font-bold rounded-lg border border-white/30">
                                        v2.5 Gold
                                    </span>
                                </div>
                                <p class="text-amber-100 text-sm">{{ $updates->count() }} fitur dan perbaikan premium</p>
                                <div class="flex items-center gap-1 mt-1">
                                    <i class="fas fa-gem text-xs text-yellow-200"></i>
                                    <span class="text-xs text-yellow-200 font-medium">SISTEM ILMU</span>
                                    <span class="text-xs text-amber-200">- Integrated Library UNIDA</span>
                                </div>
                            </div>
                        </div>
                        <button wire:click="closeSplash" class="p-2 hover:bg-white/20 rounded-xl transition border border-white/20">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                {{-- Content --}}
                <div class="p-6 max-h-96 overflow-y-auto bg-gradient-to-b from-amber-50/50 to-yellow-50/30">
                    <div class="space-y-4">
                        @foreach($updates->take(5) as $update)
                        <div class="flex items-start gap-4 p-4 bg-white/70 backdrop-blur-sm border border-amber-200/40 rounded-2xl shadow-sm">
                            <div class="w-12 h-12 bg-gradient-to-br from-{{ $update->color }}-400 to-{{ $update->color }}-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                                <i class="fas fa-{{ $update->icon ?? 'star' }} text-white text-lg"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-2">
                                    <h4 class="font-bold text-gray-900">{{ $update->title }}</h4>
                                    <span class="px-2 py-1 bg-gradient-to-r from-{{ $update->color }}-100 to-{{ $update->color }}-200 text-{{ $update->color }}-800 text-xs font-bold rounded-lg border border-{{ $update->color }}-300/50">
                                        {{ ucfirst($update->type) }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-700 leading-relaxed mb-2">{{ $update->description }}</p>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-sparkles text-amber-500 text-xs"></i>
                                    <span class="text-xs text-amber-700 font-medium">Premium Feature</span>
                                </div>
                            </div>
                        </div>
                        @endforeach

                        @if($updates->count() > 5)
                        <div class="text-center py-3">
                            <div class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-amber-100 to-yellow-100 border border-amber-300/50 rounded-xl">
                                <i class="fas fa-plus-circle text-amber-600"></i>
                                <span class="text-sm font-medium text-amber-800">{{ $updates->count() - 5 }} pembaruan premium lainnya</span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Footer --}}
                <div class="p-6 border-t border-amber-200/60 bg-gradient-to-r from-amber-50/80 to-yellow-50/80 backdrop-blur-sm flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-info-circle text-amber-600"></i>
                        <p class="text-sm text-amber-800 font-medium">Pembaruan dapat dilihat kembali di dashboard</p>
                    </div>
                    <div class="flex gap-3">
                        <button wire:click="closeSplash" class="px-4 py-2 text-amber-700 hover:text-amber-900 font-medium transition border border-amber-300/50 rounded-xl hover:bg-amber-100/50">
                            Nanti Saja
                        </button>
                        <button wire:click="dismissAll" class="px-6 py-2 bg-gradient-to-r from-amber-500 to-yellow-600 text-white font-bold rounded-xl hover:from-amber-600 hover:to-yellow-700 transition shadow-lg border border-amber-400">
                            <i class="fas fa-crown mr-2"></i>
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
        <div class="bg-gradient-to-r from-emerald-50 via-teal-50 to-cyan-50 border border-emerald-200/60 rounded-2xl p-6 shadow-sm">
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 bg-gradient-to-br from-emerald-400 to-teal-600 rounded-2xl flex items-center justify-center flex-shrink-0 shadow-lg">
                    <i class="fas fa-link text-white text-xl"></i>
                </div>
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <h3 class="text-lg font-bold text-emerald-900">{{ $memberLinkingInfo['title'] }}</h3>
                        <span class="px-2 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-lg border border-emerald-300/50">
                            Premium
                        </span>
                    </div>
                    <p class="text-emerald-800 mb-4 font-medium">{{ $memberLinkingInfo['description'] }}</p>
                    
                    <div class="grid md:grid-cols-2 gap-3 mb-4">
                        @foreach($memberLinkingInfo['benefits'] as $benefit)
                        <div class="flex items-center gap-2 text-sm text-emerald-800">
                            <i class="fas fa-gem text-emerald-500"></i>
                            <span class="font-medium">{{ $benefit }}</span>
                        </div>
                        @endforeach
                    </div>
                    
                    <a href="{{ route('staff.profile') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-bold rounded-xl hover:from-emerald-600 hover:to-teal-700 transition shadow-lg border border-emerald-400">
                        <i class="fas fa-crown"></i>
                        <span>Hubungkan Sekarang</span>
                    </a>
                </div>
            </div>
        </div>
        @endif

        {{-- System Updates Cards --}}
        @foreach($updates->take(3) as $update)
        <div class="bg-gradient-to-br from-amber-50 via-yellow-50 to-orange-50 border border-amber-200/60 rounded-2xl p-6 shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-start gap-4 flex-1">
                    <div class="w-12 h-12 bg-gradient-to-br from-{{ $update->color }}-400 to-{{ $update->color }}-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                        <i class="fas fa-{{ $update->icon ?? 'star' }} text-white text-lg"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-2">
                            <h4 class="font-bold text-gray-900">{{ $update->title }}</h4>
                            <span class="px-2 py-1 bg-gradient-to-r from-{{ $update->color }}-100 to-{{ $update->color }}-200 text-{{ $update->color }}-800 text-xs font-bold rounded-lg border border-{{ $update->color }}-300/50">
                                {{ ucfirst($update->type) }}
                            </span>
                            <span class="px-2 py-1 bg-gradient-to-r from-amber-100 to-yellow-100 text-amber-800 text-xs font-bold rounded-lg border border-amber-300/50">
                                v2.5
                            </span>
                        </div>
                        <p class="text-gray-700 text-sm leading-relaxed mb-2 font-medium">{{ $update->description }}</p>
                        <div class="flex items-center gap-4">
                            <p class="text-xs text-amber-600 flex items-center gap-1">
                                <i class="fas fa-clock"></i>
                                <span class="font-medium">{{ $update->published_at->diffForHumans() }}</span>
                            </p>
                            <div class="flex items-center gap-1">
                                <i class="fas fa-gem text-amber-500 text-xs"></i>
                                <span class="text-xs text-amber-700 font-bold">SISTEM ILMU</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                @if($update->is_dismissible)
                <button wire:click="dismissUpdate({{ $update->id }})" 
                        class="p-2 text-amber-500 hover:text-amber-700 hover:bg-amber-100/50 rounded-xl transition flex-shrink-0 border border-amber-200/50"
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
