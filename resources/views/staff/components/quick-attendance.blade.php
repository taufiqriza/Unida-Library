{{-- Quick Attendance Popup Component --}}
@php
    $todayCheckIn = auth()->user() ? \App\Models\Attendance::checkInToday(auth()->id()) : null;
    $todayCheckOut = auth()->user() ? \App\Models\Attendance::checkOutToday(auth()->id()) : null;
    $locations = \App\Models\AttendanceLocation::active()
        ->where(function($q) {
            $user = auth()->user();
            if ($user->role === 'super_admin') {
                // Super admin sees all
            } else {
                $q->where('branch_id', $user->branch_id)
                  ->orWhereNull('branch_id');
            }
        })
        ->orderBy('name')
        ->get();
@endphp

<div class="relative" x-data="quickAttendance()" 
     @beforeunload.window="stopScanner()"
     x-on:livewire:navigating.window="stopScanner()"
     x-on:visibilitychange.window="if (document.hidden) stopScanner()">
    {{-- Trigger Button --}}
    <button @click="togglePopup()"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl font-semibold text-sm shadow hover:shadow-lg hover:-translate-y-0.5 transition-all"
            :class="hasCheckedIn && hasCheckedOut 
                ? 'bg-gradient-to-r from-gray-400 to-gray-500 text-white' 
                : hasCheckedIn 
                    ? 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white' 
                    : 'bg-gradient-to-r from-emerald-500 to-teal-600 text-white animate-pulse'">
        <i class="fas fa-fingerprint"></i>
        <span class="hidden sm:inline" x-text="buttonText"></span>
        <i class="fas fa-chevron-down text-xs transition-transform" :class="open ? 'rotate-180' : ''"></i>
    </button>

    {{-- Popup --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
         @click.away="closePopup()"
         @keydown.escape.window="closePopup()"
         class="absolute right-0 top-full mt-2 w-[360px] bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden z-50"
         x-cloak>
        
        {{-- Header --}}
        <div class="bg-gradient-to-r from-emerald-600 to-teal-700 px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2 text-white">
                <i class="fas fa-fingerprint"></i>
                <span class="font-semibold text-sm">Absensi Cepat</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs text-emerald-100">{{ now()->format('H:i') }}</span>
                <button @click="closePopup()" class="w-6 h-6 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center text-white transition">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
        </div>

        {{-- Status Today --}}
        <div class="p-4 border-b border-gray-100 bg-gray-50">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $todayCheckIn ? 'bg-emerald-500 text-white' : 'bg-gray-200 text-gray-400' }}">
                        <i class="fas {{ $todayCheckIn ? 'fa-check' : 'fa-arrow-right-to-bracket' }}"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Masuk</p>
                        <p class="text-sm font-bold {{ $todayCheckIn ? 'text-emerald-600' : 'text-gray-400' }}">
                            {{ $todayCheckIn ? \Carbon\Carbon::parse($todayCheckIn->scanned_at)->format('H:i') : '--:--' }}
                        </p>
                    </div>
                </div>
                <i class="fas fa-arrow-right text-gray-300"></i>
                <div class="flex items-center gap-3">
                    <div>
                        <p class="text-xs text-gray-500 text-right">Pulang</p>
                        <p class="text-sm font-bold {{ $todayCheckOut ? 'text-blue-600' : 'text-gray-400' }}">
                            {{ $todayCheckOut ? \Carbon\Carbon::parse($todayCheckOut->scanned_at)->format('H:i') : '--:--' }}
                        </p>
                    </div>
                    <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $todayCheckOut ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-400' }}">
                        <i class="fas {{ $todayCheckOut ? 'fa-check' : 'fa-arrow-right-from-bracket' }}"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Mode Switcher --}}
        <div class="p-4 border-b border-gray-100">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Metode Absensi</p>
            <div class="flex bg-gray-100 rounded-xl p-1">
                <button @click="setMode('location')" 
                        class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium transition"
                        :class="mode === 'location' ? 'bg-white shadow text-emerald-600' : 'text-gray-500 hover:text-gray-700'">
                    <i class="fas fa-map-marker-alt"></i>
                    Pilih Lokasi
                </button>
                <button @click="setMode('qr')" 
                        class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium transition"
                        :class="mode === 'qr' ? 'bg-white shadow text-violet-600' : 'text-gray-500 hover:text-gray-700'">
                    <i class="fas fa-qrcode"></i>
                    Scan QR
                </button>
            </div>
        </div>

        {{-- Action Area --}}
        <div class="p-4">
            {{-- Location Select Mode --}}
            <div x-show="mode === 'location'" class="space-y-3">
                @if($locations->count() > 0)
                    <select x-model="selectedLocation" class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">-- Pilih Lokasi --</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                        @endforeach
                    </select>
                @else
                    <div class="text-center py-4 text-gray-400">
                        <i class="fas fa-map-marker-alt text-2xl mb-2"></i>
                        <p class="text-sm">Belum ada lokasi tersedia</p>
                    </div>
                @endif
            </div>

            {{-- QR Scan Mode - Auto Start Camera --}}
            <div x-show="mode === 'qr'" class="space-y-3">
                <div id="quick-qr-reader" class="w-full aspect-video bg-gray-900 rounded-xl overflow-hidden relative">
                    <div x-show="!scanning" class="absolute inset-0 flex items-center justify-center text-white">
                        <div class="text-center">
                            <i class="fas fa-spinner fa-spin text-3xl mb-2 opacity-75"></i>
                            <p class="text-xs opacity-75">Membuka kamera...</p>
                        </div>
                    </div>
                </div>
                
                {{-- QR Detected --}}
                <div x-show="qrDetected" class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center gap-3">
                    <i class="fas fa-check-circle text-emerald-500 text-xl"></i>
                    <div>
                        <p class="font-medium text-emerald-800">QR Terdeteksi!</p>
                        <p class="text-sm text-emerald-600" x-text="qrLocationName"></p>
                    </div>
                </div>
                
                {{-- Scanner Controls --}}
                <div class="flex gap-2">
                    <button @click="toggleScanner()" 
                            class="flex-1 py-2.5 font-semibold rounded-xl transition flex items-center justify-center gap-2 text-sm"
                            :class="scanning ? 'bg-red-100 hover:bg-red-200 text-red-700' : 'bg-violet-100 hover:bg-violet-200 text-violet-700'">
                        <i class="fas" :class="scanning ? 'fa-stop' : 'fa-camera'"></i>
                        <span x-text="scanning ? 'Stop' : 'Mulai Ulang'"></span>
                    </button>
                </div>
            </div>
        </div>

        {{-- GPS Status --}}
        <div class="px-4 pb-3">
            <div class="p-2.5 rounded-lg text-xs flex items-center gap-2"
                 :class="gpsStatus === 'active' ? 'bg-emerald-50 text-emerald-700' : gpsStatus === 'loading' ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-700'">
                <i class="fas" :class="gpsStatus === 'active' ? 'fa-location-dot' : gpsStatus === 'loading' ? 'fa-spinner fa-spin' : 'fa-location-crosshairs'"></i>
                <span x-text="gpsText"></span>
            </div>
        </div>

        {{-- Action Button --}}
        <div class="px-4 pb-4">
            @if(!$todayCheckIn)
                <button @click="doCheckIn()" 
                        :disabled="(!selectedLocation && !qrDetected) || gpsStatus !== 'active'"
                        class="w-full py-3.5 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 disabled:from-gray-300 disabled:to-gray-400 text-white font-bold rounded-xl shadow-lg transition flex items-center justify-center gap-2">
                    <i class="fas fa-arrow-right-to-bracket"></i>
                    CHECK IN
                </button>
            @elseif(!$todayCheckOut)
                <button @click="doCheckOut()" 
                        :disabled="gpsStatus !== 'active'"
                        class="w-full py-3.5 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 disabled:from-gray-300 disabled:to-gray-400 text-white font-bold rounded-xl shadow-lg transition flex items-center justify-center gap-2">
                    <i class="fas fa-arrow-right-from-bracket"></i>
                    CHECK OUT
                </button>
            @else
                <div class="w-full py-3.5 bg-gray-100 text-gray-500 font-bold rounded-xl text-center flex items-center justify-center gap-2">
                    <i class="fas fa-check-double"></i>
                    Absensi Hari Ini Lengkap
                </div>
            @endif
        </div>

        {{-- Footer Link --}}
        <div class="px-4 pb-4 text-center">
            <a href="{{ route('staff.attendance.index') }}" class="text-xs text-gray-400 hover:text-emerald-600 transition">
                <i class="fas fa-external-link-alt mr-1"></i>
                Buka Halaman Kehadiran Lengkap
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
function quickAttendance() {
    return {
        open: false,
        mode: 'location',
        selectedLocation: '',
        gpsStatus: 'loading',
        gpsText: 'Memuat GPS...',
        gpsLat: null,
        gpsLng: null,
        hasCheckedIn: {{ $todayCheckIn ? 'true' : 'false' }},
        hasCheckedOut: {{ $todayCheckOut ? 'true' : 'false' }},
        
        // QR Scanner
        html5QrCode: null,
        scanning: false,
        qrDetected: false,
        qrLocationName: '',
        qrLocationId: null,
        
        get buttonText() {
            if (this.hasCheckedIn && this.hasCheckedOut) return 'Selesai';
            if (this.hasCheckedIn) return 'Check Out';
            return 'Check In';
        },

        init() {
            this.initGps();
        },

        togglePopup() {
            this.open = !this.open;
            if (!this.open) {
                this.stopScanner();
            }
        },

        closePopup() {
            this.open = false;
            this.stopScanner();
        },

        setMode(newMode) {
            if (this.mode === newMode) return;
            
            // Stop scanner when switching away from QR
            if (this.mode === 'qr') {
                this.stopScanner();
            }
            
            this.mode = newMode;
            
            // Auto-start scanner when switching to QR
            if (newMode === 'qr') {
                this.$nextTick(() => {
                    setTimeout(() => {
                        this.startScanner();
                    }, 300);
                });
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
                    this.gpsText = `GPS Aktif (±${Math.round(pos.coords.accuracy)}m)`;
                },
                (err) => {
                    this.gpsStatus = 'error';
                    this.gpsText = 'Izinkan akses lokasi';
                },
                { enableHighAccuracy: true, timeout: 15000 }
            );
        },

        startScanner() {
            const qrElement = document.getElementById('quick-qr-reader');
            if (!qrElement) return;

            qrElement.innerHTML = '';

            if (typeof Html5Qrcode === 'undefined') {
                console.error('Html5Qrcode not loaded');
                return;
            }

            this.html5QrCode = new Html5Qrcode("quick-qr-reader");
            this.html5QrCode.start(
                { facingMode: "environment" },
                { fps: 10, qrbox: { width: 200, height: 200 } },
                (decodedText) => {
                    console.log('Quick QR Decoded:', decodedText);
                    this.handleQrResult(decodedText);
                },
                (errorMessage) => { }
            ).then(() => {
                this.scanning = true;
            }).catch((err) => {
                console.error('Quick QR Scanner error:', err);
                this.scanning = false;
            });
        },

        stopScanner() {
            if (this.html5QrCode && this.scanning) {
                this.html5QrCode.stop().then(() => {
                    this.scanning = false;
                    const qrElement = document.getElementById('quick-qr-reader');
                    if (qrElement) qrElement.innerHTML = '';
                }).catch(() => {});
            }
        },

        toggleScanner() {
            if (this.scanning) {
                this.stopScanner();
            } else {
                this.startScanner();
            }
        },

        handleQrResult(code) {
            // Try to parse QR code and find location
            let qrCode = code;
            try {
                const data = JSON.parse(code);
                if (data.code) qrCode = data.code;
            } catch (e) {}

            // Find location by QR code 
            const locations = @json($locations->map(fn($l) => ['id' => $l->id, 'name' => $l->name, 'qr' => $l->qr_code]));
            const location = locations.find(l => l.qr === qrCode);
            
            if (location) {
                this.qrDetected = true;
                this.qrLocationName = location.name;
                this.qrLocationId = location.id;
                this.selectedLocation = location.id;
                this.stopScanner();
            }
        },

        doCheckIn() {
            const locId = this.selectedLocation || this.qrLocationId;
            if (!locId || this.gpsStatus !== 'active') return;
            
            this.stopScanner();
            const url = new URL('{{ route("staff.attendance.index") }}', window.location.origin);
            url.searchParams.set('action', 'checkin');
            url.searchParams.set('location', locId);
            url.searchParams.set('lat', this.gpsLat);
            url.searchParams.set('lng', this.gpsLng);
            window.location.href = url.toString();
        },

        doCheckOut() {
            if (this.gpsStatus !== 'active') return;
            
            this.stopScanner();
            const url = new URL('{{ route("staff.attendance.index") }}', window.location.origin);
            url.searchParams.set('action', 'checkout');
            url.searchParams.set('lat', this.gpsLat);
            url.searchParams.set('lng', this.gpsLng);
            window.location.href = url.toString();
        }
    }
}
</script>
@endpush
