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
                <button @click="openModal()" 
                        class="flex items-center justify-between w-full px-4 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 rounded-xl transition">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-link text-white"></i>
                        <span class="text-white font-semibold text-sm">Short URL</span>
                    </div>
                    <i class="fas fa-plus text-white/70 text-xs"></i>
                </button>
                
                {{-- Short URL Modal --}}
                <div x-show="showModal" x-cloak class="fixed inset-0 z-[100] overflow-y-auto">
                    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal()"></div>
                        
                        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start">
                                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                        <i class="fas fa-link text-blue-600"></i>
                                    </div>
                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Buat Short URL</h3>
                                        
                                        <div x-show="!generatedUrl">
                                            <div class="space-y-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">URL Asli *</label>
                                                    <input 
                                                        type="url" 
                                                        x-model="originalUrl"
                                                        placeholder="https://onedrive.live.com/..."
                                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                    >
                                                    <span x-show="errors.url" class="text-red-500 text-xs" x-text="errors.url"></span>
                                                </div>
                                                
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul (Opsional)</label>
                                                    <input 
                                                        type="text" 
                                                        x-model="title"
                                                        placeholder="Deskripsi singkat..."
                                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                    >
                                                </div>
                                                
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Kode Custom (Opsional)</label>
                                                    <div class="flex">
                                                        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                                            {{ url('/s/') }}/
                                                        </span>
                                                        <input 
                                                            type="text" 
                                                            x-model="customCode"
                                                            placeholder="abc123"
                                                            class="flex-1 px-3 py-2 border border-gray-300 rounded-r-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                            maxlength="10"
                                                        >
                                                    </div>
                                                    <span x-show="errors.custom_code" class="text-red-500 text-xs" x-text="errors.custom_code"></span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div x-show="generatedUrl">
                                            <div class="bg-green-50 border border-green-200 rounded-md p-4 mb-4">
                                                <div class="flex">
                                                    <i class="fas fa-check-circle text-green-400 mt-0.5"></i>
                                                    <div class="ml-3">
                                                        <h3 class="text-sm font-medium text-green-800">Short URL Berhasil Dibuat!</h3>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Short URL Anda:</label>
                                                <div class="flex">
                                                    <input 
                                                        type="text" 
                                                        x-model="generatedUrl"
                                                        readonly
                                                        class="flex-1 px-3 py-2 border border-gray-300 rounded-l-md bg-gray-50"
                                                    >
                                                    <button 
                                                        @click="copyToClipboard()"
                                                        class="px-4 py-2 bg-blue-600 text-white rounded-r-md hover:bg-blue-700"
                                                    >
                                                        <i class="fas fa-copy"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button 
                                    x-show="!generatedUrl"
                                    @click="generate()"
                                    :disabled="loading"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 disabled:opacity-50 sm:ml-3 sm:w-auto sm:text-sm"
                                >
                                    <span x-show="!loading">Generate</span>
                                    <span x-show="loading">Loading...</span>
                                </button>
                                <button 
                                    @click="closeModal()"
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                                >
                                    <span x-text="generatedUrl ? 'Tutup' : 'Batal'"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
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
