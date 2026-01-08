<div class="flex items-center gap-2">
    <style>
        @media (min-width: 768px) {
            .qa-text { display: inline !important; }
        }
    </style>
    
    {{-- Sirkulasi Button --}}
    <a href="{{ route('filament.admin.pages.circulation') }}" class="inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-sm font-medium bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 transition">
        <x-heroicon-o-arrow-path class="w-4 h-4" />
        <span style="display: none;" class="qa-text">Sirkulasi</span>
    </a>

    {{-- Quick Actions Dropdown --}}
    <x-filament::dropdown placement="bottom-end">
        <x-slot name="trigger">
            <button type="button" class="inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-sm font-medium bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 transition">
                <x-heroicon-o-bolt class="w-4 h-4" />
                <span style="display: none;" class="qa-text">Aksi</span>
            </button>
        </x-slot>

        <x-filament::dropdown.list>
            <x-filament::dropdown.list.item href="{{ route('filament.admin.pages.quick-return') }}" icon="heroicon-o-arrow-uturn-left" tag="a">
                Pengembalian Cepat
            </x-filament::dropdown.list.item>
            <x-filament::dropdown.list.item href="{{ route('filament.admin.resources.members.create') }}" icon="heroicon-o-user-plus" tag="a">
                Anggota Baru
            </x-filament::dropdown.list.item>
            <x-filament::dropdown.list.item href="{{ route('filament.admin.resources.books.create') }}" icon="heroicon-o-plus-circle" tag="a">
                Katalog Baru
            </x-filament::dropdown.list.item>
            <x-filament::dropdown.list.item href="{{ route('filament.admin.resources.items.create') }}" icon="heroicon-o-archive-box-arrow-down" tag="a">
                Eksemplar Baru
            </x-filament::dropdown.list.item>
            <x-filament::dropdown.list.item href="{{ route('filament.admin.pages.print-barcodes') }}" icon="heroicon-o-qr-code" tag="a">
                Cetak Barcode
            </x-filament::dropdown.list.item>
            
            {{-- Divider --}}
            <x-filament::dropdown.list.item>
                <div class="border-t border-gray-200 dark:border-gray-600 my-1"></div>
            </x-filament::dropdown.list.item>
            
            {{-- Short URL Generator --}}
            <x-filament::dropdown.list.item 
                x-data="{ showModal: false }"
                x-on:click="showModal = true"
                icon="heroicon-o-link"
                tag="button"
                class="w-full text-left"
            >
                Buat Short URL
            </x-filament::dropdown.list.item>
        </x-filament::dropdown.list>
    </x-filament::dropdown>

    {{-- Short URL Modal --}}
    <div x-data="shortUrlGenerator()" x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" x-on:click="closeModal()"></div>
            
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                            <x-heroicon-o-link class="h-6 w-6 text-blue-600" />
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
                                        <span x-show="errors.originalUrl" class="text-red-500 text-xs" x-text="errors.originalUrl"></span>
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
                                        <span x-show="errors.customCode" class="text-red-500 text-xs" x-text="errors.customCode"></span>
                                    </div>
                                </div>
                            </div>
                            
                            <div x-show="generatedUrl">
                                <div class="bg-green-50 border border-green-200 rounded-md p-4 mb-4">
                                    <div class="flex">
                                        <x-heroicon-o-check-circle class="h-5 w-5 text-green-400" />
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
                                            x-on:click="copyToClipboard()"
                                            class="px-4 py-2 bg-blue-600 text-white rounded-r-md hover:bg-blue-700"
                                        >
                                            <x-heroicon-o-clipboard class="w-4 h-4" />
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
                        x-on:click="generate()"
                        :disabled="loading"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 disabled:opacity-50 sm:ml-3 sm:w-auto sm:text-sm"
                    >
                        <span x-show="!loading">Generate</span>
                        <span x-show="loading">Loading...</span>
                    </button>
                    <button 
                        x-on:click="closeModal()"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                    >
                        <span x-text="generatedUrl ? 'Tutup' : 'Batal'"></span>
                    </button>
                </div>
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
        
        closeModal() {
            this.showModal = false;
            this.reset();
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
            } finally {
                this.loading = false;
            }
        },
        
        async copyToClipboard() {
            try {
                await navigator.clipboard.writeText(this.generatedUrl);
                
                // Show toast
                const toast = document.createElement('div');
                toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-md shadow-lg z-50';
                toast.textContent = 'URL berhasil disalin!';
                document.body.appendChild(toast);
                
                setTimeout(() => {
                    document.body.removeChild(toast);
                }, 3000);
            } catch (error) {
                console.error('Failed to copy:', error);
            }
        }
    }
}

// Listen for external modal trigger
document.addEventListener('DOMContentLoaded', function() {
    window.addEventListener('open-short-url-modal', function() {
        Alpine.store('shortUrl').showModal = true;
    });
});
</script>
