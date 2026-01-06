<div>
    <div class="min-h-screen bg-gray-50 lg:bg-transparent">
        {{-- Mobile Header --}}
        <div class="lg:hidden sticky top-0 z-50 bg-gradient-to-r from-primary-600 to-primary-800 safe-area-top">
            <div class="flex items-center justify-between px-4 py-3">
                <div class="flex items-center gap-3">
                    <label class="relative w-10 h-10 cursor-pointer">
                        <div class="w-full h-full bg-white/20 rounded-full flex items-center justify-center overflow-hidden">
                            @if($member->photo)
                                <img src="{{ asset('storage/' . $member->photo) }}" alt="{{ $member->name }}" class="w-full h-full object-cover">
                            @else
                                <i class="fas fa-user text-white/80"></i>
                            @endif
                        </div>
                        <div class="absolute -bottom-0.5 -right-0.5 w-4 h-4 bg-white rounded-full flex items-center justify-center shadow">
                            <i class="fas fa-pencil-alt text-primary-600 text-[8px]"></i>
                        </div>
                        <input type="file" wire:model="photo" accept="image/*" class="hidden">
                        <div wire:loading wire:target="photo" class="absolute inset-0 bg-black/70 rounded-full flex items-center justify-center">
                            <i class="fas fa-spinner fa-spin text-white text-xs"></i>
                        </div>
                    </label>
                    <div>
                        <p class="text-white font-semibold text-sm">{{ Str::limit($member->name, 20) }}</p>
                        <p class="text-primary-200 text-xs">{{ $member->member_id }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    @php $staffAccountMobile = \App\Models\User::where('email', $member->email)->where('status', 'approved')->where('is_active', true)->first(); @endphp
                    @if($staffAccountMobile)
                    <a href="{{ route('auth.switch-portal', 'staff') }}" class="w-9 h-9 bg-blue-500/30 text-white rounded-full flex items-center justify-center hover:bg-blue-500/50 transition" title="Staff Portal">
                        <i class="fas fa-exchange-alt text-sm"></i>
                    </a>
                    @endif
                    <button wire:click="$set('showDigitalCard', true)" class="w-9 h-9 bg-amber-500/80 text-white rounded-full flex items-center justify-center hover:bg-amber-500 transition" title="Kartu Digital">
                        <i class="fas fa-id-card text-sm"></i>
                    </button>
                    <a href="{{ route('opac.member.settings') }}" class="w-9 h-9 bg-white/10 text-white rounded-full flex items-center justify-center hover:bg-white/20 transition">
                        <i class="fas fa-cog text-sm"></i>
                    </a>
                    <a href="{{ route('opac.logout') }}" class="w-9 h-9 bg-white/10 text-white rounded-full flex items-center justify-center hover:bg-white/20 transition">
                        <i class="fas fa-sign-out-alt text-sm"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-3 sm:px-4 py-3 lg:py-8">
            {{-- Desktop Profile Header --}}
            <div class="hidden lg:block bg-gradient-to-r from-primary-600 to-primary-800 rounded-2xl p-6 mb-6 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="relative flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="relative group">
                            <div class="w-16 h-16 bg-white/20 rounded-xl flex items-center justify-center overflow-hidden">
                                @if($member->photo)
                                    <img src="{{ asset('storage/' . $member->photo) }}" alt="{{ $member->name }}" class="w-full h-full object-cover">
                                @else
                                    <i class="fas fa-user text-2xl text-white/80"></i>
                                @endif
                            </div>
                            <label class="absolute -bottom-1 -right-1 w-6 h-6 bg-white rounded-full flex items-center justify-center cursor-pointer shadow-lg hover:bg-gray-100 transition">
                                <i class="fas fa-pencil-alt text-primary-600 text-xs"></i>
                                <input type="file" wire:model="photo" accept="image/*" class="hidden">
                            </label>
                            <div wire:loading wire:target="photo" class="absolute inset-0 bg-black/70 rounded-xl flex items-center justify-center">
                                <i class="fas fa-spinner fa-spin text-white"></i>
                            </div>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold">{{ $member->name }}</h1>
                            <p class="text-primary-200 text-sm">{{ $member->member_id }} • {{ $member->memberType?->name ?? 'Umum' }}</p>
                            @if($googleAccount = $member->socialAccounts()->where('provider', 'google')->first())
                            <p class="text-primary-200 text-xs mt-1 flex items-center gap-1">
                                <svg class="w-3 h-3" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                                {{ $googleAccount->provider_email }}
                            </p>
                            @else
                            <p class="text-primary-300 text-xs mt-1">Berlaku s/d {{ $member->expire_date?->format('d M Y') ?? '-' }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex flex-col gap-2 items-end">
                        @php $staffAccount = \App\Models\User::where('email', $member->email)->where('status', 'approved')->where('is_active', true)->first(); @endphp
                        @if($staffAccount)
                        <a href="{{ route('auth.switch-portal', 'staff') }}" class="w-32 group bg-blue-500/20 hover:bg-blue-500/30 text-white px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center justify-center gap-2 border border-blue-400/30 shadow-sm backdrop-blur-md h-8">
                            <i class="fas fa-exchange-alt"></i> Staff Portal
                        </a>
                        @endif
                        
                        <div class="flex items-center gap-2">
                            {{-- Google Connect Notice - only for non-UNIDA email users without google_id --}}
                            @if(!$member->google_id && !str_ends_with($member->email, '@unida.gontor.ac.id'))
                            <div x-data="{ show: !localStorage.getItem('hideGoogleNotice_{{ $member->id }}_{{ today()->format('Ymd') }}') }" x-show="show" x-cloak
                                 class="flex items-center gap-2 px-2.5 py-1.5 bg-white/10 border border-white/20 rounded-xl backdrop-blur-sm">
                                <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24">
                                    <path fill="#fff" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                    <path fill="#fff" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                    <path fill="#fff" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                    <path fill="#fff" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                                </svg>
                                <a href="{{ route('auth.google') }}" class="text-white text-[10px] font-medium hover:underline whitespace-nowrap">Hubungkan UNIDA</a>
                                <button @click="show = false; localStorage.setItem('hideGoogleNotice_{{ $member->id }}_{{ today()->format('Ymd') }}', '1')" class="text-white/50 hover:text-white">
                                    <i class="fas fa-times text-[10px]"></i>
                                </button>
                            </div>
                            @endif
                            
                            <a href="{{ route('opac.member.settings') }}" class="group bg-white/10 hover:bg-white/20 text-white px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center justify-center gap-2 border border-white/20 shadow-sm backdrop-blur-md h-8">
                                <i class="fas fa-cog group-hover:rotate-90 transition-transform duration-500"></i> <span class="hidden sm:inline">Pengaturan</span>
                            </a>
                            <button wire:click="$set('showDigitalCard', true)" class="bg-amber-500/80 hover:bg-amber-500 text-white px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center justify-center gap-2 border border-amber-400/50 shadow-sm h-8">
                                <i class="fas fa-id-card"></i> <span class="hidden sm:inline">Kartu</span>
                            </button>
                            <a href="{{ route('opac.logout') }}" class="px-3 py-1.5 bg-white/10 hover:bg-red-500/80 text-white rounded-xl transition flex items-center justify-center gap-2 border border-transparent hover:border-red-400 text-xs font-medium h-8">
                                <i class="fas fa-sign-out-alt"></i> <span class="hidden sm:inline">Keluar</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Stats Cards --}}
            <div class="grid grid-cols-4 gap-1.5 lg:gap-4 mb-3 lg:mb-4">
                <div class="bg-white rounded-lg lg:rounded-xl p-2 lg:p-3 shadow-sm border border-gray-100">
                    <div class="flex flex-col lg:flex-row items-center lg:gap-2 text-center lg:text-left">
                        <div class="w-7 h-7 lg:w-9 lg:h-9 bg-blue-100 rounded-lg flex items-center justify-center mb-1 lg:mb-0">
                            <i class="fas fa-book-reader text-blue-600 text-[10px] lg:text-sm"></i>
                        </div>
                        <div>
                            <p class="text-base lg:text-xl font-bold text-gray-900">{{ $loans->count() }}</p>
                            <p class="text-[8px] lg:text-[10px] text-gray-500">Dipinjam</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg lg:rounded-xl p-2 lg:p-3 shadow-sm border border-gray-100">
                    <div class="flex flex-col lg:flex-row items-center lg:gap-2 text-center lg:text-left">
                        <div class="w-7 h-7 lg:w-9 lg:h-9 bg-orange-100 rounded-lg flex items-center justify-center mb-1 lg:mb-0">
                            <i class="fas fa-exclamation-triangle text-orange-600 text-[10px] lg:text-sm"></i>
                        </div>
                        <div>
                            <p class="text-base lg:text-xl font-bold text-gray-900">{{ $loans->where('due_date', '<', now())->count() }}</p>
                            <p class="text-[8px] lg:text-[10px] text-gray-500">Terlambat</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg lg:rounded-xl p-2 lg:p-3 shadow-sm border border-gray-100">
                    <div class="flex flex-col lg:flex-row items-center lg:gap-2 text-center lg:text-left">
                        <div class="w-7 h-7 lg:w-9 lg:h-9 bg-violet-100 rounded-lg flex items-center justify-center mb-1 lg:mb-0">
                            <i class="fas fa-file-alt text-violet-600 text-[10px] lg:text-sm"></i>
                        </div>
                        <div>
                            <p class="text-base lg:text-xl font-bold text-gray-900">{{ $submissions->count() }}</p>
                            <p class="text-[8px] lg:text-[10px] text-gray-500">Tugas Akhir</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg lg:rounded-xl p-2 lg:p-3 shadow-sm border border-gray-100">
                    <div class="flex flex-col lg:flex-row items-center lg:gap-2 text-center lg:text-left">
                        <div class="w-7 h-7 lg:w-9 lg:h-9 bg-red-100 rounded-lg flex items-center justify-center mb-1 lg:mb-0">
                            <i class="fas fa-coins text-red-600 text-[10px] lg:text-sm"></i>
                        </div>
                        <div>
                            <p class="text-base lg:text-xl font-bold text-gray-900">{{ $fines->count() }}</p>
                            <p class="text-[8px] lg:text-[10px] text-gray-500">Denda</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Layanan Sirkulasi --}}
            @if($loans->count() > 0 || ($reservations ?? collect())->count() > 0)
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-3 mb-3 lg:mb-4 border border-blue-100">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <div class="w-9 h-9 bg-blue-500 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-sync-alt text-white text-sm"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-blue-900">Layanan Sirkulasi</p>
                            <p class="text-[10px] text-blue-600">
                                @if($loans->filter(fn($l) => $l->canRenew())->count() > 0)
                                    {{ $loans->filter(fn($l) => $l->canRenew())->count() }} buku bisa diperpanjang
                                @else
                                    Perpanjang pinjaman & reservasi buku
                                @endif
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('opac.member.loans') }}" class="px-3 py-1.5 bg-blue-500 text-white text-xs font-medium rounded-lg hover:bg-blue-600 transition flex-shrink-0">
                        Kelola <i class="fas fa-arrow-right ml-1 text-[10px]"></i>
                    </a>
                </div>
            </div>
            @endif

            {{-- Main Content Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
                
                {{-- Left Column: Peminjaman Aktif + Submissions --}}
                <div class="lg:col-span-2 space-y-4">

                    {{-- Turnitin Active Banner - Dismissible --}}
                    <div x-data="{ show: !localStorage.getItem('hideTurnitinBanner_{{ $member->id }}') }" x-show="show" x-cloak class="p-2.5 bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200 rounded-xl">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 bg-emerald-500 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-check text-white text-xs"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-emerald-800 text-[10px] font-semibold">Turnitin/iThenticate Aktif • Kuota 5x</p>
                            </div>
                            <button @click="localStorage.setItem('hideTurnitinBanner_{{ $member->id }}', '1'); show = false" class="w-6 h-6 text-emerald-400 hover:text-emerald-600 hover:bg-emerald-100 rounded-full flex items-center justify-center transition">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Premium CTA Card --}}
                    <div class="bg-gradient-to-br from-slate-800/95 to-slate-900/95 backdrop-blur rounded-2xl p-4 border border-white/10 shadow-xl">
                        <div class="flex items-center gap-2 mb-3">
                            <i class="fas fa-bolt text-amber-400 text-xs"></i>
                            <span class="text-white/80 text-xs font-medium">Layanan Utama</span>
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            {{-- Cek Plagiasi CTA --}}
                            @if($member->canAccessPlagiarism())
                            <a href="{{ route('opac.member.plagiarism.create') }}" class="block bg-gradient-to-br from-teal-500 to-emerald-600 rounded-xl p-2.5 text-white shadow-md hover:shadow-lg hover:scale-[1.02] transition-all">
                                <div class="flex items-center gap-2">
                                    <div class="w-9 h-9 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-search-plus text-sm"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="font-bold text-xs">Cek Plagiasi</h3>
                                        <p class="text-teal-200 text-[9px]">Kuota 5x</p>
                                    </div>
                                </div>
                            </a>
                            @else
                            <div class="block bg-gradient-to-br from-gray-500 to-gray-600 rounded-xl p-2.5 text-white shadow-md relative">
                                <div class="absolute top-1 right-1 px-1 py-0.5 bg-amber-500 text-[7px] font-bold rounded">TERBATAS</div>
                                <div class="flex items-center gap-2">
                                    <div class="w-9 h-9 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-lock text-sm"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="font-bold text-xs">Cek Plagiasi</h3>
                                        <p class="text-gray-300 text-[9px]">Civitas</p>
                                    </div>
                                </div>
                            </div>
                            @endif

                            {{-- Upload TA CTA --}}
                            <a href="{{ route('opac.member.submissions') }}" class="block bg-gradient-to-br from-violet-500 to-purple-600 rounded-xl p-2.5 text-white shadow-md hover:shadow-lg hover:scale-[1.02] transition-all">
                                <div class="flex items-center gap-2">
                                    <div class="w-9 h-9 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-upload text-sm"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="font-bold text-xs">Upload TA</h3>
                                        <p class="text-violet-200 text-[9px]">Skripsi/Tesis</p>
                                    </div>
                                </div>
                            </a>

                            {{-- Kelas Saya CTA --}}
                            @php
                                $myCoursesCount = \App\Models\CourseEnrollment::where('member_id', $member->id)->whereIn('status', ['approved', 'completed'])->count();
                            @endphp
                            <a href="{{ route('opac.member.courses') }}" class="block bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl p-2.5 text-white shadow-md hover:shadow-lg hover:scale-[1.02] transition-all relative">
                                @if($myCoursesCount > 0)
                                <div class="absolute top-1 right-1 px-1.5 py-0.5 bg-white text-blue-600 text-[8px] font-bold rounded">{{ $myCoursesCount }}</div>
                                @endif
                                <div class="flex items-center gap-2">
                                    <div class="w-9 h-9 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-graduation-cap text-sm"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="font-bold text-xs">Kelas Saya</h3>
                                        <p class="text-blue-200 text-[9px]">E-Learning</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>

                    {{-- Premium Digital Resources Card --}}
                    <div class="bg-gradient-to-br from-slate-800/95 to-slate-900/95 backdrop-blur rounded-2xl p-4 border border-white/10 shadow-xl">
                        <div class="flex items-center gap-2 mb-3">
                            <i class="fas fa-crown text-amber-400 text-xs"></i>
                            <span class="text-white/80 text-xs font-medium">Akses Premium Anggota</span>
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            {{-- Maktabah Shamela Card --}}
                            <a href="{{ route('opac.shamela.index') }}" class="block bg-gradient-to-br from-emerald-600 to-teal-700 rounded-xl p-2.5 text-white shadow-md hover:shadow-lg hover:scale-[1.02] transition-all">
                                <div class="flex items-center gap-2">
                                    <div class="w-9 h-9 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-book-quran text-sm"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="font-bold text-xs">Shamela</h3>
                                        <p class="text-emerald-200 text-[9px]">8.4K Kitab</p>
                                    </div>
                                </div>
                            </a>

                            {{-- Repository EPrints Card --}}
                            <a href="{{ route('opac.repository') }}" class="block bg-gradient-to-br from-orange-500 to-amber-600 rounded-xl p-2.5 text-white shadow-md hover:shadow-lg hover:scale-[1.02] transition-all">
                                <div class="flex items-center gap-2">
                                    <div class="w-9 h-9 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-archive text-sm"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="font-bold text-xs">Repository</h3>
                                        <p class="text-orange-200 text-[9px]">EPrints</p>
                                    </div>
                                </div>
                            </a>

                            {{-- FPPTI Database Access Card --}}
                            <a href="{{ route('opac.database-access') }}" class="block bg-gradient-to-br from-indigo-600 to-purple-700 rounded-xl p-2.5 text-white shadow-md hover:shadow-lg hover:scale-[1.02] transition-all">
                                <div class="flex items-center gap-2">
                                    <div class="w-9 h-9 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-database text-sm"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="font-bold text-xs">Database</h3>
                                        <p class="text-indigo-200 text-[9px]">FPPTI</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>

                    {{-- Peminjaman Aktif --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                            <h2 class="font-bold text-gray-900 text-sm flex items-center gap-2">
                                <div class="w-7 h-7 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-book-reader text-blue-600 text-xs"></i>
                                </div>
                                Peminjaman Aktif
                            </h2>
                            <span class="text-xs text-gray-500">{{ $loans->count() }} buku</span>
                        </div>
                        
                        @if($loans->count() > 0)
                            <div class="divide-y divide-gray-50">
                                @foreach($loans as $loan)
                                <div class="p-4 flex items-center gap-3">
                                    <div class="w-12 h-16 bg-gray-100 rounded-lg flex-shrink-0 overflow-hidden">
                                        @if($loan->item->book->cover_url)
                                            <img src="{{ $loan->item->book->cover_url }}" alt="{{ $loan->item->book->title }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <i class="fas fa-book text-gray-400 text-xs"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 line-clamp-1">{{ $loan->item->book->title }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5">{{ $loan->item->book->author_names }}</p>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-xs text-gray-400">Kembali:</span>
                                            <span class="text-xs font-medium {{ $loan->due_date < now() ? 'text-red-600' : 'text-gray-600' }}">
                                                {{ $loan->due_date->format('d M Y') }}
                                            </span>
                                            @if($loan->due_date < now())
                                                <span class="px-1.5 py-0.5 bg-red-100 text-red-600 text-[10px] font-medium rounded">
                                                    Terlambat
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-8 text-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-book text-gray-400 text-xl"></i>
                                </div>
                                <p class="text-sm text-gray-500">Tidak ada peminjaman aktif</p>
                            </div>
                        @endif
                    </div>

                    {{-- Riwayat Cek Plagiasi --}}
                    @if(\App\Services\Plagiarism\PlagiarismService::isEnabled())
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                            <h2 class="font-bold text-gray-900 text-sm flex items-center gap-2">
                                <div class="w-7 h-7 bg-primary-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-shield-alt text-primary-600 text-xs"></i>
                                </div>
                                Riwayat Cek Plagiasi
                            </h2>
                            <a href="{{ route('opac.member.plagiarism.index') }}" class="text-xs text-primary-600 hover:underline">Lihat Semua</a>
                        </div>
                        
                        @if($latestChecks->count() > 0)
                        <div class="divide-y divide-gray-50">
                            @foreach($latestChecks as $check)
                            <a href="{{ route('opac.member.plagiarism.show', $check) }}" class="group block p-4 hover:bg-gray-50 transition-all duration-200">
                                <div class="flex items-center gap-4">
                                    <div class="flex-shrink-0">
                                        <div class="w-14 h-14 rounded-2xl flex flex-col items-center justify-center transition-transform group-hover:scale-105
                                            @if($check->isCompleted())
                                                @if($check->similarity_level === 'low') bg-emerald-50 text-emerald-600
                                                @elseif($check->similarity_level === 'moderate') bg-amber-50 text-amber-600
                                                @else bg-red-50 text-red-600
                                                @endif
                                            @elseif($check->isFailed()) bg-red-50 text-red-600
                                            @else bg-primary-50 text-primary-600
                                            @endif
                                        ">
                                            @if($check->isCompleted())
                                                <div class="flex items-baseline gap-0.5 translate-y-0.5">
                                                    <span class="text-lg font-bold tracking-tight">{{ number_format($check->similarity_score, 0) }}</span>
                                                    <span class="text-[10px] font-bold opacity-80">%</span>
                                                </div>
                                                <span class="text-[9px] font-bold uppercase tracking-wider opacity-60 -translate-y-0.5">SIM</span>
                                            @elseif($check->isFailed())
                                                <i class="fas fa-times text-xl mb-0.5"></i>
                                            @else
                                                <i class="fas fa-spinner fa-spin text-lg mb-0.5"></i>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0 py-0.5">
                                        <div class="flex items-center gap-2 mb-1.5">
                                            <h3 class="font-bold text-gray-900 text-sm truncate group-hover:text-primary-600 transition-colors">
                                                {{ $check->document_title }}
                                            </h3>
                                            @if($check->isCompleted())
                                                @if($check->isPassed())
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-700 text-[10px] font-bold tracking-wide uppercase flex-shrink-0">Lolos</span>
                                                @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-red-100 text-red-700 text-[10px] font-bold tracking-wide uppercase flex-shrink-0">Review</span>
                                                @endif
                                            @elseif($check->isFailed())
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-red-100 text-red-600 text-[10px] font-bold tracking-wide uppercase flex-shrink-0">Gagal</span>
                                            @elseif($check->isProcessing())
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-primary-100 text-primary-600 text-[10px] font-bold tracking-wide uppercase flex-shrink-0">Proses</span>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-3 text-xs text-gray-500 font-medium">
                                            <div class="flex items-center gap-1.5">
                                                <i class="far fa-calendar-alt text-gray-400"></i>
                                                {{ $check->created_at->format('d M Y') }}
                                            </div>
                                            <div class="w-1 h-1 rounded-full bg-gray-300"></div>
                                            <div class="flex items-center gap-1.5">
                                                <i class="far fa-clock text-gray-400"></i>
                                                {{ $check->created_at->format('H:i') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0 pl-2">
                                        @if($check->isCompleted())
                                            <div class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-50 text-gray-400 border border-gray-100 group-hover:bg-primary-50 group-hover:text-primary-600 group-hover:border-primary-100 transition-all shadow-sm">
                                                <i class="fas fa-download text-sm"></i>
                                            </div>
                                        @elseif($check->isFailed())
                                            <div class="w-9 h-9 flex items-center justify-center rounded-xl bg-red-50 text-red-500 border border-red-100">
                                                <i class="fas fa-exclamation text-sm"></i>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </a>
                            @endforeach
                        </div>
                        @else
                            <div class="p-8 text-center">
                                <div class="w-16 h-16 bg-teal-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-search-plus text-teal-300 text-xl"></i>
                                </div>
                                <p class="text-sm text-gray-500">Belum ada riwayat cek plagiasi</p>
                            </div>
                        @endif
                    </div>
                    @endif

                    {{-- Submission Tugas Akhir --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-4 border-b border-gray-100">
                            <div class="flex items-center justify-between mb-3">
                                <h2 class="font-bold text-gray-900 text-sm flex items-center gap-2">
                                    <div class="w-7 h-7 bg-violet-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-file-alt text-violet-600 text-xs"></i>
                                    </div>
                                    Pengajuan Tugas Akhir
                                </h2>
                                <a href="{{ route('opac.member.submissions') }}" class="text-xs text-primary-600 hover:text-primary-700 font-medium">
                                    Lihat Semua <i class="fas fa-chevron-right ml-1 text-[10px]"></i>
                                </a>
                            </div>
                            {{-- Status Legend --}}
                            <div class="flex flex-wrap gap-2 text-[10px]">
                                <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span> Draft</span>
                                <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 bg-blue-500 rounded-full"></span> Diajukan</span>
                                <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 bg-orange-500 rounded-full"></span> Revisi</span>
                                <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span> Disetujui</span>
                                <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 bg-primary-500 rounded-full"></span> Publikasi</span>
                                <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span> Ditolak</span>
                            </div>
                        </div>
                        
                        @if($submissions->count() > 0)
                            <div class="divide-y divide-gray-50">
                                @foreach($submissions->take(5) as $sub)
                                <div class="p-4 hover:bg-gray-50/50 transition">
                                    <div class="flex items-start gap-3">
                                        {{-- Cover --}}
                                        <div class="w-12 h-16 bg-gradient-to-br from-primary-100 to-primary-200 rounded-lg flex items-center justify-center flex-shrink-0 overflow-hidden">
                                            @if($sub->cover_file)
                                                <img src="{{ route('thesis.file', [$sub, 'cover']) }}" alt="" class="w-full h-full object-cover">
                                            @else
                                                <i class="fas fa-book text-primary-400 text-sm"></i>
                                            @endif
                                        </div>
                                        
                                        {{-- Content --}}
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-start justify-between gap-2">
                                                <p class="text-sm font-medium text-gray-900 line-clamp-2">{{ $sub->title }}</p>
                                                {{-- Status Badge --}}
                                                <span @class([
                                                    'px-2 py-1 text-[10px] font-semibold rounded-lg flex-shrink-0 flex items-center gap-1',
                                                    'bg-gray-100 text-gray-600' => $sub->status === 'draft',
                                                    'bg-blue-100 text-blue-700' => in_array($sub->status, ['submitted', 'under_review']),
                                                    'bg-orange-100 text-orange-700' => $sub->status === 'revision_required',
                                                    'bg-emerald-100 text-emerald-700' => $sub->status === 'approved',
                                                    'bg-primary-100 text-primary-700' => $sub->status === 'published',
                                                    'bg-red-100 text-red-700' => $sub->status === 'rejected',
                                                ])>
                                                    @if($sub->status === 'under_review')
                                                        <span class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-pulse"></span>
                                                    @endif
                                                    {{ $sub->status_label ?? ucfirst(str_replace('_', ' ', $sub->status)) }}
                                                </span>
                                            </div>
                                            
                                            {{-- Meta Info --}}
                                            <p class="text-[10px] text-gray-500 mt-1 flex items-center gap-2 flex-wrap">
                                                @if($sub->thesis_type)
                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 bg-gray-100 rounded">
                                                    <i class="fas {{ $sub->getThesisTypeEnum()?->icon() ?? 'fa-file' }} text-[8px]"></i>
                                                    {{ $sub->getTypeDegree() }}
                                                </span>
                                                @endif
                                                <span>{{ $sub->department?->name ?? '-' }}</span>
                                                <span>•</span>
                                                <span>{{ $sub->created_at->diffForHumans() }}</span>
                                            </p>

                                            {{-- File Status --}}
                                            @if(method_exists($sub, 'getFilesInfo'))
                                            <div class="flex items-center gap-2 mt-2 text-[10px]">
                                                @php $files = $sub->getFilesInfo(); @endphp
                                                @foreach($files as $key => $file)
                                                    <span @class([
                                                        'flex items-center gap-0.5',
                                                        'text-emerald-600' => $file['exists'],
                                                        'text-gray-300' => !$file['exists'],
                                                    ]) title="{{ $file['label'] ?? $key }}">
                                                        <i class="fas {{ $file['icon'] }}"></i>
                                                        @if($file['exists'])
                                                            <i class="fas fa-check text-[8px]"></i>
                                                        @endif
                                                    </span>
                                                @endforeach
                                            </div>
                                            @endif

                                            {{-- Actions --}}
                                            <div class="flex items-center gap-2 mt-2">
                                                @if($sub->canEdit())
                                                    <a href="{{ route('opac.member.edit-submission', $sub->id) }}" class="px-2 py-1 bg-primary-100 text-primary-700 text-[10px] font-medium rounded hover:bg-primary-200 transition inline-flex items-center gap-1">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                @endif
                                                @if($sub->isPublished() && $sub->ethesis_id)
                                                    <a href="{{ route('opac.ethesis.show', $sub->ethesis_id) }}" class="px-2 py-1 bg-emerald-100 text-emerald-700 text-[10px] font-medium rounded hover:bg-emerald-200 transition inline-flex items-center gap-1">
                                                        <i class="fas fa-external-link-alt"></i> E-Thesis
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Status Messages --}}
                                    @if($sub->status === 'revision_required' && $sub->review_notes)
                                        <div class="mt-3 p-2.5 bg-orange-50 border border-orange-200 rounded-lg">
                                            <div class="flex items-start gap-2">
                                                <i class="fas fa-exclamation-triangle text-orange-500 text-xs mt-0.5"></i>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-[10px] font-semibold text-orange-800">Catatan Revisi</p>
                                                    <p class="text-[10px] text-orange-700 mt-0.5 line-clamp-2">{{ $sub->review_notes }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if($sub->status === 'rejected' && $sub->rejection_reason)
                                        <div class="mt-3 p-2.5 bg-red-50 border border-red-200 rounded-lg">
                                            <div class="flex items-start gap-2">
                                                <i class="fas fa-times-circle text-red-500 text-xs mt-0.5"></i>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-[10px] font-semibold text-red-800">Alasan Penolakan</p>
                                                    <p class="text-[10px] text-red-700 mt-0.5 line-clamp-2">{{ $sub->rejection_reason }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if($sub->status === 'approved' && !$sub->isPublished())
                                        <div class="mt-3 p-2.5 bg-emerald-50 border border-emerald-200 rounded-lg">
                                            <div class="flex items-start gap-2">
                                                <i class="fas fa-check-circle text-emerald-500 text-xs mt-0.5"></i>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-[10px] font-semibold text-emerald-800">Disetujui!</p>
                                                    <p class="text-[10px] text-emerald-700 mt-0.5">Akan segera dipublikasikan ke E-Thesis.</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if(in_array($sub->status, ['submitted', 'under_review']))
                                        <div class="mt-3 p-2.5 bg-blue-50 border border-blue-200 rounded-lg">
                                            <div class="flex items-start gap-2">
                                                <i class="fas fa-hourglass-half text-blue-500 text-xs mt-0.5 animate-pulse"></i>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-[10px] font-semibold text-blue-800">
                                                        {{ $sub->status === 'under_review' ? 'Sedang Direview' : 'Menunggu Review' }}
                                                    </p>
                                                    <p class="text-[10px] text-blue-700 mt-0.5">Sedang diverifikasi oleh pustakawan.</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                            
                            @if($submissions->count() > 5)
                            <div class="p-3 bg-gray-50 border-t border-gray-100 text-center">
                                <a href="{{ route('opac.member.submissions') }}" class="text-xs text-primary-600 hover:text-primary-700 font-medium">
                                    Lihat {{ $submissions->count() - 5 }} submission lainnya <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                            @endif
                        @else
                            <div class="p-8 text-center">
                                <div class="w-16 h-16 bg-violet-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-file-alt text-violet-300 text-xl"></i>
                                </div>
                                <p class="text-sm text-gray-500 mb-3">Belum ada pengajuan tugas akhir</p>
                                <a href="{{ route('opac.member.submit-thesis') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-violet-600 text-white text-xs font-medium rounded-lg hover:bg-violet-700 transition">
                                    <i class="fas fa-plus"></i>
                                    Buat Pengajuan
                                </a>
                            </div>
                        @endif
                    </div>
                </div>


                {{-- Right Column: History Pinjaman (Compact) --}}
                <div class="space-y-4">
                    {{-- Denda (jika ada) --}}
                    @if($fines->count() > 0)
                    <div class="bg-gradient-to-br from-red-50 to-orange-50 rounded-2xl shadow-sm border border-red-200/50 overflow-hidden">
                        <div class="p-3 border-b border-red-200/50">
                            <h2 class="font-bold text-red-800 text-xs flex items-center gap-2">
                                <div class="w-6 h-6 bg-red-500 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-coins text-white text-[10px]"></i>
                                </div>
                                Denda Belum Dibayar
                            </h2>
                        </div>
                        <div class="p-3 space-y-2">
                            @foreach($fines->take(3) as $fine)
                            <div class="p-2 bg-white rounded-lg border border-red-100">
                                <p class="text-xs font-medium text-gray-900 line-clamp-1">{{ $fine->loan?->item?->book?->title ?? 'Denda' }}</p>
                                <p class="text-sm font-bold text-red-600 mt-0.5">Rp {{ number_format($fine->amount, 0, ',', '.') }}</p>
                            </div>
                            @endforeach
                            @if($fines->count() > 3)
                            <p class="text-[10px] text-red-600 text-center">+{{ $fines->count() - 3 }} denda lainnya</p>
                            @endif
                        </div>
                    </div>
                    @endif

                    {{-- Surat Bebas Pustaka & Sertifikat Plagiasi - Compact Cards --}}
                    @if((isset($clearanceLetters) && $clearanceLetters->where('status', 'approved')->count() > 0) || (isset($plagiarismCertificates) && $plagiarismCertificates->count() > 0))
                    <div class="space-y-2">
                        @if(isset($clearanceLetters) && $clearanceLetters->where('status', 'approved')->count() > 0)
                        @php $latestClearance = $clearanceLetters->where('status', 'approved')->first(); @endphp
                        <div class="p-2.5 bg-gradient-to-r from-emerald-50 to-green-50 border border-emerald-200 rounded-xl">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-certificate text-white text-xs"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-emerald-800 font-semibold text-[10px]">Surat Bebas Pustaka</p>
                                    <p class="text-emerald-600 text-[9px] truncate">{{ $latestClearance->letter_number }}</p>
                                </div>
                                <a href="{{ route('opac.member.clearance-letter.download', $latestClearance) }}" class="w-7 h-7 bg-emerald-600 text-white rounded-lg flex items-center justify-center hover:bg-emerald-700 transition">
                                    <i class="fas fa-download text-[10px]"></i>
                                </a>
                            </div>
                        </div>
                        @endif
                        
                        @if(isset($plagiarismCertificates) && $plagiarismCertificates->count() > 0)
                        <a href="{{ route('opac.member.plagiarism.certificate', $plagiarismCertificates->first()) }}" class="block p-2.5 bg-gradient-to-r from-violet-50 to-purple-50 border border-violet-200 rounded-xl hover:border-violet-300 transition">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-violet-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-award text-white text-xs"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-violet-800 font-semibold text-[10px]">Sertifikat Bebas Plagiasi</p>
                                    <p class="text-violet-600 text-[9px] truncate">{{ $plagiarismCertificates->first()->certificate_number }}</p>
                                </div>
                                @if($plagiarismCertificates->count() > 1)
                                <span class="px-1.5 py-0.5 bg-violet-200 text-violet-700 text-[8px] font-bold rounded">+{{ $plagiarismCertificates->count() - 1 }}</span>
                                @endif
                                <i class="fas fa-chevron-right text-violet-400 text-[10px]"></i>
                            </div>
                        </a>
                        @endif
                    </div>
                    @endif

                    {{-- External Plagiarism Upload - Compact --}}
                    <a href="{{ route('opac.member.plagiarism.external') }}" class="block p-2.5 bg-gradient-to-r from-teal-50 to-emerald-50 border border-teal-200 rounded-xl hover:border-teal-300 transition">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-teal-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-file-upload text-white text-xs"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-teal-800 font-semibold text-[10px]">Upload Hasil Plagiasi Eksternal</p>
                                <p class="text-teal-600 text-[9px]">Dapatkan sertifikat bebas plagiasi</p>
                            </div>
                            <i class="fas fa-chevron-right text-teal-400 text-[10px]"></i>
                        </div>
                    </a>

                    {{-- Quick Links --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-3 border-b border-gray-100">
                            <h2 class="font-bold text-gray-900 text-xs flex items-center gap-2">
                                <div class="w-6 h-6 bg-primary-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-link text-primary-600 text-[10px]"></i>
                                </div>
                                Menu Cepat
                            </h2>
                        </div>
                        <div class="p-2 space-y-1">
                            <a href="{{ route('opac.member.loans') }}" class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-50 transition">
                                <div class="w-7 h-7 bg-blue-50 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-sync-alt text-blue-500 text-[10px]"></i>
                                </div>
                                <span class="text-xs text-gray-700">Pinjaman Saya</span>
                                @if($loans->count() > 0)
                                <span class="ml-auto px-1.5 py-0.5 bg-blue-100 text-blue-700 text-[8px] font-bold rounded">{{ $loans->count() }}</span>
                                @endif
                            </a>
                            <a href="{{ route('opac.home') }}" class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-50 transition">
                                <div class="w-7 h-7 bg-emerald-50 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-search text-emerald-500 text-[10px]"></i>
                                </div>
                                <span class="text-xs text-gray-700">Cari Buku</span>
                            </a>
                            <a href="{{ route('opac.member.submit-thesis') }}" class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-50 transition">
                                <div class="w-7 h-7 bg-violet-50 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-upload text-violet-500 text-[10px]"></i>
                                </div>
                                <span class="text-xs text-gray-700">Unggah Tugas Akhir</span>
                            </a>
                            @if(\App\Services\Plagiarism\PlagiarismService::isEnabled())
                            <a href="{{ route('opac.member.plagiarism.create') }}" class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-50 transition">
                                <div class="w-7 h-7 bg-teal-50 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-shield-alt text-teal-500 text-[10px]"></i>
                                </div>
                                <span class="text-xs text-gray-700">Cek Plagiasi</span>
                            </a>
                            <a href="{{ route('opac.member.plagiarism.index') }}" class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-50 transition">
                                <div class="w-7 h-7 bg-emerald-50 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-history text-emerald-500 text-[10px]"></i>
                                </div>
                                <span class="text-xs text-gray-700">Riwayat Plagiasi</span>
                            </a>
                            @endif
                            <a href="{{ route('opac.page', 'e-learning') }}" class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-50 transition">
                                <div class="w-7 h-7 bg-violet-50 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-graduation-cap text-violet-500 text-[10px]"></i>
                                </div>
                                <span class="text-xs text-gray-700">E-Learning</span>
                                @php $myEnrollments = \App\Models\CourseEnrollment::where('member_id', $member->id)->whereIn('status', ['approved', 'completed'])->count(); @endphp
                                @if($myEnrollments > 0)
                                <span class="ml-auto px-1.5 py-0.5 bg-violet-100 text-violet-700 text-[8px] font-bold rounded">{{ $myEnrollments }}</span>
                                @endif
                            </a>
                            <div class="border-t border-gray-100 my-1"></div>
                            <a href="{{ route('opac.database-access') }}" class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-50 transition">
                                <div class="w-7 h-7 bg-indigo-50 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-database text-indigo-500 text-[10px]"></i>
                                </div>
                                <span class="text-xs text-gray-700">Database Jurnal</span>
                                <span class="ml-auto px-1.5 py-0.5 bg-amber-100 text-amber-700 text-[8px] font-bold rounded">PRO</span>
                            </a>
                            <a href="{{ route('opac.page', 'journal-subscription') }}" class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-50 transition">
                                <div class="w-7 h-7 bg-emerald-50 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-book-open text-emerald-500 text-[10px]"></i>
                                </div>
                                <span class="text-xs text-gray-700">E-Resources Gratis</span>
                            </a>
                            <a href="{{ route('opac.page', 'panduan-opac') }}" class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-50 transition">
                                <div class="w-7 h-7 bg-amber-50 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-question-circle text-amber-500 text-[10px]"></i>
                                </div>
                                <span class="text-xs text-gray-700">Panduan OPAC</span>
                            </a>
                        </div>
                    </div>

                    {{-- Kelas E-Learning Saya --}}
                    @php
                        $myEnrollmentsData = \App\Models\CourseEnrollment::with(['course.category', 'course.instructor'])
                            ->where('member_id', $member->id)
                            ->whereIn('status', ['approved', 'completed', 'pending'])
                            ->latest()
                            ->take(3)
                            ->get();
                    @endphp
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-3 border-b border-gray-100 flex items-center justify-between">
                            <h2 class="font-bold text-gray-900 text-xs flex items-center gap-2">
                                <div class="w-6 h-6 bg-violet-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-graduation-cap text-violet-500 text-[10px]"></i>
                                </div>
                                Kelas E-Learning
                            </h2>
                            <a href="{{ route('opac.page', 'e-learning') }}" class="text-[10px] text-violet-600 hover:underline">Lihat Semua</a>
                        </div>
                        @if($myEnrollmentsData->count() > 0)
                        <div class="divide-y divide-gray-50">
                            @foreach($myEnrollmentsData as $enrollment)
                            <a href="{{ route('opac.classroom', $enrollment->course->slug) }}" class="block p-3 hover:bg-gray-50 transition">
                                <div class="flex items-start gap-2">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center flex-shrink-0 overflow-hidden">
                                        @if($enrollment->course->thumbnail)
                                        <img src="{{ Storage::url($enrollment->course->thumbnail) }}" class="w-full h-full object-cover">
                                        @else
                                        <i class="fas fa-book-open text-white text-xs"></i>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-medium text-gray-900 line-clamp-1">{{ $enrollment->course->title }}</p>
                                        <p class="text-[10px] text-gray-500">{{ $enrollment->course->instructor->name }}</p>
                                        @if($enrollment->status === 'pending')
                                        <span class="inline-block mt-1 px-1.5 py-0.5 bg-amber-100 text-amber-600 text-[8px] font-bold rounded">MENUNGGU</span>
                                        @else
                                        <div class="flex items-center gap-2 mt-1">
                                            <div class="flex-1 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                                <div class="h-full bg-blue-500 rounded-full" style="width: {{ $enrollment->progress_percent }}%"></div>
                                            </div>
                                            <span class="text-[9px] text-gray-500">{{ $enrollment->progress_percent }}%</span>
                                        </div>
                                        @endif
                                    </div>
                                    @if($enrollment->status === 'completed')
                                    <span class="px-1.5 py-0.5 bg-emerald-100 text-emerald-600 text-[8px] font-bold rounded">LULUS</span>
                                    @endif
                                </div>
                            </a>
                            @endforeach
                        </div>
                        @else
                        <div class="p-4 text-center">
                            <div class="w-12 h-12 bg-violet-50 rounded-full flex items-center justify-center mx-auto mb-2">
                                <i class="fas fa-graduation-cap text-violet-300 text-lg"></i>
                            </div>
                            <p class="text-xs text-gray-500 mb-2">Belum mengikuti kelas</p>
                            <a href="{{ route('opac.page', 'e-learning') }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-violet-600 text-white text-[10px] font-semibold rounded-lg hover:bg-violet-700 transition">
                                <i class="fas fa-search"></i> Cari Kelas
                            </a>
                        </div>
                        @endif
                    </div>

                    {{-- History Pinjaman --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-3 border-b border-gray-100 flex items-center justify-between">
                            <h2 class="font-bold text-gray-900 text-xs flex items-center gap-2">
                                <div class="w-6 h-6 bg-gray-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-history text-gray-500 text-[10px]"></i>
                                </div>
                                Riwayat Pinjaman
                            </h2>
                        </div>
                        
                        @if($history->count() > 0)
                            <div class="divide-y divide-gray-50 max-h-80 overflow-y-auto">
                                @foreach($history as $loan)
                                <div class="p-3 flex items-center gap-2">
                                    <div class="w-8 h-10 bg-gray-100 rounded flex-shrink-0 overflow-hidden">
                                        @if($loan->item?->book?->cover_url)
                                            <img src="{{ $loan->item->book->cover_url }}" alt="" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <i class="fas fa-book text-gray-300 text-[8px]"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-medium text-gray-900 line-clamp-1">{{ $loan->item?->book?->title ?? '-' }}</p>
                                        <p class="text-[10px] text-gray-400 mt-0.5">
                                            {{ $loan->return_date?->format('d M Y') ?? '-' }}
                                        </p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="px-1.5 py-0.5 bg-green-100 text-green-600 text-[9px] font-medium rounded">
                                            Selesai
                                        </span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-6 text-center">
                                <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                    <i class="fas fa-history text-gray-300 text-sm"></i>
                                </div>
                                <p class="text-xs text-gray-400">Belum ada riwayat</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Digital Card Modal --}}
    @if($showDigitalCard)
    <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm" x-data x-transition @click.self="$wire.set('showDigitalCard', false)">
        <div class="w-full max-w-sm">
            {{-- Card --}}
            <div class="bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-700 rounded-3xl shadow-2xl overflow-hidden relative" id="digital-card">
                {{-- Pattern --}}
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute top-0 right-0 w-40 h-40 bg-white rounded-full -translate-y-1/2 translate-x-1/2"></div>
                    <div class="absolute bottom-0 left-0 w-32 h-32 bg-white rounded-full translate-y-1/2 -translate-x-1/2"></div>
                </div>
                
                {{-- Header --}}
                <div class="relative p-5 pb-3">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                                <i class="fas fa-book-reader text-white text-lg"></i>
                            </div>
                            <div>
                                <p class="text-white font-bold text-sm">PERPUSTAKAAN</p>
                                <p class="text-blue-200 text-[10px]">UNIDA GONTOR</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-blue-200 text-[10px]">KARTU ANGGOTA</p>
                            <p class="text-white font-mono text-xs">{{ $member->member_id }}</p>
                        </div>
                    </div>
                </div>

                {{-- Content --}}
                <div class="relative bg-white mx-3 rounded-2xl p-4 mb-3">
                    <div class="flex gap-4">
                        {{-- Photo --}}
                        <div class="w-20 h-24 bg-gray-100 rounded-xl overflow-hidden flex-shrink-0 border-2 border-gray-200">
                            @if($member->photo)
                            <img src="{{ asset('storage/' . $member->photo) }}" class="w-full h-full object-cover">
                            @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
                                <i class="fas fa-user text-gray-400 text-2xl"></i>
                            </div>
                            @endif
                        </div>
                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-gray-900 text-sm leading-tight line-clamp-2">{{ $member->name }}</h3>
                            <p class="text-gray-500 text-xs mt-1">{{ $member->institution ?? $member->memberType?->name ?? 'Anggota' }}</p>
                            @if($member->faculty)
                            <p class="text-gray-400 text-[10px]">{{ $member->faculty->name ?? '' }}</p>
                            @endif
                            <div class="mt-2 flex items-center gap-1">
                                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 text-[10px] font-semibold rounded-full">
                                    <i class="fas fa-check-circle mr-0.5"></i> Aktif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- QR Code --}}
                <div class="relative px-3 pb-4">
                    <div class="bg-white rounded-2xl p-3 flex items-center gap-3">
                        <div class="w-20 h-20 bg-white rounded-xl flex items-center justify-center flex-shrink-0">
                            <img src="data:image/svg+xml;base64,{{ $this->qrCode }}" class="w-full h-full">
                        </div>
                        <div class="flex-1">
                            <p class="text-[10px] text-gray-400 mb-1">Scan untuk verifikasi</p>
                            <p class="text-xs text-gray-600">Berlaku s/d</p>
                            <p class="text-sm font-bold text-gray-900">{{ $member->expire_date?->format('d M Y') ?? 'Seumur Hidup' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex gap-2 mt-4 justify-center">
                <button onclick="downloadCard()" class="px-4 py-2.5 bg-white text-gray-700 text-xs font-semibold rounded-xl hover:bg-gray-100 transition flex items-center gap-2">
                    <i class="fas fa-download"></i> Simpan
                </button>
                <button onclick="window.print()" class="px-4 py-2.5 bg-white text-gray-700 text-xs font-semibold rounded-xl hover:bg-gray-100 transition flex items-center gap-2">
                    <i class="fas fa-print"></i> Cetak
                </button>
                <button wire:click="$set('showDigitalCard', false)" class="px-4 py-2.5 bg-white/20 text-white text-xs font-semibold rounded-xl hover:bg-white/30 transition flex items-center gap-2">
                    <i class="fas fa-times"></i> Tutup
                </button>
            </div>
        </div>
    </div>

    <script>
    function downloadCard() {
        html2canvas(document.getElementById('digital-card'), {
            scale: 2,
            backgroundColor: null
        }).then(canvas => {
            const link = document.createElement('a');
            link.download = 'kartu-anggota-{{ $member->member_id }}.png';
            link.href = canvas.toDataURL();
            link.click();
        });
    }
    </script>
    @endif
</div>
