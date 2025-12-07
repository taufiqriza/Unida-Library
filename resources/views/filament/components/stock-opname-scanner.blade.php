<div x-data="{
    barcode: '',
    loading: false,
    recentScans: [],
    stats: {
        pending: {{ $record->total_items - $record->found_items - $record->missing_items }},
        found: {{ $record->found_items }},
        missing: {{ $record->missing_items }},
        total: {{ $record->total_items }}
    },
    
    get progress() {
        return this.stats.total > 0 ? Math.round(((this.stats.found + this.stats.missing) / this.stats.total) * 100) : 0;
    },
    
    async scan() {
        if (!this.barcode.trim() || this.loading) return;
        this.loading = true;
        
        try {
            const response = await fetch('{{ route('stock-opname.scan', $record->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ barcode: this.barcode })
            });
            
            const data = await response.json();
            
            if (data.success) {
                if (data.stats) {
                    this.stats.pending = data.stats.pending;
                    this.stats.found = data.stats.found;
                    this.stats.missing = data.stats.missing;
                }
                
                this.recentScans.unshift({
                    title: data.title,
                    barcode: this.barcode,
                    time: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
                });
                if (this.recentScans.length > 5) this.recentScans.pop();
                
                new FilamentNotification().title('✓ Ditemukan!').body(data.title).success().send();
            } else {
                new FilamentNotification().title('Gagal').body(data.message || 'Tidak ditemukan').danger().send();
            }
        } catch (error) {
            new FilamentNotification().title('Error').body(error.message).danger().send();
        }
        
        this.barcode = '';
        this.loading = false;
        this.$refs.input.focus();
    }
}">
    {{-- Stats Cards --}}
    <div style="margin-bottom: 1rem;">
        <div style="display: flex; gap: 0.5rem; margin-bottom: 0.75rem;">
            <div style="flex: 1; display: flex; align-items: center; gap: 0.5rem; padding: 0.75rem; border-radius: 0.5rem; background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#d97706" style="width: 1.25rem; height: 1.25rem;"><path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zM12.75 6a.75.75 0 00-1.5 0v6c0 .414.336.75.75.75h4.5a.75.75 0 000-1.5h-3.75V6z" clip-rule="evenodd" /></svg>
                <div>
                    <div style="font-size: 1.25rem; font-weight: 800; color: #92400e; line-height: 1;" x-text="stats.pending"></div>
                    <div style="font-size: 0.65rem; color: #b45309; text-transform: uppercase;">Pending</div>
                </div>
            </div>
            <div style="flex: 1; display: flex; align-items: center; gap: 0.5rem; padding: 0.75rem; border-radius: 0.5rem; background: linear-gradient(135deg, #d1fae5 0%, #6ee7b7 100%);">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#059669" style="width: 1.25rem; height: 1.25rem;"><path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" /></svg>
                <div>
                    <div style="font-size: 1.25rem; font-weight: 800; color: #065f46; line-height: 1;" x-text="stats.found"></div>
                    <div style="font-size: 0.65rem; color: #047857; text-transform: uppercase;">Ditemukan</div>
                </div>
            </div>
            <div style="flex: 1; display: flex; align-items: center; gap: 0.5rem; padding: 0.75rem; border-radius: 0.5rem; background: linear-gradient(135deg, #fee2e2 0%, #fca5a5 100%);">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#dc2626" style="width: 1.25rem; height: 1.25rem;"><path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm-1.72 6.97a.75.75 0 10-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 101.06 1.06L12 13.06l1.72 1.72a.75.75 0 101.06-1.06L13.06 12l1.72-1.72a.75.75 0 10-1.06-1.06L12 10.94l-1.72-1.72z" clip-rule="evenodd" /></svg>
                <div>
                    <div style="font-size: 1.25rem; font-weight: 800; color: #991b1b; line-height: 1;" x-text="stats.missing"></div>
                    <div style="font-size: 0.65rem; color: #b91c1c; text-transform: uppercase;">Hilang</div>
                </div>
            </div>
        </div>

        {{-- Progress Bar --}}
        <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.625rem 0.75rem; background: #f3f4f6; border-radius: 0.5rem;">
            <span style="font-size: 0.75rem; color: #6b7280;">Progress</span>
            <div style="flex: 1; height: 10px; background: #e5e7eb; border-radius: 9999px; overflow: hidden;">
                <div 
                    style="height: 100%; border-radius: 9999px; background: #10b981; transition: width 0.3s ease;"
                    :style="{ width: progress + '%' }"
                ></div>
            </div>
            <span style="font-size: 0.875rem; font-weight: 700; color: #059669; min-width: 3rem; text-align: right;" x-text="progress + '%'"></span>
        </div>

        {{-- Info --}}
        <div style="display: flex; justify-content: space-between; margin-top: 0.5rem; font-size: 0.75rem; color: #9ca3af;">
            <span>Total: <strong style="color: #6b7280;" x-text="stats.total"></strong> eksemplar</span>
            <span>{{ $record->branch?->name ?? 'Semua Cabang' }}</span>
        </div>
    </div>

    {{-- Scanner Input --}}
    <div style="margin-bottom: 1rem;">
        <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.25rem;" class="text-gray-700 dark:text-gray-300">Scan Barcode</label>
        <div style="display: flex; gap: 0.5rem;">
            <input 
                type="text" 
                x-ref="input"
                x-model="barcode"
                @keydown.enter.prevent="scan()"
                placeholder="Ketik atau scan barcode..."
                class="flex-1 px-4 py-3 border rounded-lg text-base font-mono bg-white dark:bg-gray-700 text-gray-900 dark:text-white border-gray-300 dark:border-gray-600 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                :disabled="loading"
                autofocus
            >
            <button 
                @click="scan()"
                :disabled="loading || !barcode.trim()"
                x-data="{ hover: false }"
                @mouseenter="hover = true"
                @mouseleave="hover = false"
                :style="{
                    padding: '12px 24px',
                    backgroundColor: (loading || !barcode.trim()) ? '#93c5fd' : (hover ? '#1d4ed8' : '#2563eb'),
                    color: 'white',
                    border: 'none',
                    borderRadius: '8px',
                    fontWeight: '600',
                    fontSize: '14px',
                    cursor: (loading || !barcode.trim()) ? 'not-allowed' : 'pointer',
                    transition: 'background-color 0.2s'
                }"
            >
                <span x-show="!loading">Scan</span>
                <span x-show="loading">...</span>
            </button>
        </div>
        <p style="margin-top: 0.25rem; font-size: 0.75rem; color: #9ca3af;">Tekan Enter setelah scan barcode</p>
    </div>

    {{-- Recent Scans --}}
    <div>
        <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;" class="text-gray-700 dark:text-gray-300">Scan Terakhir</label>
        <div style="max-height: 160px; overflow-y: auto; border-radius: 0.5rem; border: 1px solid #e5e7eb;">
            <template x-if="recentScans.length === 0">
                <p style="padding: 1rem; text-align: center; color: #9ca3af; font-size: 0.875rem;">Belum ada item yang di-scan</p>
            </template>
            <template x-for="(item, index) in recentScans" :key="index">
                <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.625rem 0.75rem; border-bottom: 1px solid #d1fae5; background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);">
                    <span style="width: 1.5rem; height: 1.5rem; display: flex; align-items: center; justify-content: center; background: #10b981; color: white; border-radius: 9999px; font-size: 0.7rem; font-weight: bold;">✓</span>
                    <div style="flex: 1; min-width: 0;">
                        <div style="font-size: 0.875rem; font-weight: 600; color: #065f46; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" x-text="item.title"></div>
                        <div style="font-size: 0.75rem; color: #047857; font-family: monospace;" x-text="item.barcode"></div>
                    </div>
                    <span style="font-size: 0.7rem; color: #6b7280;" x-text="item.time"></span>
                </div>
            </template>
        </div>
    </div>
</div>
