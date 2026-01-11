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
               class="flex items-center justify-between w-full px-4 py-3 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 rounded-xl transition mb-2">
                <div class="flex items-center gap-3">
                    <i class="fas fa-door-open text-white"></i>
                    <span class="text-white font-semibold text-sm">Visitor</span>
                </div>
                <i class="fas fa-external-link-alt text-white/70 text-xs"></i>
            </a>
            @else
            <div x-data="{ showBranches: false }" class="relative mb-2">
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
            
            {{-- Short URL Button --}}
            <div x-data="shortUrlGenerator()">
                <button @click="@if(auth()->user()->role === 'super_admin') window.location.href='{{ route('staff.short-urls.index') }}' @else openModal() @endif" 
                        class="flex items-center justify-between w-full px-4 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 rounded-xl transition">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-link text-white"></i>
                        <span class="text-white font-semibold text-sm">
                            @if(auth()->user()->role === 'super_admin')
                                Short URL Dashboard
                            @else
                                Short URL
                            @endif
                        </span>
                    </div>
                    <i class="fas fa-{{ auth()->user()->role === 'super_admin' ? 'external-link-alt' : 'plus' }} text-white/70 text-xs"></i>
                </button>
                
                {{-- Short URL Modal --}}
                <template x-teleport="body">
                    <div x-show="showModal" 
                         x-cloak
                         class="fixed inset-0 z-[99999] flex items-center justify-center p-4"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         @keydown.escape.window="closeModal()">
                        
                        {{-- Backdrop --}}
                        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="closeModal()"></div>
                        
                        {{-- Modal Content --}}
                        <div class="relative bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                             @click.stop>
                            
                            {{-- Icon Header --}}
                            <div class="pt-8 pb-4 flex justify-center">
                                <div class="w-20 h-20 rounded-full bg-gradient-to-br from-blue-100 to-blue-50 flex items-center justify-center">
                                    <div class="w-14 h-14 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg shadow-blue-500/30">
                                        <i class="fas fa-link text-white text-2xl"></i>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Content --}}
                            <div class="px-6 pb-6">
                                <div class="text-center mb-6">
                                    <h3 class="text-xl font-bold text-gray-900 mb-2">Short URL Generator</h3>
                                    <p class="text-gray-500 text-sm">
                                        Buat link pendek profesional untuk berbagi dengan mudah
                                    </p>
                                </div>

                                <div class="space-y-4">
                                    {{-- URL Input --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-globe mr-2 text-blue-500"></i>URL Asli
                                        </label>
                                        <input 
                                            type="url" 
                                            x-model="originalUrl"
                                            placeholder="https://example.com/very-long-url"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                            required
                                        >
                                    </div>

                                    {{-- Title Input --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-heading mr-2 text-purple-500"></i>Judul (Opsional)
                                        </label>
                                        <input 
                                            type="text" 
                                            x-model="title"
                                            placeholder="Judul untuk link ini"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                        >
                                    </div>

                                    {{-- Custom Code Input --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-code mr-2 text-green-500"></i>Kode Kustom (Opsional)
                                        </label>
                                        <div class="flex rounded-xl overflow-hidden border border-gray-300 focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-transparent transition">
                                            <span class="inline-flex items-center px-3 text-sm text-gray-500 bg-gray-50">
                                                library.unida.gontor.ac.id/s/
                                            </span>
                                            <input 
                                                type="text" 
                                                x-model="customCode"
                                                placeholder="kode-unik"
                                                class="flex-1 px-4 py-3 border-0 focus:ring-0"
                                            >
                                        </div>
                                    </div>

                                    {{-- Generated URL Display --}}
                                    <div x-show="generatedUrl" class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl p-4">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-green-800 mb-2">
                                                    <i class="fas fa-check-circle mr-2"></i>Short URL berhasil dibuat!
                                                </p>
                                                <div class="flex items-center gap-2">
                                                    <code class="text-sm bg-white px-3 py-2 rounded-lg border text-green-700 flex-1 font-mono" x-text="generatedUrl"></code>
                                                    <button @click="copyUrl()" class="p-2 hover:bg-green-100 rounded-lg transition">
                                                        <i class="fas fa-copy text-green-600"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Actions --}}
                                <div class="flex gap-3 mt-6">
                                    <button 
                                        @click="closeModal()"
                                        class="flex-1 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition"
                                    >
                                        <span x-text="generatedUrl ? 'Tutup' : 'Batal'"></span>
                                    </button>
                                    <button 
                                        x-show="!generatedUrl"
                                        @click="generate()"
                                        :disabled="loading || !originalUrl"
                                        class="flex-1 px-4 py-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold rounded-xl shadow-lg shadow-blue-500/25 transition flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                                    >
                                        <i class="fas fa-magic" x-show="!loading"></i>
                                        <i class="fas fa-spinner fa-spin" x-show="loading"></i>
                                        <span x-text="loading ? 'Membuat...' : 'Generate'"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
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

        {{-- Katalog & Produktivitas Section --}}
        <div class="p-3">
            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-2 px-1">Katalog & Produktivitas</p>
            <div class="grid grid-cols-4 gap-2">
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
                <a href="{{ route('staff.task.notes') }}" wire:navigate @click="open = false"
                   class="group flex flex-col items-center gap-2 p-3 rounded-xl bg-amber-50 hover:bg-amber-100 transition-all">
                    <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-orange-500 rounded-xl flex items-center justify-center text-white shadow-sm group-hover:scale-110 transition-transform">
                        <i class="fas fa-sticky-note"></i>
                    </div>
                    <span class="text-[10px] font-semibold text-gray-600 text-center">Notes</span>
                </a>
                <a href="{{ route('staff.stock-opname.index') }}" wire:navigate @click="open = false"
                   class="group flex flex-col items-center gap-2 p-3 rounded-xl bg-slate-50 hover:bg-slate-100 transition-all">
                    <div class="w-10 h-10 bg-gradient-to-br from-slate-500 to-gray-600 rounded-xl flex items-center justify-center text-white shadow-sm group-hover:scale-110 transition-transform">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <span class="text-[10px] font-semibold text-gray-600 text-center">Stock</span>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function shortUrlGenerator() {
    return {
        showModal: false,
        originalUrl: '',
        title: '',
        customCode: '',
        generatedUrl: '',
        loading: false,
        errors: {},
        
        openModal() {
            this.showModal = true;
            this.reset();
        },
        
        closeModal() {
            this.showModal = false;
        },
        
        reset() {
            this.originalUrl = '';
            this.title = '';
            this.customCode = '';
            this.generatedUrl = '';
            this.errors = {};
            this.loading = false;
        },
        
        async generate() {
            this.loading = true;
            this.errors = {};
            
            try {
                const response = await fetch('/api/short-url', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        url: this.originalUrl,
                        title: this.title,
                        custom_code: this.customCode
                    })
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    this.generatedUrl = data.data.short_url;
                } else {
                    this.errors = data.errors || {};
                }
            } catch (error) {
                console.error('Error:', error);
                this.errors = { general: 'Terjadi kesalahan, coba lagi.' };
            } finally {
                this.loading = false;
            }
        },
        
        async copyToClipboard() {
            try {
                await navigator.clipboard.writeText(this.generatedUrl);
                
                // Show toast
                const toast = document.createElement('div');
                toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-md shadow-lg z-[200]';
                toast.innerHTML = '<i class="fas fa-check mr-2"></i>URL berhasil disalin!';
                document.body.appendChild(toast);
                
                setTimeout(() => {
                    if (document.body.contains(toast)) {
                        document.body.removeChild(toast);
                    }
                }, 3000);
            } catch (error) {
                console.error('Failed to copy:', error);
            }
        }
    }
}
</script>
