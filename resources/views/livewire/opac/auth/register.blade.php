<div>
    <div class="min-h-[70vh] flex items-center justify-center py-8 px-4" x-data="{ registerType: 'member' }">
        <div class="w-full max-w-4xl">
            <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 overflow-hidden">
                <div class="flex flex-col lg:flex-row">
                    <!-- Left - Info -->
                    <div class="hidden lg:flex lg:w-5/12 bg-gradient-to-br from-primary-600 to-primary-800 p-8 flex-col justify-center text-white">
                        <div class="mb-8">
                            <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mb-4">
                                <i class="fas fa-user-plus text-3xl"></i>
                            </div>
                            <h2 class="text-2xl font-bold mb-2">{{ __('opac.auth.register.join_now') }}</h2>
                            <p class="text-primary-200">{{ __('opac.auth.register.join_desc') }}</p>
                        </div>
                        
                        {{-- Member Benefits --}}
                        <div x-show="registerType === 'member'" class="space-y-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center"><i class="fas fa-check text-lg"></i></div>
                                <div><p class="font-medium">{{ __('opac.auth.register.borrow_books') }}</p><p class="text-primary-200 text-sm">{{ __('opac.auth.register.borrow_books_desc') }}</p></div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center"><i class="fas fa-check text-lg"></i></div>
                                <div><p class="font-medium">{{ __('opac.auth.register.digital_access') }}</p><p class="text-primary-200 text-sm">{{ __('opac.auth.register.digital_access_desc') }}</p></div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center"><i class="fas fa-upload text-lg"></i></div>
                                <div><p class="font-medium">{{ __('opac.auth.register.upload_thesis') }}</p><p class="text-primary-200 text-sm">{{ __('opac.auth.register.upload_thesis_desc') }}</p></div>
                            </div>
                        </div>
                        
                        {{-- Staff Benefits --}}
                        <div x-show="registerType === 'staff'" class="space-y-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center"><i class="fas fa-cog text-lg"></i></div>
                                <div><p class="font-medium">{{ __('opac.auth.register.manage_library') }}</p><p class="text-primary-200 text-sm">{{ __('opac.auth.register.manage_library_desc') }}</p></div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center"><i class="fas fa-users text-lg"></i></div>
                                <div><p class="font-medium">{{ __('opac.auth.register.manage_members') }}</p><p class="text-primary-200 text-sm">{{ __('opac.auth.register.manage_members_desc') }}</p></div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center"><i class="fas fa-chart-bar text-lg"></i></div>
                                <div><p class="font-medium">{{ __('opac.auth.register.reports') }}</p><p class="text-primary-200 text-sm">{{ __('opac.auth.register.reports_desc') }}</p></div>
                            </div>
                        </div>
                    </div>

                    <!-- Right - Form -->
                    <div class="lg:w-7/12">
                        <div class="p-6 lg:p-8">
                            {{-- Type Switcher --}}
                            <div class="flex bg-gray-100 rounded-xl p-1 mb-6">
                                <button type="button" @click="registerType = 'member'" 
                                        :class="registerType === 'member' ? 'bg-white shadow text-primary-600' : 'text-gray-500 hover:text-gray-700'"
                                        class="flex-1 py-2.5 rounded-lg font-semibold text-sm transition flex items-center justify-center gap-2">
                                    <i class="fas fa-user"></i> {{ __('opac.auth.register.member') }}
                                </button>
                                <button type="button" @click="registerType = 'staff'" 
                                        :class="registerType === 'staff' ? 'bg-white shadow text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                                        class="flex-1 py-2.5 rounded-lg font-semibold text-sm transition flex items-center justify-center gap-2">
                                    <i class="fas fa-user-tie"></i> {{ __('opac.auth.register.staff') }}
                                </button>
                            </div>

                            <div class="mb-6">
                                <h1 class="text-xl font-bold text-gray-900" x-text="registerType === 'member' ? '{{ __('opac.auth.register.member_title') }}' : '{{ __('opac.auth.register.staff_title') }}'"></h1>
                                <p class="text-gray-500 text-sm" x-text="registerType === 'member' ? '{{ __('opac.auth.register.member_subtitle') }}' : '{{ __('opac.auth.register.staff_subtitle') }}'"></p>
                            </div>

                            {{-- Staff Notice --}}
                            <div x-show="registerType === 'staff'" class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-5">
                                <div class="flex gap-3">
                                    <i class="fas fa-info-circle text-amber-500 mt-0.5"></i>
                                    <div class="text-sm text-amber-800">
                                        <p class="font-medium">{{ __('opac.auth.register.admin_approval') }}</p>
                                        <p class="text-amber-600">{{ __('opac.auth.register.admin_approval_desc') }}</p>
                                    </div>
                                </div>
                            </div>

                            @if($errors->any())
                            <div class="bg-red-50 border border-red-200 text-red-600 text-sm p-4 rounded-xl mb-5">
                                <ul class="space-y-1">
                                    @foreach($errors->all() as $error)
                                    <li><i class="fas fa-exclamation-circle mr-1"></i>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif

                            {{-- SIAKAD Member Detected Confirmation --}}
                            @if($showConfirmation && $detectedMember)
                            <div x-show="registerType === 'member'" class="space-y-4">
                                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                                    <div class="flex gap-3">
                                        <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-user-check text-emerald-600"></i>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-emerald-800">Data Mahasiswa Ditemukan!</p>
                                            <p class="text-sm text-emerald-700 mt-1">Kami menemukan data Anda di sistem SIAKAD:</p>
                                        </div>
                                    </div>
                                    <div class="mt-4 bg-white rounded-lg p-3 border border-emerald-100">
                                        <div class="grid grid-cols-2 gap-2 text-sm">
                                            <div><span class="text-gray-500">NIM:</span> <span class="font-medium">{{ $detectedMember->member_id }}</span></div>
                                            <div><span class="text-gray-500">Nama:</span> <span class="font-medium">{{ $detectedMember->name }}</span></div>
                                            @if($detectedMember->faculty)
                                            <div><span class="text-gray-500">Fakultas:</span> <span class="font-medium">{{ $detectedMember->faculty->name }}</span></div>
                                            @endif
                                            @if($detectedMember->department)
                                            <div><span class="text-gray-500">Prodi:</span> <span class="font-medium">{{ $detectedMember->department->name }}</span></div>
                                            @endif
                                        </div>
                                    </div>
                                    <p class="text-sm text-emerald-700 mt-3">Apakah ini data Anda? Klik "Hubungkan Akun" untuk melanjutkan.</p>
                                </div>

                                <form wire:submit="confirmLinkAccount" class="space-y-3">
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                                            <input type="password" wire:model="password" required placeholder="Min. 8 karakter"
                                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 transition">
                                            @error('password') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                                            <input type="password" wire:model="password_confirmation" required placeholder="Ulangi password"
                                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 transition">
                                        </div>
                                    </div>
                                    <div class="flex gap-3">
                                        <button type="button" wire:click="cancelLinkAccount" class="flex-1 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition">
                                            <i class="fas fa-times mr-1"></i> Bukan Saya
                                        </button>
                                        <button type="submit" wire:loading.attr="disabled" class="flex-1 py-3 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition disabled:opacity-50">
                                            <span wire:loading wire:target="confirmLinkAccount"><i class="fas fa-spinner fa-spin"></i></span>
                                            <span wire:loading.remove wire:target="confirmLinkAccount"><i class="fas fa-link mr-1"></i> Hubungkan Akun</span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                            @else
                            {{-- Member Form (Livewire) --}}
                            <form x-show="registerType === 'member'" wire:submit="register" class="space-y-3" x-data="memberForm()">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('opac.auth.register.full_name') }}</label>
                                    <input type="text" wire:model.blur="name" required placeholder="{{ __('opac.auth.register.full_name_placeholder') }}"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 transition">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('opac.auth.register.email') }}</label>
                                    <input type="email" wire:model.blur="email" x-model="email" @input="checkEmail()" required placeholder="{{ __('opac.auth.register.email_placeholder') }}"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 transition">
                                    <p x-show="emailType === 'internal'" class="text-xs text-emerald-600 mt-1"><i class="fas fa-check-circle mr-1"></i>{{ __('opac.auth.register.email_unida_detected') }}</p>
                                    <p x-show="emailType === 'external'" class="text-xs text-blue-600 mt-1"><i class="fas fa-university mr-1"></i>{{ __('opac.auth.register.email_campus_detected') }}</p>
                                    <p x-show="emailType === 'public'" class="text-xs text-gray-500 mt-1"><i class="fas fa-envelope mr-1"></i>{{ __('opac.auth.register.email_public') }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('opac.auth.register.phone') }}</label>
                                    <input type="text" wire:model="phone" placeholder="{{ __('opac.auth.register.phone_placeholder') }}"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 transition">
                                </div>
                                
                                {{-- Institution fields for public email --}}
                                <div x-show="emailType === 'public'" x-collapse class="bg-gray-50 rounded-xl p-3 space-y-3">
                                    <p class="text-xs text-gray-600 font-medium"><i class="fas fa-building mr-1"></i> {{ __('opac.auth.register.institution_section') }}</p>
                                    <div class="grid grid-cols-2 gap-3">
                                        <input type="text" wire:model="institution" placeholder="{{ __('opac.auth.register.institution') }}"
                                            class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 transition">
                                        <input type="text" wire:model="institution_city" placeholder="{{ __('opac.auth.register.institution_city') }}"
                                            class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 transition">
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('opac.auth.register.password') }}</label>
                                        <input type="password" wire:model="password" required placeholder="{{ __('opac.auth.register.password_placeholder') }}"
                                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 transition">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('opac.auth.register.confirm_password') }}</label>
                                        <input type="password" wire:model="password_confirmation" required placeholder="{{ __('opac.auth.register.confirm_placeholder') }}"
                                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 transition">
                                    </div>
                                </div>
                                <button type="submit" wire:loading.attr="disabled" class="w-full py-3 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-semibold rounded-xl shadow-lg shadow-primary-500/30 hover:shadow-xl transition flex items-center justify-center gap-2 mt-2 disabled:opacity-50">
                                    <span wire:loading wire:target="register"><i class="fas fa-spinner fa-spin"></i></span>
                                    <span wire:loading.remove wire:target="register"><i class="fas fa-user-plus"></i></span>
                                    {{ __('opac.auth.register.register_member_btn') }}
                                </button>
                            </form>
                            @endif

                            {{-- Staff Form --}}
                            <form x-show="registerType === 'staff'" action="{{ route('opac.register.staff') }}" method="POST" class="space-y-3">
                                @csrf
                                <input type="hidden" name="type" value="staff">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('opac.auth.register.full_name') }}</label>
                                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="{{ __('opac.auth.register.full_name_placeholder') }}"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 transition">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('opac.auth.register.email') }}</label>
                                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="{{ __('opac.auth.register.email_placeholder') }}"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 transition">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('opac.auth.register.branch') }}</label>
                                    <select name="branch_id" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 transition">
                                        <option value="">{{ __('opac.auth.register.branch_placeholder') }}</option>
                                        @foreach(\App\Models\Branch::where('is_active', true)->orderByDesc('is_main')->orderBy('name')->get() as $branch)
                                        <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('opac.auth.register.password') }}</label>
                                        <input type="password" name="password" required placeholder="{{ __('opac.auth.register.password_placeholder') }}"
                                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 transition">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('opac.auth.register.confirm_password') }}</label>
                                        <input type="password" name="password_confirmation" required placeholder="{{ __('opac.auth.register.confirm_placeholder') }}"
                                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 transition">
                                    </div>
                                </div>
                                <button type="submit" class="w-full py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-xl shadow-lg shadow-blue-500/30 hover:shadow-xl transition flex items-center justify-center gap-2 mt-2">
                                    <i class="fas fa-user-tie"></i> {{ __('opac.auth.register.register_staff_btn') }}
                                </button>
                            </form>

                            <div class="mt-5 pt-5 border-t border-gray-100 text-center">
                                <p class="text-sm text-gray-500">
                                    {{ __('opac.auth.register.has_account') }} 
                                    <a href="{{ route('login') }}" class="text-primary-600 font-semibold hover:text-primary-700">{{ __('opac.auth.register.login') }}</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function memberForm() {
        const trustedDomains = @json(array_map('trim', file(base_path('docs/email.md'))));
        return {
            email: '{{ old("email") }}',
            emailType: '',
            checkEmail() {
                if (!this.email || !this.email.includes('@')) {
                    this.emailType = '';
                    return;
                }
                const domain = '@' + this.email.split('@')[1].toLowerCase();
                if (trustedDomains.includes(domain)) {
                    this.emailType = 'internal';
                } else if (domain.endsWith('.ac.id')) {
                    this.emailType = 'external';
                } else {
                    this.emailType = 'public';
                }
            },
            init() { this.checkEmail(); }
        }
    }
    </script>
</div>
