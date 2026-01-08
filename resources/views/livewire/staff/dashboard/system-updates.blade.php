<div>
    {{-- Splash Modal for New Updates --}}
    @if($showSplashModal && $updates->isNotEmpty())
    <template x-teleport="body">
        <div class="fixed inset-0 z-[99999] flex items-center justify-center p-4" x-data x-init="document.body.classList.add('overflow-hidden')" x-destroy="document.body.classList.remove('overflow-hidden')">
            <div class="absolute inset-0 bg-white/80 backdrop-blur-sm"></div>
            
            <div class="relative bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 border border-blue-200/60 rounded-3xl shadow-2xl max-w-3xl w-full max-h-[85vh] overflow-hidden">
                {{-- Header --}}
                <div class="bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-600 p-6 text-white relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-600/20 via-indigo-600/20 to-purple-600/20"></div>
                    <div class="relative flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm border border-white/30">
                                <i class="fas fa-cog text-2xl text-blue-100"></i>
                            </div>
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <h2 class="text-xl font-bold">Pembaruan Sistem</h2>
                                    <span class="px-2 py-1 bg-white/20 text-blue-100 text-xs font-bold rounded-lg border border-white/30">
                                        v2.5
                                    </span>
                                </div>
                                <p class="text-blue-100 text-sm">{{ $updates->count() }} fitur dan perbaikan terbaru</p>
                            </div>
                        </div>
                        <button wire:click="closeSplash" class="p-2 hover:bg-white/20 rounded-xl transition border border-white/20">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                {{-- Content --}}
                <div class="p-6 max-h-96 overflow-y-auto bg-gradient-to-b from-blue-50/50 to-indigo-50/30">
                    <div class="grid md:grid-cols-2 gap-3">
                        @foreach($updates as $update)
                        <div class="flex items-center gap-3 p-3 bg-white/70 backdrop-blur-sm border border-blue-200/40 rounded-xl shadow-sm">
                            <div class="w-8 h-8 bg-gradient-to-br from-{{ $update->color }}-400 to-{{ $update->color }}-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-{{ $update->icon ?? 'star' }} text-white text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-semibold text-gray-900 text-sm truncate">{{ $update->title }}</h4>
                                <span class="text-xs text-{{ $update->color }}-600 font-medium">{{ ucfirst($update->type) }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Footer --}}
                <div class="p-6 border-t border-blue-200/60 bg-gradient-to-r from-blue-50/80 to-indigo-50/80 backdrop-blur-sm flex justify-end">
                    <button wire:click="dismissAll" class="px-6 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-bold rounded-xl hover:from-blue-600 hover:to-indigo-700 transition shadow-lg border border-blue-400">
                        <i class="fas fa-check mr-2"></i>
                        Mengerti
                    </button>
                </div>
            </div>
        </div>
    </template>
    @endif

    {{-- Simple Account Integration Cards --}}
    <div class="grid md:grid-cols-2 gap-4 mb-4">
        {{-- Google Account Card --}}
        <div class="bg-gradient-to-r from-red-50 to-pink-50 border border-red-200/60 rounded-xl p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-red-400 to-pink-500 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fab fa-google text-white"></i>
                </div>
                <div class="flex-1">
                    @if(auth()->user()->socialAccounts()->where('provider', 'google')->exists())
                        <h4 class="font-semibold text-red-900 text-sm">Google Account Terhubung</h4>
                        <p class="text-xs text-red-600">Login lebih mudah</p>
                    @else
                        <h4 class="font-semibold text-red-900 text-sm">Hubungkan Google</h4>
                        <p class="text-xs text-red-600">Login lebih mudah</p>
                    @endif
                </div>
                @if(!auth()->user()->socialAccounts()->where('provider', 'google')->exists())
                <a href="{{ route('auth.google') }}" class="px-3 py-1.5 bg-red-500 text-white text-xs font-medium rounded-lg hover:bg-red-600 transition">
                    Hubungkan
                </a>
                @else
                <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check text-green-600 text-xs"></i>
                </div>
                @endif
            </div>
        </div>

        {{-- Member Account Card --}}
        <div class="bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200/60 rounded-xl p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-user-graduate text-white"></i>
                </div>
                <div class="flex-1">
                    @php
                        $linkedMember = \App\Models\Member::where('email', auth()->user()->email)->first();
                    @endphp
                    @if($linkedMember)
                        <h4 class="font-semibold text-emerald-900 text-sm">Member Area Terhubung</h4>
                        <p class="text-xs text-emerald-600">{{ $linkedMember->name }}</p>
                    @else
                        <h4 class="font-semibold text-emerald-900 text-sm">Hubungkan Member Area</h4>
                        <p class="text-xs text-emerald-600">Akses fasilitas mahasiswa</p>
                    @endif
                </div>
                @if(!$linkedMember)
                <a href="{{ route('staff.profile') }}" class="px-3 py-1.5 bg-emerald-500 text-white text-xs font-medium rounded-lg hover:bg-emerald-600 transition">
                    Hubungkan
                </a>
                @else
                <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check text-green-600 text-xs"></i>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
