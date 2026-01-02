<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Import Koleksi Buku</h1>
            <p class="text-gray-500">Upload file Excel untuk import data buku secara massal</p>
        </div>
        <a href="{{ route('staff.biblio.index') }}" class="btn btn-ghost">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    @if(!$batch)
    {{-- Upload Form --}}
    <div class="grid lg:grid-cols-2 gap-6">
        {{-- Left: Upload --}}
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-lg">
                    <i class="fas fa-upload text-primary"></i>
                    Upload File
                </h2>

                <form wire:submit="upload" class="space-y-4">
                    {{-- Branch Selection --}}
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Cabang Perpustakaan</span>
                        </label>
                        <select wire:model="branchId" class="select select-bordered w-full">
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Excel File --}}
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">File Excel <span class="text-error">*</span></span>
                        </label>
                        <input type="file" wire:model="excelFile" accept=".xlsx,.xls" 
                            class="file-input file-input-bordered w-full" />
                        @error('excelFile') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        <label class="label">
                            <span class="label-text-alt text-gray-500">Format: .xlsx atau .xls (max 10MB)</span>
                        </label>
                    </div>

                    {{-- Covers ZIP --}}
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">File Cover (ZIP)</span>
                        </label>
                        <input type="file" wire:model="coversZip" accept=".zip" 
                            class="file-input file-input-bordered w-full" />
                        @error('coversZip') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        <label class="label">
                            <span class="label-text-alt text-gray-500">Opsional. ZIP berisi folder covers/ (max 100MB)</span>
                        </label>
                    </div>

                    <div class="flex gap-3 pt-4">
                        <button type="submit" class="btn btn-primary flex-1" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="upload">
                                <i class="fas fa-cloud-upload-alt mr-2"></i> Upload & Validasi
                            </span>
                            <span wire:loading wire:target="upload">
                                <i class="fas fa-spinner fa-spin mr-2"></i> Memproses...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Right: Download Template --}}
        <div class="card bg-gradient-to-br from-primary/10 to-secondary/10 shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-lg">
                    <i class="fas fa-file-excel text-success"></i>
                    Download Template
                </h2>
                
                <p class="text-gray-600">
                    Download template Excel yang sudah disiapkan dengan format yang benar. 
                    Template berisi:
                </p>

                <ul class="space-y-2 text-sm">
                    <li class="flex items-start gap-2">
                        <i class="fas fa-check-circle text-success mt-0.5"></i>
                        <span><strong>Sheet Data Koleksi</strong> - Form input dengan dropdown</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-check-circle text-success mt-0.5"></i>
                        <span><strong>Sheet Panduan</strong> - Instruksi pengisian lengkap</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-check-circle text-success mt-0.5"></i>
                        <span><strong>Sheet Daftar DDC</strong> - Referensi kode klasifikasi</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-check-circle text-success mt-0.5"></i>
                        <span><strong>Sheet Kategori</strong> - Daftar kategori tersedia</span>
                    </li>
                </ul>

                <div class="card-actions mt-4">
                    <button wire:click="downloadTemplate" class="btn btn-success w-full">
                        <i class="fas fa-download mr-2"></i> Download Template Excel
                    </button>
                </div>

                <div class="divider">Panduan Cover</div>

                <div class="bg-base-100 rounded-lg p-4 text-sm space-y-2">
                    <p class="font-medium">Cara menyertakan cover buku:</p>
                    <ol class="list-decimal list-inside space-y-1 text-gray-600">
                        <li>Foto cover buku, simpan dengan nama unik (misal: <code class="bg-gray-100 px-1 rounded">buku001.jpg</code>)</li>
                        <li>Tulis nama file di kolom "Cover File" pada Excel</li>
                        <li>Kumpulkan semua foto dalam folder <code class="bg-gray-100 px-1 rounded">covers</code></li>
                        <li>Compress folder menjadi <code class="bg-gray-100 px-1 rounded">covers.zip</code></li>
                        <li>Upload ZIP bersama file Excel</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    @else
    {{-- Preview Section --}}
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            {{-- Stats --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="stat bg-base-200 rounded-xl p-4">
                    <div class="stat-title text-xs">Total</div>
                    <div class="stat-value text-2xl">{{ $stats['total'] ?? 0 }}</div>
                </div>
                <div class="stat bg-success/10 rounded-xl p-4">
                    <div class="stat-title text-xs">Valid</div>
                    <div class="stat-value text-2xl text-success">{{ $stats['valid'] ?? 0 }}</div>
                </div>
                <div class="stat bg-warning/10 rounded-xl p-4">
                    <div class="stat-title text-xs">Warning</div>
                    <div class="stat-value text-2xl text-warning">{{ $stats['warning'] ?? 0 }}</div>
                </div>
                <div class="stat bg-error/10 rounded-xl p-4">
                    <div class="stat-title text-xs">Error</div>
                    <div class="stat-value text-2xl text-error">{{ $stats['error'] ?? 0 }}</div>
                </div>
            </div>

            {{-- Filters --}}
            <div class="flex flex-wrap gap-3 mb-4">
                <div class="join">
                    <button wire:click="$set('filterStatus', 'all')" 
                        class="join-item btn btn-sm {{ $filterStatus === 'all' ? 'btn-primary' : 'btn-ghost' }}">
                        Semua
                    </button>
                    <button wire:click="$set('filterStatus', 'valid')" 
                        class="join-item btn btn-sm {{ $filterStatus === 'valid' ? 'btn-success' : 'btn-ghost' }}">
                        <i class="fas fa-check-circle mr-1"></i> Valid
                    </button>
                    <button wire:click="$set('filterStatus', 'warning')" 
                        class="join-item btn btn-sm {{ $filterStatus === 'warning' ? 'btn-warning' : 'btn-ghost' }}">
                        <i class="fas fa-exclamation-triangle mr-1"></i> Warning
                    </button>
                    <button wire:click="$set('filterStatus', 'error')" 
                        class="join-item btn btn-sm {{ $filterStatus === 'error' ? 'btn-error' : 'btn-ghost' }}">
                        <i class="fas fa-times-circle mr-1"></i> Error
                    </button>
                </div>

                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari judul/penulis..." 
                    class="input input-bordered input-sm w-64" />
            </div>

            {{-- Preview Table --}}
            <div class="overflow-x-auto">
                <table class="table table-sm">
                    <thead>
                        <tr class="bg-base-200">
                            <th class="w-12">#</th>
                            <th class="w-16">Cover</th>
                            <th>Detail Buku</th>
                            <th class="w-24">Status</th>
                            <th class="w-20">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($filteredPreview as $index => $row)
                        <tr class="hover {{ $row['status'] === 'error' ? 'bg-error/5' : ($row['status'] === 'warning' ? 'bg-warning/5' : '') }}">
                            <td class="font-mono text-xs">{{ $index + 1 }}</td>
                            <td>
                                @if($row['data']['cover_found'] ?? false)
                                    <div class="w-12 h-16 bg-gray-100 rounded flex items-center justify-center">
                                        <i class="fas fa-image text-success"></i>
                                    </div>
                                @else
                                    <div class="w-12 h-16 bg-gray-100 rounded flex items-center justify-center">
                                        <i class="fas fa-image text-gray-300"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="font-medium">{{ $row['data']['title'] ?? '-' }}</div>
                                <div class="text-sm text-gray-500">
                                    {{ $row['data']['authors'] ?? '-' }} | {{ $row['data']['year'] ?? '-' }}
                                </div>
                                <div class="text-xs text-gray-400">
                                    DDC: {{ $row['data']['ddc'] ?? '-' }} → 
                                    <span class="font-mono">{{ $row['data']['call_number'] ?? '(tidak bisa generate)' }}</span>
                                </div>
                                @if(!empty($row['errors']))
                                    @foreach($row['errors'] as $error)
                                        <div class="text-xs text-error mt-1">
                                            <i class="fas fa-times-circle mr-1"></i> {{ $error }}
                                        </div>
                                    @endforeach
                                @endif
                                @if(!empty($row['warnings']))
                                    @foreach($row['warnings'] as $warning)
                                        <div class="text-xs text-warning mt-1">
                                            <i class="fas fa-exclamation-triangle mr-1"></i> {{ $warning }}
                                        </div>
                                    @endforeach
                                @endif
                            </td>
                            <td>
                                @if($row['status'] === 'valid')
                                    <span class="badge badge-success badge-sm">Valid</span>
                                @elseif($row['status'] === 'warning')
                                    <span class="badge badge-warning badge-sm">Warning</span>
                                @else
                                    <span class="badge badge-error badge-sm">Error</span>
                                @endif
                            </td>
                            <td>
                                <button wire:click="showDetail({{ $index }})" class="btn btn-ghost btn-xs">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-8 text-gray-500">
                                Tidak ada data yang sesuai filter
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Import Options --}}
            <div class="divider"></div>
            
            <div class="bg-base-200 rounded-xl p-4 space-y-4">
                <h3 class="font-semibold">Opsi Import</h3>
                
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" wire:model="includeWarnings" class="checkbox checkbox-primary" />
                    <span>Import data dengan warning (cover/DDC kosong akan dilewati)</span>
                </label>

                <div class="flex gap-3">
                    <button wire:click="cancelImport" class="btn btn-ghost">
                        <i class="fas fa-times mr-2"></i> Batalkan
                    </button>
                    <button wire:click="executeImport" class="btn btn-primary flex-1" 
                        {{ ($stats['valid'] ?? 0) + ($includeWarnings ? ($stats['warning'] ?? 0) : 0) === 0 ? 'disabled' : '' }}>
                        <i class="fas fa-check mr-2"></i> 
                        Import {{ ($stats['valid'] ?? 0) + ($includeWarnings ? ($stats['warning'] ?? 0) : 0) }} Buku
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Detail Modal --}}
    @if($showDetailModal && $selectedRow)
    <div class="modal modal-open">
        <div class="modal-box max-w-2xl">
            <button wire:click="$set('showDetailModal', false)" class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
            
            <h3 class="font-bold text-lg mb-4">
                <i class="fas fa-book mr-2 text-primary"></i>
                Detail Buku
            </h3>

            <div class="grid md:grid-cols-3 gap-4">
                {{-- Cover --}}
                <div class="flex flex-col items-center">
                    <div class="w-32 h-44 bg-gray-100 rounded-lg flex items-center justify-center border">
                        @if($selectedRow['data']['cover_found'] ?? false)
                            <i class="fas fa-image text-4xl text-success"></i>
                        @else
                            <i class="fas fa-image text-4xl text-gray-300"></i>
                        @endif
                    </div>
                    <span class="text-xs text-gray-500 mt-2">
                        {{ $selectedRow['data']['cover_file'] ?: 'Tidak ada cover' }}
                    </span>
                </div>

                {{-- Info --}}
                <div class="md:col-span-2 space-y-3">
                    <div>
                        <label class="text-xs text-gray-500">Judul</label>
                        <p class="font-medium">{{ $selectedRow['data']['title'] ?? '-' }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs text-gray-500">Penulis</label>
                            <p>{{ $selectedRow['data']['authors'] ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500">ISBN</label>
                            <p>{{ $selectedRow['data']['isbn'] ?: '-' }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500">Penerbit</label>
                            <p>{{ $selectedRow['data']['publisher'] ?: '-' }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500">Tahun</label>
                            <p>{{ $selectedRow['data']['year'] ?: '-' }}</p>
                        </div>
                    </div>

                    <div class="divider my-2"></div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs text-gray-500">DDC</label>
                            <p>{{ $selectedRow['data']['ddc'] ?: '-' }}</p>
                            @if($selectedRow['data']['ddc_name'] ?? false)
                                <p class="text-xs text-gray-500">{{ $selectedRow['data']['ddc_name'] }}</p>
                            @endif
                        </div>
                        <div>
                            <label class="text-xs text-gray-500">Call Number</label>
                            <p class="font-mono">{{ $selectedRow['data']['call_number'] ?: '(tidak bisa generate)' }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500">Eksemplar</label>
                            <p>{{ $selectedRow['data']['quantity'] ?? 1 }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500">Lokasi Rak</label>
                            <p>{{ $selectedRow['data']['location'] ?: '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Validation Status --}}
            <div class="mt-4 p-3 rounded-lg {{ $selectedRow['status'] === 'error' ? 'bg-error/10' : ($selectedRow['status'] === 'warning' ? 'bg-warning/10' : 'bg-success/10') }}">
                <h4 class="font-medium mb-2">Status Validasi</h4>
                
                @if($selectedRow['status'] === 'valid')
                    <p class="text-success text-sm"><i class="fas fa-check-circle mr-1"></i> Semua validasi berhasil</p>
                @endif

                @foreach($selectedRow['errors'] ?? [] as $error)
                    <p class="text-error text-sm"><i class="fas fa-times-circle mr-1"></i> {{ $error }}</p>
                @endforeach

                @foreach($selectedRow['warnings'] ?? [] as $warning)
                    <p class="text-warning text-sm"><i class="fas fa-exclamation-triangle mr-1"></i> {{ $warning }}</p>
                @endforeach
            </div>

            <div class="modal-action">
                <button wire:click="$set('showDetailModal', false)" class="btn">Tutup</button>
            </div>
        </div>
        <div class="modal-backdrop" wire:click="$set('showDetailModal', false)"></div>
    </div>
    @endif
</div>
