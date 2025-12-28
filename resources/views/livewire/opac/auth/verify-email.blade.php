<div>
    <div class="min-h-[70vh] flex items-center justify-center py-8 px-4">
        <div class="w-full max-w-md">
            <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 overflow-hidden">
                <div class="bg-gradient-to-r from-primary-600 to-primary-700 p-6 text-center text-white">
                    <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-envelope-open-text text-3xl"></i>
                    </div>
                    <h1 class="text-xl font-bold">{{ __('opac.auth.verify_email.title') }}</h1>
                    <p class="text-primary-200 text-sm mt-1">{{ __('opac.auth.verify_email.subtitle') }}</p>
                </div>

                <div class="p-6">
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-5">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-info-circle text-blue-500"></i>
                            <div class="text-sm">
                                <p class="text-blue-800">{{ __('opac.auth.verify_email.code_sent') }}</p>
                                <p class="font-semibold text-blue-900">{{ $member->email }}</p>
                            </div>
                        </div>
                    </div>

                    @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-600 text-sm p-4 rounded-xl mb-5">
                        @foreach($errors->all() as $error)
                        <p><i class="fas fa-exclamation-circle mr-1"></i>{{ $error }}</p>
                        @endforeach
                    </div>
                    @endif

                    <form wire:submit="verify" x-data="otpForm()">
                        <div class="mb-5">
                            <label class="block text-sm font-medium text-gray-700 mb-3 text-center">{{ __('opac.auth.verify_email.enter_code') }}</label>
                            <div class="flex justify-center gap-2" dir="ltr">
                                @for($i = 0; $i < 6; $i++)
                                <input type="text" maxlength="1" 
                                       x-ref="otp{{ $i }}"
                                       @input="handleInput($event, {{ $i }})"
                                       @keydown.backspace="handleBackspace($event, {{ $i }})"
                                       @paste="handlePaste($event)"
                                       class="w-12 h-14 text-center text-2xl font-bold border-2 border-gray-200 rounded-xl focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition">
                                @endfor
                            </div>
                            <input type="hidden" x-model="otpValue" x-init="$watch('otpValue', v => $wire.otp = v)">
                        </div>

                        <button type="submit" class="w-full py-3 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-semibold rounded-xl shadow-lg shadow-primary-500/30 hover:shadow-xl transition flex items-center justify-center gap-2">
                            <i class="fas fa-check-circle"></i> {{ __('opac.auth.verify_email.verify') }}
                        </button>
                    </form>

                    <div class="mt-5 pt-5 border-t border-gray-100 text-center" x-data="resendTimer({{ $resendInfo['wait_seconds'] }})">
                        <p class="text-sm text-gray-500 mb-2">{{ __('opac.auth.verify_email.not_received') }}</p>
                        <button @click="resendOtp()" 
                                :disabled="countdown > 0 || loading"
                                :class="countdown > 0 || loading ? 'text-gray-400 cursor-not-allowed' : 'text-primary-600 hover:text-primary-700'"
                                class="text-sm font-semibold transition">
                            <span x-show="loading"><i class="fas fa-spinner fa-spin mr-1"></i> {{ __('opac.auth.verify_email.sending') }}</span>
                            <span x-show="!loading && countdown > 0">{{ __('opac.auth.verify_email.resend_in') }} <span x-text="countdown"></span>s</span>
                            <span x-show="!loading && countdown <= 0"><i class="fas fa-redo mr-1"></i> {{ __('opac.auth.verify_email.resend_code') }}</span>
                        </button>
                    </div>

                    <div class="mt-4 text-center">
                        <a href="{{ route('opac.register') }}" class="text-sm text-gray-500 hover:text-gray-700">
                            <i class="fas fa-arrow-left mr-1"></i> {{ __('opac.auth.verify_email.back_to_register') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function otpForm() {
        return {
            otpValue: '',
            handleInput(e, index) {
                const value = e.target.value;
                if (!/^\d$/.test(value)) {
                    e.target.value = '';
                    return;
                }
                if (index < 5) this.$refs['otp' + (index + 1)].focus();
                this.updateOtpValue();
            },
            handleBackspace(e, index) {
                if (e.target.value === '' && index > 0) {
                    this.$refs['otp' + (index - 1)].focus();
                }
                this.updateOtpValue();
            },
            handlePaste(e) {
                e.preventDefault();
                const paste = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, 6);
                for (let i = 0; i < paste.length; i++) {
                    if (this.$refs['otp' + i]) this.$refs['otp' + i].value = paste[i];
                }
                this.updateOtpValue();
            },
            updateOtpValue() {
                let otp = '';
                for (let i = 0; i < 6; i++) {
                    otp += this.$refs['otp' + i]?.value || '';
                }
                this.otpValue = otp;
            }
        }
    }

    function resendTimer(initialWait) {
        return {
            countdown: initialWait,
            loading: false,
            init() {
                if (this.countdown > 0) this.startTimer();
                Livewire.on('otp-resent', () => {
                    this.countdown = 60;
                    this.startTimer();
                    this.loading = false;
                });
            },
            startTimer() {
                const interval = setInterval(() => {
                    this.countdown--;
                    if (this.countdown <= 0) clearInterval(interval);
                }, 1000);
            },
            resendOtp() {
                this.loading = true;
                @this.resendOtp();
            }
        }
    }
    </script>
</div>
