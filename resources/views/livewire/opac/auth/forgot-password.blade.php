<div>
    <div class="min-h-[70vh] flex items-center justify-center py-8 px-4">
        <div class="w-full max-w-md">
            <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 overflow-hidden">
                {{-- Header --}}
                <div class="bg-gradient-to-r from-primary-600 to-primary-800 px-6 py-5 text-center">
                    <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-key text-2xl text-white"></i>
                    </div>
                    <h1 class="text-xl font-bold text-white">Reset Password</h1>
                    <p class="text-primary-200 text-sm mt-1">
                        @if($step === 'email')
                            Masukkan email untuk menerima kode verifikasi
                        @elseif($step === 'otp')
                            Masukkan kode OTP yang dikirim ke email
                        @else
                            Buat password baru untuk akun Anda
                        @endif
                    </p>
                </div>

                <div class="p-6">
                    {{-- Progress Steps --}}
                    <div class="flex items-center justify-center gap-2 mb-6">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold {{ $step === 'email' ? 'bg-primary-600 text-white' : 'bg-primary-100 text-primary-600' }}">1</div>
                            <span class="text-xs text-gray-500 hidden sm:inline">Email</span>
                        </div>
                        <div class="w-8 h-0.5 {{ in_array($step, ['otp', 'reset']) ? 'bg-primary-600' : 'bg-gray-200' }}"></div>
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold {{ $step === 'otp' ? 'bg-primary-600 text-white' : ($step === 'reset' ? 'bg-primary-100 text-primary-600' : 'bg-gray-200 text-gray-400') }}">2</div>
                            <span class="text-xs text-gray-500 hidden sm:inline">Verifikasi</span>
                        </div>
                        <div class="w-8 h-0.5 {{ $step === 'reset' ? 'bg-primary-600' : 'bg-gray-200' }}"></div>
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold {{ $step === 'reset' ? 'bg-primary-600 text-white' : 'bg-gray-200 text-gray-400' }}">3</div>
                            <span class="text-xs text-gray-500 hidden sm:inline">Password</span>
                        </div>
                    </div>

                    {{-- Step 1: Email --}}
                    @if($step === 'email')
                    <form wire:submit="sendOtp" class="space-y-4">
                        @if($errors->has('email'))
                        <div class="bg-red-50 border border-red-200 text-red-600 text-sm p-3 rounded-xl flex items-start gap-2">
                            <i class="fas fa-exclamation-circle mt-0.5"></i>
                            <span>{{ $errors->first('email') }}</span>
                        </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                <input type="email" wire:model="email" required autofocus
                                    placeholder="Masukkan email terdaftar"
                                    class="w-full pl-11 pr-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                            </div>
                        </div>

                        <button type="submit" wire:loading.attr="disabled" 
                            class="w-full py-3 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-semibold rounded-xl shadow-lg shadow-primary-500/30 hover:shadow-xl transition flex items-center justify-center gap-2 disabled:opacity-50">
                            <span wire:loading wire:target="sendOtp"><i class="fas fa-spinner fa-spin"></i></span>
                            <span wire:loading.remove wire:target="sendOtp"><i class="fas fa-paper-plane"></i></span>
                            Kirim Kode OTP
                        </button>
                    </form>
                    @endif

                    {{-- Step 2: OTP Verification --}}
                    @if($step === 'otp')
                    <form wire:submit="verifyOtp" class="space-y-4">
                        <div class="bg-blue-50 border border-blue-200 text-blue-700 text-sm p-3 rounded-xl flex items-start gap-2">
                            <i class="fas fa-info-circle mt-0.5"></i>
                            <span>Kode OTP telah dikirim ke <strong>{{ $email }}</strong></span>
                        </div>

                        @if($errors->has('otp'))
                        <div class="bg-red-50 border border-red-200 text-red-600 text-sm p-3 rounded-xl flex items-start gap-2">
                            <i class="fas fa-exclamation-circle mt-0.5"></i>
                            <span>{{ $errors->first('otp') }}</span>
                        </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Kode OTP</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                    <i class="fas fa-shield-alt"></i>
                                </span>
                                <input type="text" wire:model="otp" required autofocus
                                    maxlength="6" inputmode="numeric" pattern="[0-9]*"
                                    placeholder="Masukkan 6 digit kode"
                                    class="w-full pl-11 pr-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition text-center text-2xl tracking-[0.5em] font-mono">
                            </div>
                        </div>

                        <button type="submit" wire:loading.attr="disabled" 
                            class="w-full py-3 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-semibold rounded-xl shadow-lg shadow-primary-500/30 hover:shadow-xl transition flex items-center justify-center gap-2 disabled:opacity-50">
                            <span wire:loading wire:target="verifyOtp"><i class="fas fa-spinner fa-spin"></i></span>
                            <span wire:loading.remove wire:target="verifyOtp"><i class="fas fa-check"></i></span>
                            Verifikasi
                        </button>

                        <div class="flex items-center justify-between text-sm">
                            <button type="button" wire:click="backToEmail" class="text-gray-500 hover:text-gray-700">
                                <i class="fas fa-arrow-left mr-1"></i> Ubah Email
                            </button>
                            <button type="button" wire:click="resendOtp" wire:loading.attr="disabled"
                                class="text-primary-600 hover:text-primary-700 disabled:text-gray-400"
                                x-data="{ countdown: @entangle('countdown') }"
                                x-init="setInterval(() => { if(countdown > 0) countdown-- }, 1000)"
                                :disabled="countdown > 0">
                                <span x-show="countdown > 0">Kirim ulang (<span x-text="countdown"></span>s)</span>
                                <span x-show="countdown <= 0">
                                    <span wire:loading wire:target="resendOtp"><i class="fas fa-spinner fa-spin"></i></span>
                                    <span wire:loading.remove wire:target="resendOtp">Kirim Ulang OTP</span>
                                </span>
                            </button>
                        </div>
                    </form>
                    @endif

                    {{-- Step 3: Reset Password --}}
                    @if($step === 'reset')
                    <form wire:submit="resetPassword" class="space-y-4">
                        <div class="bg-green-50 border border-green-200 text-green-700 text-sm p-3 rounded-xl flex items-start gap-2">
                            <i class="fas fa-check-circle mt-0.5"></i>
                            <span>Email terverifikasi. Silakan buat password baru.</span>
                        </div>

                        @if($errors->has('password'))
                        <div class="bg-red-50 border border-red-200 text-red-600 text-sm p-3 rounded-xl flex items-start gap-2">
                            <i class="fas fa-exclamation-circle mt-0.5"></i>
                            <span>{{ $errors->first('password') }}</span>
                        </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Password Baru</label>
                            <div class="relative" x-data="{ show: false }">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input :type="show ? 'text' : 'password'" wire:model="password" required autofocus
                                    placeholder="Minimal 8 karakter"
                                    class="w-full pl-11 pr-12 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                                <button type="button" @click="show = !show" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <i :class="show ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Konfirmasi Password</label>
                            <div class="relative" x-data="{ show: false }">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input :type="show ? 'text' : 'password'" wire:model="password_confirmation" required
                                    placeholder="Ulangi password baru"
                                    class="w-full pl-11 pr-12 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                                <button type="button" @click="show = !show" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <i :class="show ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" wire:loading.attr="disabled" 
                            class="w-full py-3 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-semibold rounded-xl shadow-lg shadow-primary-500/30 hover:shadow-xl transition flex items-center justify-center gap-2 disabled:opacity-50">
                            <span wire:loading wire:target="resetPassword"><i class="fas fa-spinner fa-spin"></i></span>
                            <span wire:loading.remove wire:target="resetPassword"><i class="fas fa-save"></i></span>
                            Simpan Password Baru
                        </button>
                    </form>
                    @endif

                    {{-- Back to Login --}}
                    <div class="mt-6 pt-5 border-t border-gray-100 text-center">
                        <a href="{{ route('login') }}" class="text-sm text-gray-500 hover:text-primary-600">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Login
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
