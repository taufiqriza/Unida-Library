{{-- Quick Actions Popup Component --}}
<div class="relative" x-data="{ open: false }">
    {{-- Trigger Button --}}
    <button @click="open = !open"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-semibold text-sm shadow hover:shadow-lg hover:-translate-y-0.5 transition-all">
        <i class="fas fa-bolt"></i>
        <span class="hidden sm:inline">Aksi Cepat</span>
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
         @click.away="open = false"
         @keydown.escape.window="open = false"
         class="absolute right-0 top-full mt-2 w-[340px] bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden z-50"
         x-cloak>
        
        {{-- Header --}}
        <div class="bg-gradient-to-r from-blue-700 to-indigo-800 px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2 text-white">
                <i class="fas fa-bolt"></i>
                <span class="font-semibold text-sm">Aksi Cepat</span>
            </div>
            <button @click="open = false" class="w-6 h-6 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center text-white transition">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>

        {{-- Visitor Button --}}
        @php
            $visitorBranch = auth()->user()->branch_id ? \App\Models\Branch::find(auth()->user()->branch_id) : null;
        @endphp
        <div class="p-3 border-b border-gray-100">
            @if($visitorBranch)
            <a href="{{ route('visitor.kiosk', $visitorBranch->code) }}" target="_blank"
               class="flex items-center justify-between w-full px-4 py-3 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 rounded-xl transition">
                <div class="flex items-center gap-3">
                    <i class="fas fa-door-open text-white"></i>
                    <span class="text-white font-semibold text-sm">Visitor</span>
                </div>
                <i class="fas fa-external-link-alt text-white/70 text-xs"></i>
            </a>
            @else
            <div x-data="{ showBranches: false }" class="relative">
                <button @click="showBranches = !showBranches" class="flex items-center justify-between w-full px-4 py-3 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 rounded-xl transition">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-door-open text-white"></i>
                        <span class="text-white font-semibold text-sm">Visitor</span>
                    </div>
                    <i class="fas fa-chevron-down text-white/70 text-xs transition-transform" :class="showBranches && 'rotate-180'"></i>
                </button>
                <div x-show="showBranches" x-transition class="mt-2 bg-gray-50 rounded-xl max-h-40 overflow-y-auto">
                    @foreach(\App\Models\Branch::where('is_active', true)->orderBy('name')->get() as $branch)
                    <a href="{{ route('visitor.kiosk', $branch->code) }}" target="_blank"
                       class="flex items-center justify-between px-4 py-2 hover:bg-amber-50 transition text-sm">
                        <span class="text-gray-700">{{ $branch->name }}</span>
                        <i class="fas fa-external-link-alt text-gray-400 text-xs"></i>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Sirkulasi Section --}}
        <div class="p-3 border-b border-gray-100">
            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-2 px-1">Sirkulasi</p>
            <div class="grid grid-cols-3 gap-2">
                <a href="{{ route('staff.circulation.index') }}" wire:navigate @click="open = false"
                   class="group flex flex-col items-center gap-2 p-3 rounded-xl bg-blue-50 hover:bg-blue-100 transition-all">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center text-white shadow-sm group-hover:scale-110 transition-transform">
                        <i class="fas fa-qrcode"></i>
                    </div>
                    <span class="text-[10px] font-semibold text-gray-600 text-center">Scan Pinjam</span>
                </a>
                <a href="{{ route('staff.circulation.index') }}" wire:navigate @click="open = false"
                   class="group flex flex-col items-center gap-2 p-3 rounded-xl bg-emerald-50 hover:bg-emerald-100 transition-all">
                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center text-white shadow-sm group-hover:scale-110 transition-transform">
                        <i class="fas fa-rotate-left"></i>
                    </div>
                    <span class="text-[10px] font-semibold text-gray-600 text-center">Pengembalian</span>
                </a>
                <a href="{{ route('staff.circulation.index') }}" wire:navigate @click="open = false"
                   class="group flex flex-col items-center gap-2 p-3 rounded-xl bg-amber-50 hover:bg-amber-100 transition-all">
                    <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-orange-500 rounded-xl flex items-center justify-center text-white shadow-sm group-hover:scale-110 transition-transform">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <span class="text-[10px] font-semibold text-gray-600 text-center">Bayar Denda</span>
                </a>
            </div>
        </div>

        {{-- E-Library Section --}}
        <div class="p-3 border-b border-gray-100">
            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-2 px-1">E-Library</p>
            <div class="grid grid-cols-3 gap-2">
                <a href="{{ route('staff.elibrary.index', ['activeTab' => 'submissions']) }}" wire:navigate @click="open = false"
                   class="group flex flex-col items-center gap-2 p-3 rounded-xl bg-amber-50 hover:bg-amber-100 transition-all">
                    <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl flex items-center justify-center text-white shadow-sm group-hover:scale-110 transition-transform">
                        <i class="fas fa-upload"></i>
                    </div>
                    <span class="text-[10px] font-semibold text-gray-600 text-center">Unggah TA</span>
                </a>
                <a href="{{ route('staff.elibrary.index', ['activeTab' => 'plagiarism']) }}" wire:navigate @click="open = false"
                   class="group flex flex-col items-center gap-2 p-3 rounded-xl bg-rose-50 hover:bg-rose-100 transition-all">
                    <div class="w-10 h-10 bg-gradient-to-br from-rose-500 to-red-600 rounded-xl flex items-center justify-center text-white shadow-sm group-hover:scale-110 transition-transform">
                        <i class="fas fa-shield-halved"></i>
                    </div>
                    <span class="text-[10px] font-semibold text-gray-600 text-center">Cek Plagiasi</span>
                </a>
                <a href="{{ route('staff.elibrary.index') }}" wire:navigate @click="open = false"
                   class="group flex flex-col items-center gap-2 p-3 rounded-xl bg-cyan-50 hover:bg-cyan-100 transition-all">
                    <div class="w-10 h-10 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-xl flex items-center justify-center text-white shadow-sm group-hover:scale-110 transition-transform">
                        <i class="fas fa-cloud"></i>
                    </div>
                    <span class="text-[10px] font-semibold text-gray-600 text-center">E-Library</span>
                </a>
            </div>
        </div>

        {{-- Katalog & Lainnya Section --}}
        <div class="p-3">
            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-2 px-1">Katalog & Lainnya</p>
            <div class="grid grid-cols-3 gap-2">
                <a href="{{ route('staff.biblio.index') }}" wire:navigate @click="open = false"
                   class="group flex flex-col items-center gap-2 p-3 rounded-xl bg-violet-50 hover:bg-violet-100 transition-all">
                    <div class="w-10 h-10 bg-gradient-to-br from-violet-500 to-purple-600 rounded-xl flex items-center justify-center text-white shadow-sm group-hover:scale-110 transition-transform">
                        <i class="fas fa-book"></i>
                    </div>
                    <span class="text-[10px] font-semibold text-gray-600 text-center">Katalog</span>
                </a>
                <a href="{{ route('staff.member.index') }}" wire:navigate @click="open = false"
                   class="group flex flex-col items-center gap-2 p-3 rounded-xl bg-pink-50 hover:bg-pink-100 transition-all">
                    <div class="w-10 h-10 bg-gradient-to-br from-pink-500 to-rose-600 rounded-xl flex items-center justify-center text-white shadow-sm group-hover:scale-110 transition-transform">
                        <i class="fas fa-users"></i>
                    </div>
                    <span class="text-[10px] font-semibold text-gray-600 text-center">Anggota</span>
                </a>
                <a href="{{ route('staff.stock-opname.index') }}" wire:navigate @click="open = false"
                   class="group flex flex-col items-center gap-2 p-3 rounded-xl bg-slate-50 hover:bg-slate-100 transition-all">
                    <div class="w-10 h-10 bg-gradient-to-br from-slate-500 to-gray-600 rounded-xl flex items-center justify-center text-white shadow-sm group-hover:scale-110 transition-transform">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <span class="text-[10px] font-semibold text-gray-600 text-center">Stock Opname</span>
                </a>
            </div>
        </div>
    </div>
</div>
