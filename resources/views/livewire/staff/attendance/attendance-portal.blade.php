@section('title', 'Kehadiran')

<div class="space-y-5" x-data="attendanceApp()" x-init="initGeolocation()"
     @auto-checkin.window="$nextTick(() => $wire.checkIn())"
     @auto-checkout.window="$nextTick(() => $wire.checkOut())"
     @beforeunload.window="cleanupScanner()"
     x-on:livewire:navigating.window="cleanupScanner()"
     x-on:visibilitychange.window="if (document.hidden) cleanupScanner()"
>
    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 via-teal-500 to-cyan-600 rounded-2xl flex items-center justify-center text-white shadow-xl shadow-emerald-500/30">
                <i class="fas fa-fingerprint text-2xl"></i>
            </div>
            <div>
                <h1 class="text-xl lg:text-2xl font-bold text-gray-900">Kehadiran</h1>
                <p class="text-sm text-gray-500 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full animate-pulse" :class="gpsStatus === 'active' ? 'bg-emerald-500' : gpsStatus === 'loading' ? 'bg-amber-500' : 'bg-red-500'"></span>
                    <span x-text="gpsText"></span>
                </p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            @if($isSuperAdmin)
            <select wire:model.live="selectedBranchId" class="px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm">
                <option value="">🌐 Semua Cabang</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                @endforeach
            </select>
            @endif
            <div class="text-right hidden md:block">
                <p class="text-2xl font-bold text-gray-900">{{ now()->format('H:i') }}</p>
                <p class="text-xs text-gray-500">{{ now()->locale('id')->isoFormat('dddd, D MMM Y') }}</p>
            </div>
        </div>
    </div>

    {{-- Quick Stats --}}
    @if($isAdmin)
    <div class="grid grid-cols-3 gap-3">
        <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center text-emerald-600">
                    <i class="fas fa-user-check"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_present'] }}</p>
                    <p class="text-xs text-gray-500">Hadir Hari Ini</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_on_time'] }}</p>
                    <p class="text-xs text-gray-500">Tepat Waktu</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center text-amber-600">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_late'] }}</p>
                    <p class="text-xs text-gray-500">Terlambat</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Tab Navigation --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-1.5">
        <div class="flex gap-1">
            <button wire:click="setActiveTab('absen')" 
                    class="flex-1 px-4 py-2.5 rounded-xl font-medium text-sm transition flex items-center justify-center gap-2
                    {{ $activeTab === 'absen' ? 'bg-gradient-to-r from-emerald-500 to-teal-600 text-white shadow-lg' : 'text-gray-600 hover:bg-gray-100' }}">
                <i class="fas fa-fingerprint"></i>
                <span class="hidden sm:inline">Absen</span>
            </button>
            <button wire:click="setActiveTab('riwayat')" 
                    class="flex-1 px-4 py-2.5 rounded-xl font-medium text-sm transition flex items-center justify-center gap-2
                    {{ $activeTab === 'riwayat' ? 'bg-gradient-to-r from-emerald-500 to-teal-600 text-white shadow-lg' : 'text-gray-600 hover:bg-gray-100' }}">
                <i class="fas fa-history"></i>
                <span class="hidden sm:inline">Riwayat</span>
            </button>
            <button wire:click="setActiveTab('peta')" 
                    class="flex-1 px-4 py-2.5 rounded-xl font-medium text-sm transition flex items-center justify-center gap-2
                    {{ $activeTab === 'peta' ? 'bg-gradient-to-r from-emerald-500 to-teal-600 text-white shadow-lg' : 'text-gray-600 hover:bg-gray-100' }}">
                <i class="fas fa-map-marked-alt"></i>
                <span class="hidden sm:inline">Peta</span>
            </button>
            @if($isAdmin)
            <button wire:click="setActiveTab('lokasi')" 
                    class="flex-1 px-4 py-2.5 rounded-xl font-medium text-sm transition flex items-center justify-center gap-2
                    {{ $activeTab === 'lokasi' ? 'bg-gradient-to-r from-emerald-500 to-teal-600 text-white shadow-lg' : 'text-gray-600 hover:bg-gray-100' }}">
                <i class="fas fa-map-pin"></i>
                <span class="hidden sm:inline">Lokasi</span>
            </button>
            @endif
        </div>
    </div>

    {{-- ===== TAB: ABSEN ===== --}}
    @if($activeTab === 'absen')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- Status Hari Ini --}}
        <div class="space-y-4">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-calendar-day text-emerald-500"></i>
                    Status Hari Ini
                </h3>
                <div class="space-y-3">
                    {{-- Check In Status --}}
                    <div class="flex items-center gap-3 p-3 rounded-xl {{ $todayStatus['check_in'] ? 'bg-emerald-50' : 'bg-gray-50' }}">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $todayStatus['check_in'] ? 'bg-emerald-500 text-white' : 'bg-gray-200 text-gray-400' }}">
                            <i class="fas {{ $todayStatus['check_in'] ? 'fa-check' : 'fa-arrow-right-to-bracket' }}"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-700">Masuk</p>
                            @if($todayStatus['check_in'])
                                <p class="text-lg font-bold {{ $todayStatus['check_in']->is_late ? 'text-amber-600' : 'text-emerald-600' }}">
                                    {{ \Carbon\Carbon::parse($todayStatus['check_in']->scanned_at)->format('H:i') }}
                                    @if($todayStatus['check_in']->is_late)
                                        <span class="text-xs font-normal">(+{{ $todayStatus['check_in']->late_minutes }}m)</span>
                                    @endif
                                </p>
                            @else
                                <p class="text-sm text-gray-400">Belum check-in</p>
                            @endif
                        </div>
                        @if($todayStatus['check_in'])
                            <i class="fas {{ $todayStatus['check_in']->verification_icon }} text-gray-400"></i>
                        @endif
                    </div>

                    {{-- Check Out Status --}}
                    <div class="flex items-center gap-3 p-3 rounded-xl {{ $todayStatus['check_out'] ? 'bg-blue-50' : 'bg-gray-50' }}">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $todayStatus['check_out'] ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-400' }}">
                            <i class="fas {{ $todayStatus['check_out'] ? 'fa-check' : 'fa-arrow-right-from-bracket' }}"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-700">Pulang</p>
                            @if($todayStatus['check_out'])
                                <p class="text-lg font-bold text-blue-600">
                                    {{ \Carbon\Carbon::parse($todayStatus['check_out']->scanned_at)->format('H:i') }}
                                </p>
                            @else
                                <p class="text-sm text-gray-400">Belum check-out</p>
                            @endif
                        </div>
                    </div>

                    {{-- Duration --}}
                    @if($todayStatus['check_in'])
                    <div class="text-center p-3 bg-gradient-to-r from-emerald-50 to-teal-50 rounded-xl">
                        <p class="text-xs text-gray-500">Durasi Kerja</p>
                        <p class="text-xl font-bold text-emerald-600">{{ $todayStatus['check_in']->duration ?? '--' }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Week Summary --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-calendar-week text-violet-500"></i>
                    Minggu Ini
                </h3>
                <div class="flex justify-between gap-1">
                    @foreach($weekSummary as $day)
                    <div class="flex-1 text-center">
                        <p class="text-[10px] text-gray-400 uppercase">{{ $day['date']->format('D') }}</p>
                        <div class="mt-1 w-full aspect-square rounded-lg flex items-center justify-center text-xs font-bold
                            {{ $day['is_future'] ? 'bg-gray-100 text-gray-300' : 
                               ($day['attended'] ? ($day['is_late'] ? 'bg-amber-100 text-amber-600' : 'bg-emerald-100 text-emerald-600') : 
                               ($day['is_today'] ? 'bg-blue-100 text-blue-600 ring-2 ring-blue-500' : 'bg-red-100 text-red-600')) }}">
                            {{ $day['date']->format('d') }}
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="mt-3 flex items-center justify-center gap-4 text-xs text-gray-500">
                    <span class="flex items-center gap-1"><span class="w-2 h-2 bg-emerald-500 rounded"></span>Hadir</span>
                    <span class="flex items-center gap-1"><span class="w-2 h-2 bg-amber-500 rounded"></span>Telat</span>
                    <span class="flex items-center gap-1"><span class="w-2 h-2 bg-red-500 rounded"></span>Absen</span>
                </div>
            </div>
        </div>

        {{-- Scanner / Location Select --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            {{-- Mode Switcher --}}
            <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-900">
                    <i class="fas {{ $scanMode === 'qr' ? 'fa-qrcode' : 'fa-map-marker-alt' }} text-emerald-500 mr-2"></i>
                    {{ $scanMode === 'qr' ? 'Scan QR Code' : 'Pilih Lokasi' }}
                </h3>
                <div class="flex items-center bg-gray-100 rounded-lg p-0.5">
                    <button wire:click="setScanMode('qr')" 
                            class="px-3 py-1.5 rounded-md text-sm font-medium transition {{ $scanMode === 'qr' ? 'bg-white shadow text-emerald-600' : 'text-gray-500' }}">
                        <i class="fas fa-qrcode mr-1"></i> QR
                    </button>
                    <button wire:click="setScanMode('select')" 
                            @click="cleanupScanner()"
                            class="px-3 py-1.5 rounded-md text-sm font-medium transition {{ $scanMode === 'select' ? 'bg-white shadow text-emerald-600' : 'text-gray-500' }}">
                        <i class="fas fa-map-marker-alt mr-1"></i> Lokasi
                    </button>
                </div>
            </div>

            <div class="p-5">
                @if($scanMode === 'qr')
                {{-- QR Scanner - Auto Start --}}
                <div x-data="qrScannerComponent()" x-init="autoStart()" class="space-y-4">
                    <div id="qr-reader" class="w-full aspect-video bg-gray-900 rounded-xl overflow-hidden relative">
                        <div x-show="!scanning" class="absolute inset-0 flex items-center justify-center text-white">
                            <div class="text-center">
                                <i class="fas fa-spinner fa-spin text-4xl mb-3 opacity-75"></i>
                                <p class="text-sm opacity-75">Membuka kamera...</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button @click="toggleScanner()" 
                                class="flex-1 py-3 font-semibold rounded-xl transition flex items-center justify-center gap-2"
                                :class="scanning 
                                    ? 'bg-red-100 hover:bg-red-200 text-red-700' 
                                    : 'bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-700 hover:to-indigo-700 text-white'">
                            <i class="fas" :class="scanning ? 'fa-stop' : 'fa-camera'"></i>
                            <span x-text="scanning ? 'Stop Scanner' : 'Mulai Ulang'"></span>
                        </button>
                    </div>
                    @if($scannedQrCode && $selectedLocationId)
                    <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center gap-3">
                        <i class="fas fa-check-circle text-emerald-500 text-xl"></i>
                        <div>
                            <p class="font-medium text-emerald-800">QR Terdeteksi</p>
                            <p class="text-sm text-emerald-600">{{ $locations->find($selectedLocationId)?->name }}</p>
                        </div>
                    </div>
                    @endif
                </div>
                @else
                {{-- Location Select --}}
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Lokasi</label>
                        <select wire:model.live="selectedLocationId" class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500">
                            <option value="">-- Pilih Lokasi --</option>
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    @if($selectedLocationId)
                    @php $loc = $locations->find($selectedLocationId); @endphp
                    <div class="p-4 bg-gray-50 rounded-xl">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center text-emerald-600 flex-shrink-0">
                                <i class="fas fa-map-pin"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-bold text-gray-900">{{ $loc->name }}</p>
                                <p class="text-sm text-gray-500">{{ $loc->address ?? 'Alamat tidak tersedia' }}</p>
                                <div class="mt-2 flex items-center gap-4 text-xs text-gray-500">
                                    <span><i class="fas fa-clock mr-1"></i>{{ \Carbon\Carbon::parse($loc->work_start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($loc->work_end_time)->format('H:i') }}</span>
                                    <span><i class="fas fa-bullseye mr-1"></i>Radius {{ $loc->radius_meters }}m</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                @endif

                {{-- GPS Status --}}
                <div class="mt-4 p-3 rounded-xl border" :class="gpsStatus === 'active' ? 'bg-emerald-50 border-emerald-200' : gpsStatus === 'loading' ? 'bg-amber-50 border-amber-200' : 'bg-red-50 border-red-200'">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" :class="gpsStatus === 'active' ? 'bg-emerald-500 text-white' : gpsStatus === 'loading' ? 'bg-amber-500 text-white' : 'bg-red-500 text-white'">
                            <i class="fas" :class="gpsStatus === 'active' ? 'fa-location-dot' : gpsStatus === 'loading' ? 'fa-spinner fa-spin' : 'fa-location-crosshairs'"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium" :class="gpsStatus === 'active' ? 'text-emerald-800' : gpsStatus === 'loading' ? 'text-amber-800' : 'text-red-800'" x-text="gpsText"></p>
                            <p x-show="gpsStatus === 'active'" class="text-xs text-emerald-600">
                                Akurasi: <span x-text="gpsAccuracy ? gpsAccuracy.toFixed(0) + 'm' : '-'"></span>
                            </p>
                        </div>
                        <button @click="initGeolocation()" class="px-3 py-1.5 bg-white rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 transition">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="mt-5 grid grid-cols-2 gap-3">
                    @if(!$todayStatus['check_in'])
                    <button wire:click="checkIn" 
                            wire:loading.attr="disabled"
                            :disabled="gpsStatus !== 'active'"
                            class="col-span-2 py-4 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 disabled:from-gray-300 disabled:to-gray-400 text-white font-bold rounded-xl shadow-lg shadow-emerald-500/25 transition flex items-center justify-center gap-2 text-lg">
                        <i class="fas fa-arrow-right-to-bracket" wire:loading.remove wire:target="checkIn"></i>
                        <i class="fas fa-spinner fa-spin" wire:loading wire:target="checkIn"></i>
                        CHECK IN
                    </button>
                    @elseif(!$todayStatus['check_out'])
                    <button wire:click="checkOut" 
                            wire:loading.attr="disabled"
                            :disabled="gpsStatus !== 'active'"
                            class="col-span-2 py-4 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 disabled:from-gray-300 disabled:to-gray-400 text-white font-bold rounded-xl shadow-lg shadow-blue-500/25 transition flex items-center justify-center gap-2 text-lg">
                        <i class="fas fa-arrow-right-from-bracket" wire:loading.remove wire:target="checkOut"></i>
                        <i class="fas fa-spinner fa-spin" wire:loading wire:target="checkOut"></i>
                        CHECK OUT
                    </button>
                    @else
                    <div class="col-span-2 py-4 bg-gray-100 text-gray-500 font-bold rounded-xl flex items-center justify-center gap-2">
                        <i class="fas fa-check-double"></i>
                        Kehadiran Hari Ini Lengkap
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ===== TAB: RIWAYAT ===== --}}
    @if($activeTab === 'riwayat')
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        {{-- Filters --}}
        <div class="p-4 border-b border-gray-100 flex flex-wrap gap-3">
            <input type="date" wire:model.live="filterDateStart" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
            <span class="self-center text-gray-400">s/d</span>
            <input type="date" wire:model.live="filterDateEnd" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
            
            @if($isSuperAdmin)
            <select wire:model.live="filterBranchId" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                <option value="">🌐 Semua Cabang</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                @endforeach
            </select>
            @endif
            
            @if($isAdmin)
            <select wire:model.live="filterUserId" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                <option value="">Semua Staff</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
            @endif
            
            <select wire:model.live="filterLocationId" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                <option value="">Semua Lokasi</option>
                @foreach($allLocations as $loc)
                    <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                @endforeach
            </select>
            
            <select wire:model.live="filterStatus" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                <option value="all">Semua Status</option>
                <option value="on_time">Tepat Waktu</option>
                <option value="late">Terlambat</option>
            </select>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="px-4 py-3 font-semibold text-gray-600">Tanggal</th>
                        @if($isAdmin)
                        <th class="px-4 py-3 font-semibold text-gray-600">Staff</th>
                        @endif
                        <th class="px-4 py-3 font-semibold text-gray-600">Lokasi</th>
                        <th class="px-4 py-3 font-semibold text-gray-600">Tipe</th>
                        <th class="px-4 py-3 font-semibold text-gray-600">Waktu</th>
                        <th class="px-4 py-3 font-semibold text-gray-600">Status</th>
                        <th class="px-4 py-3 font-semibold text-gray-600">Verifikasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($history as $record)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">{{ $record->date->format('d M Y') }}</td>
                        @if($isAdmin)
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 bg-gray-200 rounded-full flex items-center justify-center text-xs font-bold text-gray-600">
                                    {{ substr($record->user->name, 0, 1) }}
                                </div>
                                <span>{{ $record->user->name }}</span>
                            </div>
                        </td>
                        @endif
                        <td class="px-4 py-3">{{ $record->location?->name ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $record->type === 'check_in' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ $record->type === 'check_in' ? 'Masuk' : 'Pulang' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 font-bold">{{ \Carbon\Carbon::parse($record->scanned_at)->format('H:i') }}</td>
                        <td class="px-4 py-3">
                            @if($record->type === 'check_in')
                                @if($record->is_late)
                                <span class="text-amber-600"><i class="fas fa-clock mr-1"></i>Telat {{ $record->late_minutes }}m</span>
                                @else
                                <span class="text-emerald-600"><i class="fas fa-check mr-1"></i>Tepat Waktu</span>
                                @endif
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <i class="fas {{ $record->verification_icon }} {{ $record->is_verified ? 'text-emerald-500' : 'text-amber-500' }}"></i>
                                <span class="text-xs text-gray-500">{{ $record->distance_meters }}m</span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $isAdmin ? 7 : 6 }}" class="px-4 py-12 text-center text-gray-400">
                            <i class="fas fa-inbox text-4xl mb-3"></i>
                            <p>Tidak ada data kehadiran</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($history->hasPages())
        <div class="p-4 border-t border-gray-100">
            {{ $history->links() }}
        </div>
        @endif
    </div>
    @endif

    {{-- ===== TAB: PETA ===== --}}
    @if($activeTab === 'peta')
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-900">
                <i class="fas fa-map-marked-alt text-emerald-500 mr-2"></i>
                Peta Kehadiran Hari Ini
            </h3>
            <div class="flex items-center gap-4 text-sm">
                <span class="flex items-center gap-1"><span class="w-3 h-3 bg-emerald-500 rounded-full"></span>{{ $stats['total_present'] }} Hadir</span>
                <span class="flex items-center gap-1"><span class="w-3 h-3 bg-amber-500 rounded-full"></span>{{ $stats['total_late'] }} Telat</span>
            </div>
        </div>
        <div id="attendance-map" class="h-[500px]" wire:ignore x-init="initMap(@js($mapData))"></div>
    </div>
    @endif

    {{-- ===== TAB: LOKASI (Admin) ===== --}}
    @if($activeTab === 'lokasi' && $isAdmin)
    <div class="space-y-4">
        <div class="flex justify-end">
            <button wire:click="openLocationModal()" 
                    class="px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-semibold rounded-xl shadow-lg shadow-emerald-500/25 transition flex items-center gap-2">
                <i class="fas fa-plus"></i>
                Tambah Lokasi
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($allLocations as $location)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden" wire:key="location-card-{{ $location->id }}-{{ $location->qr_code }}">
                <div class="p-4">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center text-white">
                                <i class="fas fa-map-pin text-xl"></i>
                            </div>
                            <div>
                                <p class="font-bold text-gray-900">{{ $location->name }}</p>
                                <p class="text-xs text-gray-500">{{ $location->branch?->name ?? 'Pusat' }}</p>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $location->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $location->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                    
                    <div class="mt-3 p-3 bg-gray-50 rounded-xl text-xs text-gray-600 space-y-1">
                        <p><i class="fas fa-location-dot mr-2 text-gray-400"></i>{{ $location->address ?? '-' }}</p>
                        <p><i class="fas fa-clock mr-2 text-gray-400"></i>{{ \Carbon\Carbon::parse($location->work_start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($location->work_end_time)->format('H:i') }}</p>
                        <p><i class="fas fa-bullseye mr-2 text-gray-400"></i>Radius {{ $location->radius_meters }}m</p>
                    </div>

                    {{-- QR Code --}}
                    <div class="mt-3 p-3 bg-gradient-to-r from-violet-50 to-indigo-50 rounded-xl text-center">
                        <p class="text-xs text-violet-600 font-medium mb-2">QR Code</p>
                        <div class="bg-white p-3 rounded-lg inline-block" wire:key="qr-wrapper-{{ $location->id }}-{{ $location->qr_code }}">
                            <div 
                                id="qr-{{ $location->id }}" 
                                x-data="{ initialized: false }"
                                x-init="
                                    $nextTick(() => {
                                        const el = document.getElementById('qr-{{ $location->id }}');
                                        if (el && !initialized) {
                                            el.innerHTML = '';
                                            new QRCode(el, {
                                                text: @js($location->qr_data),
                                                width: 100,
                                                height: 100,
                                                colorDark: '#4f46e5',
                                                colorLight: '#ffffff'
                                            });
                                            initialized = true;
                                        }
                                    });
                                "
                            ></div>
                        </div>
                        <p class="text-[10px] text-gray-400 mt-1">{{ $location->qr_code }}</p>
                    </div>

                    <div class="mt-3 flex gap-2">
                        <button wire:click="openLocationModal({{ $location->id }})" class="flex-1 px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition">
                            <i class="fas fa-edit mr-1"></i> Edit
                        </button>
                        <button wire:click="regenerateQr({{ $location->id }})" class="px-3 py-2 bg-violet-100 hover:bg-violet-200 text-violet-700 rounded-lg text-sm font-medium transition" title="Generate QR Baru">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                        <button wire:click="deleteLocation({{ $location->id }})" wire:confirm="Yakin hapus lokasi ini?" class="px-3 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg text-sm font-medium transition" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full py-12 text-center text-gray-400">
                <i class="fas fa-map-marker-alt text-4xl mb-3"></i>
                <p>Belum ada lokasi. Klik "Tambah Lokasi" untuk menambahkan.</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Location Modal --}}
    @if($showLocationModal)
    <template x-teleport="body">
        <div class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden max-h-[90vh] overflow-y-auto" @click.away="$wire.closeLocationModal()">
                <div class="px-6 py-4 bg-gradient-to-r from-emerald-500 to-teal-600 sticky top-0">
                    <h3 class="text-lg font-bold text-white">
                        {{ $editingLocation ? 'Edit Lokasi' : 'Tambah Lokasi Baru' }}
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    @if($isSuperAdmin)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cabang / Kampus</label>
                        <select wire:model="locationForm.branch_id" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500">
                            <option value="">🌐 Global (Semua Cabang)</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Lokasi global dapat digunakan oleh semua cabang</p>
                    </div>
                    @endif
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lokasi</label>
                        <input type="text" wire:model="locationForm.name" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500" placeholder="Contoh: Perpustakaan Pusat">
                        @error('locationForm.name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                        <textarea wire:model="locationForm.address" rows="2" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500" placeholder="Alamat lengkap..."></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Latitude</label>
                            <input type="text" wire:model="locationForm.latitude" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500" placeholder="-7.xxxxxx">
                            @error('locationForm.latitude') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Longitude</label>
                            <input type="text" wire:model="locationForm.longitude" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500" placeholder="110.xxxxxx">
                            @error('locationForm.longitude') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="p-3 bg-blue-50 rounded-xl text-xs text-blue-600">
                        <i class="fas fa-info-circle mr-1"></i>
                        Tips: Gunakan Google Maps untuk mendapatkan koordinat lokasi.
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Radius (m)</label>
                            <input type="number" wire:model="locationForm.radius_meters" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500" min="10" max="5000">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jam Masuk</label>
                            <input type="time" wire:model="locationForm.work_start_time" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jam Pulang</label>
                            <input type="time" wire:model="locationForm.work_end_time" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Toleransi Telat (menit)</label>
                            <input type="number" wire:model="locationForm.late_tolerance_minutes" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500" min="0" max="60">
                        </div>
                        <div class="flex items-end">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:model="locationForm.is_active" class="w-5 h-5 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                <span class="text-sm font-medium text-gray-700">Aktif</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3">
                    <button wire:click="closeLocationModal" class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-xl transition">
                        Batal
                    </button>
                    <button wire:click="saveLocation" class="px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-semibold rounded-xl shadow-lg shadow-emerald-500/25 transition flex items-center gap-2">
                        <i class="fas fa-save"></i>
                        {{ $editingLocation ? 'Update' : 'Simpan' }}
                    </button>
                </div>
            </div>
        </div>
    </template>
    @endif
    @endif

    {{-- Toast Notification --}}
    <template x-teleport="body">
        <div x-data="toastNotification()" 
             x-on:notify.window="show($event.detail)"
             style="position: fixed; top: 20px; right: 20px; z-index: 2147483647;"
             class="flex flex-col gap-2">
            <template x-for="toast in toasts" :key="toast.id">
                <div x-show="toast.visible"
                     x-transition:enter="transform transition ease-out duration-300"
                     x-transition:enter-start="translate-x-full opacity-0"
                     x-transition:enter-end="translate-x-0 opacity-100"
                     x-transition:leave="transform transition ease-in duration-200"
                     x-transition:leave-start="translate-x-0 opacity-100"
                     x-transition:leave-end="translate-x-full opacity-0"
                     class="flex items-center gap-3 min-w-[320px] max-w-md px-4 py-3 rounded-xl shadow-2xl backdrop-blur-sm border text-white"
                     :class="{
                         'bg-gradient-to-r from-emerald-500 to-green-600 border-emerald-400': toast.type === 'success',
                         'bg-gradient-to-r from-red-500 to-rose-600 border-red-400': toast.type === 'error',
                         'bg-gradient-to-r from-amber-500 to-orange-600 border-amber-400': toast.type === 'warning'
                     }">
                    <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center flex-shrink-0">
                        <i class="fas" :class="{'fa-check-circle': toast.type === 'success', 'fa-times-circle': toast.type === 'error', 'fa-exclamation-triangle': toast.type === 'warning'}"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-sm" x-text="toast.type === 'success' ? 'Berhasil!' : (toast.type === 'error' ? 'Error!' : 'Perhatian!')"></p>
                        <p class="text-sm text-white/90" x-text="toast.message"></p>
                    </div>
                    <button @click="dismiss(toast.id)" class="w-8 h-8 rounded-lg bg-white/20 hover:bg-white/30 flex items-center justify-center transition">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>
            </template>
        </div>
    </template>
</div>

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
function attendanceApp() {
    return {
        gpsStatus: 'loading', // loading, active, error
        gpsText: 'Memuat lokasi GPS...',
        gpsAccuracy: null,
        gpsLat: null,
        gpsLng: null,
        map: null,
        html5QrCode: null,
        scanning: false,

        // Cleanup scanner on page close/navigate/mode switch
        cleanupScanner() {
            // Cleanup own scanner
            if (this.html5QrCode && this.scanning) {
                this.html5QrCode.stop().catch(() => {});
                this.scanning = false;
            }
            
            // Also cleanup global scanner from qrScannerComponent
            if (window.activeQrScanner && window.qrScannerActive) {
                window.activeQrScanner.stop().catch(() => {});
                window.qrScannerActive = false;
                window.activeQrScanner = null;
                const qrElement = document.getElementById('qr-reader');
                if (qrElement) qrElement.innerHTML = '';
            }
        },

        initGeolocation() {
            console.log('Initializing GPS...');
            this.gpsStatus = 'loading';
            this.gpsText = 'Memuat lokasi GPS...';
            
            if (!navigator.geolocation) {
                this.gpsStatus = 'error';
                this.gpsText = 'Geolocation tidak didukung browser ini';
                console.error('Geolocation not supported');
                return;
            }

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    console.log('GPS position received:', position.coords);
                    this.gpsLat = position.coords.latitude;
                    this.gpsLng = position.coords.longitude;
                    this.gpsAccuracy = position.coords.accuracy;
                    this.gpsStatus = 'active';
                    this.gpsText = `GPS Aktif (${this.gpsLat.toFixed(6)}, ${this.gpsLng.toFixed(6)})`;
                    
                    // Update Livewire
                    if (typeof Livewire !== 'undefined') {
                        @this.updateGps(this.gpsLat, this.gpsLng, this.gpsAccuracy);
                    }
                },
                (error) => {
                    console.error('GPS Error:', error);
                    this.gpsStatus = 'error';
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            this.gpsText = 'Akses lokasi ditolak. Izinkan di pengaturan browser.';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            this.gpsText = 'Lokasi tidak tersedia';
                            break;
                        case error.TIMEOUT:
                            this.gpsText = 'Timeout mendapatkan lokasi';
                            break;
                        default:
                            this.gpsText = 'Error mendapatkan lokasi';
                    }
                },
                { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
            );
        },

        startQrScanner() {
            const qrElement = document.getElementById('qr-reader');
            if (!qrElement) {
                console.error('QR reader element not found');
                return;
            }

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

            this.html5QrCode = new Html5Qrcode("qr-reader");
            this.html5QrCode.start(
                { facingMode: "environment" },
                { fps: 10, qrbox: { width: 250, height: 250 } },
                (decodedText) => {
                    console.log('QR Decoded:', decodedText);
                    @this.handleQrScan(decodedText);
                    this.html5QrCode.stop().then(() => {
                        this.scanning = false;
                    });
                },
                (errorMessage) => { /* ignore scan errors */ }
            ).then(() => {
                this.scanning = true;
                console.log('QR Scanner started');
            }).catch((err) => {
                console.error('QR Scanner error:', err);
                this.scanning = false;
                alert('Tidak dapat mengakses kamera. Pastikan izin kamera sudah diberikan.');
            });
        },

        initMap(locations) {
            const mapElement = document.getElementById('attendance-map');
            if (!mapElement) return;
            
            if (this.map) {
                this.map.remove();
            }

            // Default center (Indonesia)
            let center = [-7.5, 110.4];
            let zoom = 8;

            if (locations && locations.length > 0) {
                center = [locations[0].lat, locations[0].lng];
                zoom = 12;
            }

            this.map = L.map('attendance-map').setView(center, zoom);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(this.map);

            if (!locations || locations.length === 0) return;

            locations.forEach(loc => {
                // Circle for radius
                L.circle([loc.lat, loc.lng], {
                    color: '#10b981',
                    fillColor: '#10b981',
                    fillOpacity: 0.1,
                    radius: loc.radius
                }).addTo(this.map);

                // Marker
                const icon = L.divIcon({
                    html: `<div style="width:40px;height:40px;background:linear-gradient(135deg,#10b981,#14b8a6);border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-weight:bold;box-shadow:0 4px 6px rgba(0,0,0,0.3);border:2px solid white;">${loc.today_count}</div>`,
                    className: '',
                    iconSize: [40, 40],
                    iconAnchor: [20, 20]
                });

                const marker = L.marker([loc.lat, loc.lng], { icon }).addTo(this.map);
                
                // Popup content
                let staffList = loc.staff.map(s => 
                    `<div style="display:flex;justify-content:space-between;padding:4px 0;">
                        <span>${s.name}</span>
                        <span style="color:${s.is_late ? '#d97706' : '#10b981'}">${s.time}</span>
                    </div>`
                ).join('') || '<p style="color:#9ca3af">Belum ada kehadiran</p>';

                marker.bindPopup(`
                    <div style="min-width:200px">
                        <h4 style="font-weight:bold;margin:0 0 4px 0;">${loc.name}</h4>
                        <p style="font-size:12px;color:#6b7280;margin:0 0 8px 0;">${loc.branch}</p>
                        <div style="font-size:13px;">${staffList}</div>
                    </div>
                `);
            });

            // Fit bounds if multiple locations
            if (locations.length > 1) {
                const bounds = L.latLngBounds(locations.map(l => [l.lat, l.lng]));
                this.map.fitBounds(bounds, { padding: [50, 50] });
            }
        }
    }
}

// Separate QR Scanner component for auto-start
function qrScannerComponent() {
    return {
        scanning: false,
        html5QrCode: null,

        autoStart() {
            // Small delay to ensure DOM is ready
            setTimeout(() => {
                this.startScanner();
            }, 500);
        },

        toggleScanner() {
            if (this.scanning) {
                this.stopScanner();
            } else {
                this.startScanner();
            }
        },

        startScanner() {
            const qrElement = document.getElementById('qr-reader');
            if (!qrElement) {
                console.error('QR reader element not found');
                return;
            }

            // Clear previous content
            qrElement.innerHTML = '';

            if (typeof Html5Qrcode === 'undefined') {
                console.error('Html5Qrcode library not loaded');
                return;
            }

            this.html5QrCode = new Html5Qrcode("qr-reader");
            // Store globally for cleanup from other scopes
            window.activeQrScanner = this.html5QrCode;
            
            this.html5QrCode.start(
                { facingMode: "environment" },
                { fps: 10, qrbox: { width: 250, height: 250 } },
                (decodedText) => {
                    console.log('QR Decoded:', decodedText);
                    @this.handleQrScan(decodedText);
                    this.stopScanner();
                },
                (errorMessage) => { /* ignore scan errors */ }
            ).then(() => {
                this.scanning = true;
                window.qrScannerActive = true;
                console.log('QR Scanner auto-started');
            }).catch((err) => {
                console.error('QR Scanner error:', err);
                this.scanning = false;
            });
        },

        stopScanner() {
            if (this.html5QrCode && this.scanning) {
                this.html5QrCode.stop().then(() => {
                    this.scanning = false;
                    window.qrScannerActive = false;
                    window.activeQrScanner = null;
                    const qrElement = document.getElementById('qr-reader');
                    if (qrElement) qrElement.innerHTML = '';
                }).catch(err => console.error('Stop error:', err));
            }
        }
    }
}

function toastNotification() {
    return {
        toasts: [],
        show(detail) {
            console.log('Toast received:', detail);
            const id = Date.now();
            // Livewire 3 sends named parameters as object properties
            const type = detail.type || 'info';
            const message = detail.message || '';
            const toast = { id, type, message, visible: true };
            this.toasts.push(toast);
            setTimeout(() => this.dismiss(id), 4000);
        },
        dismiss(id) {
            const toast = this.toasts.find(t => t.id === id);
            if (toast) {
                toast.visible = false;
                setTimeout(() => {
                    this.toasts = this.toasts.filter(t => t.id !== id);
                }, 300);
            }
        }
    }
}
</script>
@endpush
