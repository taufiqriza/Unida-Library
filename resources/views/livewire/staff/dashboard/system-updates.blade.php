<div>
    {{-- Splash Modal for New Updates --}}
    @if($showSplashModal && $updates->isNotEmpty())
    <template x-teleport="body">
        <div class="fixed inset-0 z-[99999] flex items-center justify-center p-4" x-data x-init="document.body.classList.add('overflow-hidden')" x-destroy="document.body.classList.remove('overflow-hidden')">
            <div class="absolute inset-0 bg-white/80 backdrop-blur-sm"></div>
            
            <div class="relative bg-gradient-to-br from-amber-50 via-yellow-50 to-orange-50 border border-amber-200/60 rounded-3xl shadow-2xl max-w-3xl w-full max-h-[85vh] overflow-hidden">
                {{-- Header --}}
                <div class="bg-gradient-to-r from-amber-400 via-yellow-500 to-orange-500 p-6 text-white relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-r from-amber-600/20 via-yellow-600/20 to-orange-600/20"></div>
                    <div class="relative flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm border border-white/30">
                                <i class="fas fa-cog text-2xl text-amber-100"></i>
                            </div>
                            <div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <h2 class="text-xl font-bold">Pembaruan Sistem</h2>
                                        <span class="px-2 py-1 bg-white/20 text-amber-100 text-xs font-bold rounded-lg border border-white/30">
                                            v2.5
                                        </span>
                                    </div>
                                    <p class="text-amber-100 text-sm">{{ $updates->count() }} fitur dan perbaikan terbaru</p>
                                </div>
                                <div class="flex items-center gap-1 mt-2">
                                    <i class="fas fa-gem text-xs text-yellow-200"></i>
                                    <span class="text-xs text-yellow-200 font-medium">SISTEM ILMU</span>
                                    <span class="text-xs text-amber-200">- Integrated Library Management UNIDA v.2.0</span>
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
                    {{-- Account Integration Cards in Modal --}}
                    <div class="grid md:grid-cols-2 gap-3 mb-4">
                        {{-- Google Account Card --}}
                        <div class="flex items-center gap-2 px-3 py-2 bg-gradient-to-r from-amber-50 to-yellow-50 border border-amber-200/60 rounded-xl shadow-sm">
                            <div class="w-6 h-6 bg-white rounded-md shadow-sm flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4" viewBox="0 0 24 24">
                                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                                </svg>
                            </div>
                            <div class="flex flex-col flex-1">
                                @if(auth()->user()->socialAccounts()->where('provider', 'google')->exists())
                                    <span class="text-[10px] text-gray-400 leading-tight">Google Terhubung</span>
                                    <span class="text-xs text-green-700 font-semibold leading-tight">✓ Login mudah</span>
                                @else
                                    <span class="text-[10px] text-gray-400 leading-tight">Login lebih mudah</span>
                                    <a href="{{ route('auth.google') }}" class="text-xs text-amber-700 hover:text-amber-800 font-semibold leading-tight">Hubungkan Google →</a>
                                @endif
                            </div>
                        </div>

                        {{-- Member Account Card --}}
                        @php
                            $linkedMember = \App\Models\Member::where('email', auth()->user()->email)->first();
                        @endphp
                        <div class="flex items-center gap-2 px-3 py-2 bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200/60 rounded-xl shadow-sm">
                            <div class="w-6 h-6 bg-white rounded-md shadow-sm flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-user-graduate text-emerald-600 text-sm"></i>
                            </div>
                            <div class="flex flex-col flex-1">
                                @if($linkedMember)
                                    <span class="text-[10px] text-gray-400 leading-tight">Member Terhubung</span>
                                    <span class="text-xs text-green-700 font-semibold leading-tight">✓ {{ $linkedMember->name }}</span>
                                @else
                                    <span class="text-[10px] text-gray-400 leading-tight">Akses fasilitas</span>
                                    <a href="{{ route('staff.profile') }}" class="text-xs text-emerald-700 hover:text-emerald-800 font-semibold leading-tight">Hubungkan Member →</a>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- System Updates Grid --}}
                    <div class="grid md:grid-cols-2 gap-3">
                        @foreach($updates as $update)
                        <div class="flex items-center gap-3 p-3 bg-white/70 backdrop-blur-sm border border-amber-200/40 rounded-xl shadow-sm">
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
                <div class="p-6 border-t border-amber-200/60 bg-gradient-to-r from-amber-50/80 to-yellow-50/80 backdrop-blur-sm flex justify-end">
                    <button wire:click="dismissAll" class="px-6 py-2 bg-gradient-to-r from-amber-500 to-yellow-600 text-white font-bold rounded-xl hover:from-amber-600 hover:to-yellow-700 transition shadow-lg border border-amber-400">
                        <i class="fas fa-gem mr-2"></i>
                        Mengerti
                    </button>
                </div>
            </div>
        </div>
    </template>
    @endif
</div>
