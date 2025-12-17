<div class="relative" x-data="quickAttendanceWidget()" 
     @attendance-updated.window="refreshStatus()"
     @notify.window="showToast($event.detail)">
    {{-- Trigger Button --}}
    <button @click="togglePopup()"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl font-semibold text-sm shadow hover:shadow-lg hover:-translate-y-0.5 transition-all"
            :class="buttonClass">
        <i class="fas fa-fingerprint"></i>
        <span class="hidden sm:inline" x-text="buttonText"></span>
        <i class="fas fa-chevron-down text-xs transition-transform" :class="open ? 'rotate-180' : ''"></i>
    </button>

    {{-- Popup --}}
    <div x-show="open" x-cloak
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click.away="closePopup()"
         @keydown.escape.window="closePopup()"
         class="absolute right-0 top-full mt-2 w-[340px] bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden z-50">
        
        {{-- Header --}}
        <div class="bg-gradient-to-r from-emerald-600 to-teal-700 px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2 text-white">
                <i class="fas fa-fingerprint"></i>
                <span class="font-semibold text-sm">Absensi Cepat</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs text-emerald-100">{{ now()->format('H:i') }}</span>
                <button @click="closePopup()" class="w-6 h-6 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center text-white">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
        </div>

        {{-- Toast Notification (inside popup) --}}
        <div x-show="toastShow" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="px-3 py-2 text-xs font-medium flex items-center gap-2"
             :class="{
                 'bg-emerald-50 text-emerald-700 border-b border-emerald-100': toastType === 'success',
                 'bg-red-50 text-red-700 border-b border-red-100': toastType === 'error',
                 'bg-amber-50 text-amber-700 border-b border-amber-100': toastType === 'warning'
             }">
            <i class="fas" :class="{
                'fa-check-circle': toastType === 'success',
                'fa-exclamation-circle': toastType === 'error',
                'fa-exclamation-triangle': toastType === 'warning'
            }"></i>
            <span x-text="toastMessage" class="flex-1"></span>
            <button @click="toastShow = false" class="opacity-50 hover:opacity-100">
                <i class="fas fa-times"></i>
            </button>
        </div>

        {{-- Status Today --}}
        <div class="p-3 border-b border-gray-100 bg-gray-50">
            <div class="flex items-center justify-between text-center">
                <div class="flex-1">
                    <div class="w-8 h-8 mx-auto rounded-full flex items-center justify-center {{ $todayStatus['checkIn'] ? 'bg-emerald-500 text-white' : 'bg-gray-200 text-gray-400' }}">
                        <i class="fas {{ $todayStatus['checkIn'] ? 'fa-check' : 'fa-arrow-right-to-bracket' }} text-xs"></i>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Masuk</p>
                    <p class="text-sm font-bold {{ $todayStatus['checkIn'] ? 'text-emerald-600' : 'text-gray-400' }}">
                        {{ $todayStatus['checkIn'] ? \Carbon\Carbon::parse($todayStatus['checkIn']->scanned_at)->format('H:i') : '--:--' }}
                    </p>
                </div>
                <i class="fas fa-arrow-right text-gray-300"></i>
                <div class="flex-1">
                    <div class="w-8 h-8 mx-auto rounded-full flex items-center justify-center {{ $todayStatus['checkOut'] ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-400' }}">
                        <i class="fas {{ $todayStatus['checkOut'] ? 'fa-check' : 'fa-arrow-right-from-bracket' }} text-xs"></i>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Pulang</p>
                    <p class="text-sm font-bold {{ $todayStatus['checkOut'] ? 'text-blue-600' : 'text-gray-400' }}">
                        {{ $todayStatus['checkOut'] ? \Carbon\Carbon::parse($todayStatus['checkOut']->scanned_at)->format('H:i') : '--:--' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Mode Switcher --}}
        <div class="p-3 border-b border-gray-100">
            <div class="flex bg-gray-100 rounded-lg p-0.5">
                <button @click="setMode('location')" 
                        class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 rounded-md text-xs font-medium transition"
                        :class="mode === 'location' ? 'bg-white shadow text-emerald-600' : 'text-gray-500'">
                    <i class="fas fa-map-marker-alt"></i>
                    Lokasi
                </button>
                <button @click="setMode('qr')" 
                        class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 rounded-md text-xs font-medium transition"
                        :class="mode === 'qr' ? 'bg-white shadow text-violet-600' : 'text-gray-500'">
                    <i class="fas fa-qrcode"></i>
                    Scan QR
                </button>
            </div>
        </div>

        {{-- Action Area --}}
        <div class="p-3">
            {{-- Location Select --}}
            <div x-show="mode === 'location'">
                <select wire:model.live="selectedLocationId" 
                        class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 mb-3">
                    <option value="">-- Pilih Lokasi --</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- QR Scanner --}}
            <div x-show="mode === 'qr'" class="mb-3">
                <div class="relative w-full aspect-video bg-gray-900 rounded-lg overflow-hidden">
                    {{-- Loading Overlay (shown when not scanning) --}}
                    <div x-show="!scanning" x-cloak class="absolute inset-0 z-10 flex items-center justify-center text-white bg-gray-900">
                        <div class="text-center">
                            <i class="fas fa-spinner fa-spin text-2xl mb-1 opacity-75"></i>
                            <p class="text-[10px] opacity-75">Membuka kamera...</p>
                        </div>
                    </div>
                    {{-- QR Reader Container (Html5Qrcode will inject video here) --}}
                    <div id="quick-qr-reader" class="w-full h-full"></div>
                </div>
                <div x-show="qrDetected" class="mt-2 p-2 bg-emerald-50 border border-emerald-200 rounded-lg flex items-center gap-2">
                    <i class="fas fa-check-circle text-emerald-500"></i>
                    <span class="text-xs text-emerald-700 font-medium" x-text="'Lokasi: ' + qrLocationName"></span>
                </div>
            </div>

            {{-- GPS Status --}}
            <div class="p-2 rounded-lg text-xs flex items-center gap-2 mb-3"
                 :class="gpsStatus === 'active' ? 'bg-emerald-50 text-emerald-700' : gpsStatus === 'loading' ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-700'">
                <i class="fas" :class="gpsStatus === 'active' ? 'fa-location-dot' : gpsStatus === 'loading' ? 'fa-spinner fa-spin' : 'fa-location-crosshairs'"></i>
                <span x-text="gpsText"></span>
            </div>

            {{-- Action Button --}}
            @if(!$todayStatus['checkIn'])
                <button wire:click="checkIn" wire:loading.attr="disabled"
                        :disabled="!canCheckIn"
                        class="w-full py-3 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 disabled:from-gray-300 disabled:to-gray-400 text-white font-bold rounded-xl transition flex items-center justify-center gap-2">
                    <span wire:loading.remove wire:target="checkIn">
                        <i class="fas fa-arrow-right-to-bracket"></i> CHECK IN
                    </span>
                    <span wire:loading wire:target="checkIn">
                        <i class="fas fa-spinner fa-spin"></i> Memproses...
                    </span>
                </button>
            @elseif(!$todayStatus['checkOut'])
                <button wire:click="checkOut" wire:loading.attr="disabled"
                        :disabled="gpsStatus !== 'active'"
                        class="w-full py-3 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 disabled:from-gray-300 disabled:to-gray-400 text-white font-bold rounded-xl transition flex items-center justify-center gap-2">
                    <span wire:loading.remove wire:target="checkOut">
                        <i class="fas fa-arrow-right-from-bracket"></i> CHECK OUT
                    </span>
                    <span wire:loading wire:target="checkOut">
                        <i class="fas fa-spinner fa-spin"></i> Memproses...
                    </span>
                </button>
            @else
                <div class="w-full py-3 bg-gray-100 text-gray-500 font-bold rounded-xl text-center flex items-center justify-center gap-2">
                    <i class="fas fa-check-double"></i>
                    Selesai Hari Ini
                </div>
            @endif
        </div>

        {{-- Footer --}}
        <div class="px-3 pb-3 text-center">
            <a href="{{ route('staff.attendance.index') }}" wire:navigate class="text-[10px] text-gray-400 hover:text-emerald-600">
                <i class="fas fa-external-link-alt mr-1"></i>Halaman Kehadiran
            </a>
        </div>
    </div>
</div>

@script
<script>
Alpine.data('quickAttendanceWidget', () => ({
    open: false,
    mode: 'location',
    gpsStatus: 'loading',
    gpsText: 'Memuat GPS...',
    gpsLat: null,
    gpsLng: null,
    gpsWatchId: null,
    scanning: false,
    html5QrCode: null,
    qrDetected: false,
    qrLocationName: '',
    hasCheckedIn: {{ $todayStatus['checkIn'] ? 'true' : 'false' }},
    hasCheckedOut: {{ $todayStatus['checkOut'] ? 'true' : 'false' }},
    
    // Toast notification
    toastShow: false,
    toastMessage: '',
    toastType: 'success',
    toastTimeout: null,

    get buttonText() {
        if (this.hasCheckedIn && this.hasCheckedOut) return 'Selesai';
        if (this.hasCheckedIn) return 'Check Out';
        return 'Check In';
    },

    get buttonClass() {
        if (this.hasCheckedIn && this.hasCheckedOut) return 'bg-gradient-to-r from-gray-400 to-gray-500 text-white';
        if (this.hasCheckedIn) return 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white';
        return 'bg-gradient-to-r from-emerald-500 to-teal-600 text-white animate-pulse';
    },

    get canCheckIn() {
        return this.gpsStatus === 'active' && ($wire.selectedLocationId || this.qrDetected);
    },

    init() {
        // Listen for QR detection from Livewire
        Livewire.on('qr-detected', (data) => {
            this.qrDetected = true;
            this.qrLocationName = data.name;
            this.stopScanner();
            
            // Auto check-in after QR scan if GPS is ready and not checked in yet
            if (this.gpsStatus === 'active' && !this.hasCheckedIn) {
                // Small delay for UX feedback
                setTimeout(() => {
                    $wire.checkIn();
                }, 500);
            }
        });
    },

    showToast(detail) {
        // Only show toast if popup is open
        if (!this.open) return;
        
        if (this.toastTimeout) clearTimeout(this.toastTimeout);
        
        this.toastType = detail.type || 'success';
        this.toastMessage = detail.message || '';
        this.toastShow = true;
        
        // Auto-hide after 5 seconds
        this.toastTimeout = setTimeout(() => {
            this.toastShow = false;
        }, 5000);
    },

    refreshStatus() {
        // Update status but DON'T close popup - let user see the result
        this.hasCheckedIn = true;
        // Checkout success will also trigger this
        if (this.hasCheckedIn && !this.hasCheckedOut) {
            this.hasCheckedOut = true;
        }
    },

    togglePopup() {
        this.open = !this.open;
        if (this.open) {
            // Refresh GPS when popup opens
            this.initGps();
            // Reset QR state
            this.qrDetected = false;
            this.qrLocationName = '';
        } else {
            this.cleanup();
        }
    },

    closePopup() {
        this.open = false;
        this.cleanup();
    },

    cleanup() {
        this.stopScanner();
        // Stop GPS watch if active
        if (this.gpsWatchId) {
            navigator.geolocation.clearWatch(this.gpsWatchId);
            this.gpsWatchId = null;
        }
    },

    setMode(newMode) {
        if (this.mode === newMode) return;
        
        // Stop scanner if leaving QR mode
        if (this.mode === 'qr') {
            this.stopScanner();
        }
        
        // Reset QR detected state when switching modes
        this.qrDetected = false;
        this.qrLocationName = '';
        
        this.mode = newMode;
        
        // Start scanner if entering QR mode
        if (newMode === 'qr') {
            this.$nextTick(() => setTimeout(() => this.startScanner(), 200));
        }
    },
    
    initGps() {
        if (!navigator.geolocation) {
            this.gpsStatus = 'error';
            this.gpsText = 'GPS tidak didukung';
            return;
        }

        this.gpsStatus = 'loading';
        this.gpsText = 'Memuat GPS...';

        // Clear previous watch
        if (this.gpsWatchId) {
            navigator.geolocation.clearWatch(this.gpsWatchId);
        }

        // Get current position first
        navigator.geolocation.getCurrentPosition(
            (pos) => {
                this.updateGpsPosition(pos);
            },
            (err) => {
                this.gpsStatus = 'error';
                this.gpsText = 'Izinkan akses lokasi';
                console.error('GPS Error:', err);
            },
            { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
        );

        // Then watch for updates
        this.gpsWatchId = navigator.geolocation.watchPosition(
            (pos) => {
                this.updateGpsPosition(pos);
            },
            () => {},
            { enableHighAccuracy: true, maximumAge: 5000 }
        );
    },

    updateGpsPosition(pos) {
        this.gpsLat = pos.coords.latitude;
        this.gpsLng = pos.coords.longitude;
        this.gpsStatus = 'active';
        this.gpsText = `GPS OK (±${Math.round(pos.coords.accuracy)}m)`;
        // Send to Livewire backend
        $wire.updateGps(this.gpsLat, this.gpsLng);
    },

    startScanner() {
        const qrElement = document.getElementById('quick-qr-reader');
        if (!qrElement) {
            console.error('QR reader element not found');
            return;
        }

        // If already scanning, stop first
        if (this.scanning) {
            if (this.html5QrCode) {
                this.html5QrCode.stop().then(() => {
                    this.scanning = false;
                    qrElement.innerHTML = '';
                }).catch(err => console.error('Stop error:', err));
            }
            return;
        }

        // Clear previous content
        qrElement.innerHTML = '';

        this.html5QrCode = new Html5Qrcode("quick-qr-reader");
        this.html5QrCode.start(
            { facingMode: "environment" },
            { fps: 10, qrbox: { width: 180, height: 180 } },
            (decodedText) => {
                console.log('QR Decoded:', decodedText);
                $wire.handleQrScan(decodedText);
                // Don't stop on scan - let user select
            },
            (errorMessage) => { /* ignore scan errors */ }
        ).then(() => {
            this.scanning = true;
            console.log('QR Scanner started successfully');
        }).catch((err) => {
            console.error('QR Scanner error:', err);
            this.scanning = false;
            // Show error toast
            this.showToast({
                type: 'error',
                message: 'Tidak dapat mengakses kamera. Pastikan izin kamera sudah diberikan.'
            });
        });
    },

    stopScanner() {
        if (this.html5QrCode && this.scanning) {
            this.html5QrCode.stop().then(() => {
                this.scanning = false;
                const el = document.getElementById('quick-qr-reader');
                if (el) el.innerHTML = '';
            }).catch(() => {});
        }
    }
}));
</script>
@endscript
