<div>
    <div class="min-h-[70vh] flex items-center justify-center py-8 px-4">
        <div class="w-full max-w-4xl">
            <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 overflow-hidden">
                <div class="flex flex-col lg:flex-row">
                    <!-- Left - Info (Hidden on Mobile) -->
                    <div class="hidden lg:flex lg:w-5/12 bg-gradient-to-br from-primary-600 to-primary-800 p-8 flex-col justify-between text-white">
                        <div>
                            <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center mb-4">
                                <i class="fas fa-book-reader text-2xl"></i>
                            </div>
                            <h2 class="text-xl font-bold mb-1">{{ __('opac.auth.login.digital_library') }}</h2>
                            <p class="text-primary-200 text-sm mb-6">{{ __('opac.auth.login.digital_library_desc') }}</p>
                        </div>
                        
                        <div class="space-y-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 bg-white/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-book"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-sm">{{ __('opac.auth.login.online_loan') }}</p>
                                    <p class="text-primary-200 text-xs">{{ __('opac.auth.login.online_loan_desc') }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 bg-white/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-file-pdf"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-sm">{{ __('opac.auth.login.ebook_ethesis') }}</p>
                                    <p class="text-primary-200 text-xs">{{ __('opac.auth.login.ebook_ethesis_desc') }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 bg-white/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-upload"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-sm">Unggah Mandiri</p>
                                    <p class="text-primary-200 text-xs">Submit skripsi/tesis/disertasi online</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 bg-white/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-search-plus"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-sm">Cek Plagiasi</p>
                                    <p class="text-primary-200 text-xs">Integrasi Turnitin untuk similarity check</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 bg-white/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-certificate"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-sm">Bebas Pustaka</p>
                                    <p class="text-primary-200 text-xs">Pengajuan surat bebas pustaka digital</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right - Form -->
                    <div class="lg:w-7/12">
                        <!-- Header - Mobile Only -->
                        <div class="lg:hidden bg-gradient-to-r from-primary-600 to-primary-800 px-6 py-4 flex items-center gap-4">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-user-circle text-2xl text-white"></i>
                            </div>
                            <div>
                                <h1 class="text-lg font-bold text-white">{{ __('opac.auth.login.title') }}</h1>
                                <p class="text-primary-200 text-sm">{{ __('opac.auth.login.subtitle') }}</p>
                            </div>
                        </div>

                        <div class="p-6 lg:p-8">
                            <!-- Desktop Title -->
                            <div class="hidden lg:block mb-6">
                                <h1 class="text-xl font-bold text-gray-900">{{ __('opac.auth.login.title') }}</h1>
                                <p class="text-gray-500 text-sm">{{ __('opac.auth.login.subtitle') }}</p>
                            </div>

                            @if($errors->any())
                            <div class="bg-red-50 border border-red-200 text-red-600 text-sm p-4 rounded-xl mb-5 flex items-start gap-3">
                                <i class="fas fa-exclamation-circle mt-0.5"></i>
                                <span>{{ $errors->first() }}</span>
                            </div>
                            @endif

                            @if(session('success'))
                            <div class="bg-emerald-50 border border-emerald-200 text-emerald-600 text-sm p-4 rounded-xl mb-5 flex items-start gap-3">
                                <i class="fas fa-check-circle mt-0.5"></i>
                                <span>{{ session('success') }}</span>
                            </div>
                            @endif

                            @if(session('error'))
                            <div class="bg-red-50 border border-red-200 text-red-600 text-sm p-4 rounded-xl mb-5 flex items-start gap-3">
                                <i class="fas fa-exclamation-circle mt-0.5"></i>
                                <span>{{ session('error') }}</span>
                            </div>
                            @endif

                            {{-- Portal Selection Modal --}}
                            @if($showPortalChoice && $availablePortals)
                            <div class="mb-5">
                                <div class="bg-blue-50 border border-blue-200 text-blue-700 text-sm p-3 rounded-xl mb-4 flex items-start gap-2">
                                    <i class="fas fa-info-circle mt-0.5"></i>
                                    <span>Email ini terdaftar di 2 portal. Pilih portal yang ingin diakses:</span>
                                </div>
                                
                                <div class="space-y-3">
                                    {{-- Staff Portal Option --}}
                                    <button wire:click="selectPortal('staff')" wire:loading.attr="disabled"
                                        class="w-full p-4 bg-gradient-to-r from-indigo-50 to-blue-50 border-2 border-indigo-200 rounded-xl hover:border-indigo-400 hover:shadow-md transition text-left group">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-xl flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-user-tie text-white text-lg"></i>
                                            </div>
                                            <div class="flex-1">
                                                <p class="font-semibold text-gray-900 group-hover:text-indigo-700">Staff Portal</p>
                                                <p class="text-sm text-gray-500">{{ $availablePortals['staff']['name'] }}</p>
                                                <span class="inline-block mt-1 px-2 py-0.5 bg-indigo-100 text-indigo-700 text-xs rounded-full capitalize">
                                                    {{ str_replace('_', ' ', $availablePortals['staff']['role']) }}
                                                </span>
                                            </div>
                                            <i class="fas fa-chevron-right text-gray-400 group-hover:text-indigo-600"></i>
                                        </div>
                                    </button>

                                    {{-- Member Portal Option --}}
                                    <button wire:click="selectPortal('member')" wire:loading.attr="disabled"
                                        class="w-full p-4 bg-gradient-to-r from-emerald-50 to-teal-50 border-2 border-emerald-200 rounded-xl hover:border-emerald-400 hover:shadow-md transition text-left group">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-user text-white text-lg"></i>
                                            </div>
                                            <div class="flex-1">
                                                <p class="font-semibold text-gray-900 group-hover:text-emerald-700">Member Portal</p>
                                                <p class="text-sm text-gray-500">{{ $availablePortals['member']['name'] }}</p>
                                                <span class="inline-block mt-1 px-2 py-0.5 bg-emerald-100 text-emerald-700 text-xs rounded-full">
                                                    ID: {{ $availablePortals['member']['member_id'] }}
                                                </span>
                                            </div>
                                            <i class="fas fa-chevron-right text-gray-400 group-hover:text-emerald-600"></i>
                                        </div>
                                    </button>
                                </div>

                                <button wire:click="cancelPortalChoice" class="w-full mt-3 py-2 text-sm text-gray-500 hover:text-gray-700">
                                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                                </button>
                            </div>
                            @else

                            @if(\App\Models\Setting::get('google_enabled'))
                            <a href="{{ route('auth.google') }}" class="w-full py-3 bg-white border-2 border-gray-200 rounded-xl flex items-center justify-center gap-3 hover:bg-gray-50 hover:border-gray-300 transition mb-3">
                                <svg class="w-5 h-5" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                                <span class="text-gray-700 font-medium">{{ __('opac.auth.login.google_login') }}</span>
                            </a>
                            
                            <!-- Info Box untuk Civitas - Dismissible -->
                            <div x-data="{ show: true }" x-show="show" x-transition class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-100 rounded-xl p-3 mb-4 relative">
                                <button @click="show = false" class="absolute top-2 right-2 text-blue-400 hover:text-blue-600 transition">
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                                <div class="flex items-start gap-2 pr-4">
                                    <i class="fas fa-magic text-blue-500 mt-0.5"></i>
                                    <div class="text-xs text-blue-700">
                                        <p class="font-medium mb-1">Auto-Link Civitas UNIDA</p>
                                        <p class="text-blue-600">Email <span class="font-semibold">@unida.gontor.ac.id</span> atau <span class="font-semibold">@student.*.unida.gontor.ac.id</span> otomatis terhubung dengan data SIAKAD/SDM.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="relative my-5">
                                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200"></div></div>
                                <div class="relative flex justify-center text-xs"><span class="px-3 bg-white text-gray-400">{{ __('opac.auth.login.or_manual') }}</span></div>
                            </div>
                            @endif

                            <form wire:submit="login" class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('opac.auth.login.member_id_email') }}</label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                            <i class="fas fa-user"></i>
                                        </span>
                                        <input type="text" wire:model="identifier" required 
                                            placeholder="{{ __('opac.auth.login.member_id_placeholder') }}"
                                            class="w-full pl-11 pr-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                                    </div>
                                </div>
                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <label class="block text-sm font-medium text-gray-700">{{ __('opac.auth.login.password') }}</label>
                                        <a href="{{ route('opac.forgot-password') }}" class="text-xs text-primary-600 hover:text-primary-700 font-medium">Lupa Password?</a>
                                    </div>
                                    <div class="relative" x-data="{ show: false }">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                        <input :type="show ? 'text' : 'password'" wire:model="password" required 
                                            placeholder="{{ __('opac.auth.login.password_placeholder') }}"
                                            class="w-full pl-11 pr-12 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                                        <button type="button" @click="show = !show" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                            <i :class="show ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                                        </button>
                                    </div>
                                </div>
                                <button type="submit" wire:loading.attr="disabled" class="w-full py-3 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-semibold rounded-xl shadow-lg shadow-primary-500/30 hover:shadow-xl hover:shadow-primary-500/40 transition flex items-center justify-center gap-2 disabled:opacity-50">
                                    <span wire:loading wire:target="login"><i class="fas fa-spinner fa-spin"></i></span>
                                    <span wire:loading.remove wire:target="login"><i class="fas fa-sign-in-alt"></i></span>
                                    {{ __('opac.auth.login.login_btn') }}
                                </button>
                            </form>

                            <div class="mt-5 pt-5 border-t border-gray-100 text-center">
                                <p class="text-sm text-gray-500">
                                    {{ __('opac.auth.login.no_account') }} 
                                    <a href="{{ route('opac.register') }}" class="text-primary-600 font-semibold hover:text-primary-700">{{ __('opac.auth.login.register_manual') }}</a>
                                </p>
                            </div>
                            @endif {{-- End portal choice else --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
