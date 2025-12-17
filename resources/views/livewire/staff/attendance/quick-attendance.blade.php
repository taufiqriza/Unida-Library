<div class="relative" x-data="quickAttendanceWidget()" @attendance-updated.window="refreshStatus()">
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
                <div id="quick-qr-reader" class="w-full aspect-video bg-gray-900 rounded-lg overflow-hidden relative">
                    <div x-show="!scanning" class="absolute inset-0 flex items-center justify-center text-white">
                        <div class="text-center">
                            <i class="fas fa-spinner fa-spin text-2xl mb-1 opacity-75"></i>
                            <p class="text-[10px] opacity-75">Membuka kamera...</p>
                        </div>
                    </div>
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
    scanning: false,
    html5QrCode: null,
    qrDetected: false,
    qrLocationName: '',
    hasCheckedIn: {{ $todayStatus['checkIn'] ? 'true' : 'false' }},
    hasCheckedOut: {{ $todayStatus['checkOut'] ? 'true' : 'false' }},

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
        this.initGps();
        
        // Listen for QR detection from Livewire
        Livewire.on('qr-detected', (data) => {
            this.qrDetected = true;
            this.qrLocationName = data.name;
            this.stopScanner();
        });
    },

    refreshStatus() {
        this.hasCheckedIn = true;
        // Close popup after successful action
        setTimeout(() => this.closePopup(), 1500);
    },

    togglePopup() {
        this.open = !this.open;
        if (!this.open) this.cleanup();
    },

    closePopup() {
        this.open = false;
        this.cleanup();
    },

    cleanup() {
        this.stopScanner();
    },

    setMode(newMode) {
        if (this.mode === newMode) return;
        if (this.mode === 'qr') this.stopScanner();
        this.mode = newMode;
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

        navigator.geolocation.getCurrentPosition(
            (pos) => {
                this.gpsLat = pos.coords.latitude;
                this.gpsLng = pos.coords.longitude;
                this.gpsStatus = 'active';
                this.gpsText = `GPS OK (±${Math.round(pos.coords.accuracy)}m)`;
                $wire.updateGps(this.gpsLat, this.gpsLng);
            },
            () => {
                this.gpsStatus = 'error';
                this.gpsText = 'Izinkan akses lokasi';
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    },

    startScanner() {
        const el = document.getElementById('quick-qr-reader');
        if (!el || typeof Html5Qrcode === 'undefined') return;

        el.innerHTML = '';
        this.html5QrCode = new Html5Qrcode("quick-qr-reader");
        this.html5QrCode.start(
            { facingMode: "environment" },
            { fps: 10, qrbox: { width: 180, height: 180 } },
            (text) => {
                $wire.handleQrScan(text);
            },
            () => {}
        ).then(() => {
            this.scanning = true;
        }).catch(() => {
            this.scanning = false;
        });
    },

    stopScanner() {
        if (this.html5QrCode && this.scanning) {
            this.html5QrCode.stop().catch(() => {});
            this.scanning = false;
        }
    }
}));
</script>
@endscript
