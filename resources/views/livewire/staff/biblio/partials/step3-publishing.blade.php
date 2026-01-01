<div class="p-5 border-b border-gray-100 bg-gradient-to-r from-primary-50 to-white">
    <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
        <i class="fas fa-building text-primary-500"></i> Penerbitan & Klasifikasi
    </h2>
</div>
<div class="p-5 space-y-5">
    {{-- Publisher Search --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5"><i class="fas fa-print text-gray-400 mr-1"></i> Penerbit</label>
        <div class="relative" x-data="{ 
            open: false, 
            search: '{{ $this->getPublisherName() ?? '' }}', 
            results: [],
            loading: false,
            async doSearch() {
                if (this.search.length < 2) { this.results = []; return; }
                this.loading = true;
                try {
                    const res = await fetch('/api/publishers/search?q=' + encodeURIComponent(this.search));
                    this.results = await res.json();
                } catch(e) { this.results = []; }
                this.loading = false;
            },
            select(pub) {
                this.search = pub.name;
                this.open = false;
                this.results = [];
                $wire.set('publisher_id', pub.id);
            },
            async createNew() {
                if (this.search.length < 2) return;
                this.loading = true;
                try {
                    const res = await fetch('/api/publishers', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content},
                        body: JSON.stringify({name: this.search})
                    });
                    const pub = await res.json();
                    if (pub.id) this.select(pub);
                } catch(e) {}
                this.loading = false;
            }
        }" @click.away="open = false">
            <div class="flex gap-2">
                <div class="relative flex-1">
                    <input type="text" x-model="search" @input.debounce.300ms="doSearch()" @focus="open = true" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Cari penerbit...">
                    <div x-show="loading" class="absolute right-3 top-1/2 -translate-y-1/2">
                        <i class="fas fa-spinner fa-spin text-gray-400"></i>
                    </div>
                </div>
                <button type="button" x-show="search.length >= 2" @click="createNew()" class="px-4 py-2.5 bg-primary-100 text-primary-700 text-sm font-medium rounded-xl hover:bg-primary-200 transition flex items-center gap-1.5 whitespace-nowrap">
                    <i class="fas fa-plus"></i> Tambah Baru
                </button>
            </div>
            <div x-show="open && results.length > 0" x-cloak class="absolute z-40 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-48 overflow-y-auto">
                <template x-for="pub in results" :key="pub.id">
                    <button type="button" @click="select(pub)" class="w-full px-4 py-2.5 text-left text-sm hover:bg-primary-50 border-b border-gray-50 last:border-0" x-text="pub.name"></button>
                </template>
            </div>
        </div>
        <p class="text-xs text-gray-400 mt-1.5">Cari penerbit atau klik "Tambah Baru"</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div x-data="{
            search: '', results: [], open: false,
            init() { 
                if (@js($place_id)) fetch('/api/places/' + @js($place_id)).then(r => r.json()).then(d => this.search = d.name || ''); 
            },
            doSearch() { 
                if (this.search.length > 1) fetch('/api/places/search?q=' + encodeURIComponent(this.search)).then(r => r.json()).then(d => { this.results = d; this.open = true; }); 
            },
            select(r) { 
                $wire.set('place_id', r.id); 
                this.search = r.name; 
                this.open = false; 
            },
            addNew() { 
                fetch('/api/places', { method: 'POST', headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content}, body: JSON.stringify({name: this.search}) })
                .then(r => r.json()).then(d => this.select(d)); 
            }
        }" x-init="init()" @click.away="open = false">
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Tempat Terbit</label>
            <div class="relative">
                <input type="text" x-model="search" @input.debounce.300ms="doSearch()" @focus="open = true" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Cari kota...">
                <div x-show="open && (results.length || search.length > 1)" x-cloak class="absolute z-50 w-full mt-1 bg-white border rounded-xl shadow-lg max-h-48 overflow-y-auto">
                    <template x-for="r in results" :key="r.id">
                        <div @click="select(r)" class="px-4 py-2 hover:bg-gray-50 cursor-pointer text-sm" x-text="r.name"></div>
                    </template>
                    <div x-show="search.length > 1 && !results.find(r => r.name.toLowerCase() === search.toLowerCase())" @click="addNew()" class="px-4 py-2 hover:bg-emerald-50 cursor-pointer text-sm text-emerald-600 border-t">
                        <i class="fas fa-plus mr-1"></i> Tambah "<span x-text="search"></span>"
                    </div>
                </div>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Tahun Terbit</label>
            <input type="text" wire:model="publish_year" maxlength="4" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="2024">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">ISBN/ISSN</label>
            <input type="text" wire:model="isbn" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="978-xxx-xxx">
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Kolasi</label>
            <input type="text" wire:model="collation" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="xii, 350 hlm. : ilus. ; 21 cm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Bahasa</label>
            <select wire:model="language" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                @foreach($languages as $code => $name)
                    <option value="{{ $code }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Klasifikasi Section --}}
    <div class="pt-4 border-t border-gray-100">
        <h3 class="text-sm font-semibold text-gray-800 mb-3 flex items-center gap-2">
            <span class="w-6 h-6 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center">
                <i class="fas fa-tag text-xs"></i>
            </span>
            Klasifikasi
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">No. Klasifikasi (DDC)</label>
                <div class="flex gap-2">
                    <input type="text" wire:model="classification" class="flex-1 px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="297.12">
                    <button type="button" @click="$dispatch('open-ddc-modal')" class="px-4 py-2.5 bg-purple-100 text-purple-700 text-sm font-medium rounded-xl hover:bg-purple-200 transition flex items-center gap-1.5">
                        <i class="fas fa-search"></i> DDC
                    </button>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">No. Panggil</label>
                <div class="flex gap-2">
                    <input type="text" wire:model="call_number" class="flex-1 px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="S 297.12 TIR M">
                    <button type="button" wire:click="generateCallNumber" class="px-4 py-2.5 bg-emerald-100 text-emerald-700 text-sm font-medium rounded-xl hover:bg-emerald-200 transition flex items-center gap-1.5">
                        <i class="fas fa-bolt"></i> Generate
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Judul Seri</label>
            <input type="text" wire:model="series_title" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Seri buku...">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Frekuensi (Terbitan Berseri)</label>
            <select wire:model="frequency_id" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                <option value="">-- Pilih --</option>
                @foreach($frequencies as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
