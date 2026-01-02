<div class="space-y-5">
    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 via-teal-500 to-cyan-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-emerald-500/25">
                <i class="fas fa-file-import text-xl"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Import Koleksi Buku</h1>
                <p class="text-sm text-gray-500">Upload file Excel untuk import data buku secara massal</p>
            </div>
        </div>
        <a href="{{ route('staff.biblio.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition text-sm">
            <i class="fas fa-arrow-left"></i><span>Kembali</span>
        </a>
    </div>

    @if(!$batch)
    {{-- Upload Section --}}
    <div class="grid lg:grid-cols-2 gap-5">
        {{-- Left: Upload Form --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-5 py-4">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    <i class="fas fa-cloud-upload-alt"></i> Upload File
                </h2>
            </div>
            <div class="p-5">
                <form wire:submit.prevent="processUpload" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Cabang Perpustakaan</label>
                        <select wire:model="branchId" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 text-gray-900 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">File Excel <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="file" wire:model="excelFile" accept=".xlsx,.xls" class="w-full px-4 py-3 border-2 border-dashed border-gray-300 rounded-xl bg-gray-50 text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-100 file:text-blue-700 file:font-medium hover:file:bg-blue-200 focus:border-blue-500 transition cursor-pointer" />
                            <div wire:loading wire:target="excelFile" class="absolute inset-0 bg-white/80 rounded-xl flex items-center justify-center">
                                <i class="fas fa-spinner fa-spin text-blue-500 mr-2"></i> Mengupload...
                            </div>
                        </div>
                        @error('excelFile') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        <p class="text-xs text-gray-500 mt-1.5">Format: .xlsx atau .xls (max 10MB)</p>
                        @if($excelFile)
                        <p class="text-xs text-emerald-600 mt-1"><i class="fas fa-check-circle mr-1"></i>{{ $excelFile->getClientOriginalName() }}</p>
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">File Cover (ZIP) <span class="text-gray-400 font-normal">- Opsional</span></label>
                        <input type="file" wire:model="coversZip" accept=".zip" class="w-full px-4 py-3 border-2 border-dashed border-gray-300 rounded-xl bg-gray-50 text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-green-100 file:text-green-700 file:font-medium hover:file:bg-green-200 focus:border-green-500 transition cursor-pointer" />
                        @error('coversZip') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        <p class="text-xs text-gray-500 mt-1.5">ZIP berisi gambar cover (max 100MB)</p>
                    </div>

                    <button type="submit" class="w-full px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold rounded-xl shadow-lg shadow-blue-500/25 transition flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed" wire:loading.attr="disabled" wire:target="excelFile,coversZip,processUpload">
                        <span wire:loading.remove wire:target="processUpload"><i class="fas fa-cloud-upload-alt"></i> Upload & Validasi</span>
                        <span wire:loading wire:target="processUpload"><i class="fas fa-spinner fa-spin"></i> Memproses...</span>
                    </button>
                </form>
            </div>
        </div>

        {{-- Right: Download Template --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-emerald-500 to-teal-600 px-5 py-4">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    <i class="fas fa-file-excel"></i> Download Template
                </h2>
            </div>
            <div class="p-5">
                <p class="text-gray-600 mb-4">Template Excel standar untuk import data koleksi perpustakaan:</p>
                <div class="space-y-2 mb-5">
                    @foreach([
                        ['icon' => 'fa-table', 'color' => 'blue', 'title' => 'Sheet Data Koleksi', 'desc' => 'Form input data buku dengan validasi'],
                        ['icon' => 'fa-book-open', 'color' => 'purple', 'title' => 'Sheet Panduan', 'desc' => 'Petunjuk pengisian setiap kolom'],
                        ['icon' => 'fa-list-ol', 'color' => 'amber', 'title' => 'Sheet Daftar DDC', 'desc' => 'Referensi kode klasifikasi Dewey'],
                        ['icon' => 'fa-tags', 'color' => 'emerald', 'title' => 'Sheet Kategori', 'desc' => 'Daftar subjek yang tersedia'],
                    ] as $item)
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                        <div class="w-9 h-9 bg-{{ $item['color'] }}-100 rounded-lg flex items-center justify-center">
                            <i class="fas {{ $item['icon'] }} text-{{ $item['color'] }}-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 text-sm">{{ $item['title'] }}</p>
                            <p class="text-xs text-gray-500">{{ $item['desc'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>

                <button wire:click="downloadTemplate" class="w-full px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-semibold rounded-xl shadow-lg shadow-emerald-500/25 transition flex items-center justify-center gap-2">
                    <i class="fas fa-download"></i> Download Template Excel
                </button>

                <div class="mt-5 pt-5 border-t border-gray-100">
                    <p class="font-medium text-gray-900 text-sm mb-2"><i class="fas fa-image text-gray-400 mr-1"></i> Panduan Cover:</p>
                    <ol class="list-decimal list-inside space-y-1 text-xs text-gray-600">
                        <li>Foto cover, simpan dengan nama unik <code class="bg-gray-100 px-1.5 py-0.5 rounded">buku001.jpg</code></li>
                        <li>Tulis nama file di kolom "Cover File"</li>
                        <li>Compress semua foto menjadi <code class="bg-gray-100 px-1.5 py-0.5 rounded">covers.zip</code></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    @else
    {{-- Preview Section --}}
    
    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl p-4 text-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center"><i class="fas fa-list text-lg"></i></div>
                <div><p class="text-2xl font-bold">{{ $stats['total'] ?? 0 }}</p><p class="text-xs text-blue-100">Total Baris</p></div>
            </div>
        </div>
        <div class="bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl p-4 text-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center"><i class="fas fa-check-circle text-lg"></i></div>
                <div><p class="text-2xl font-bold">{{ $stats['valid'] ?? 0 }}</p><p class="text-xs text-emerald-100">Valid</p></div>
            </div>
        </div>
        <div class="bg-gradient-to-r from-amber-500 to-orange-600 rounded-xl p-4 text-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center"><i class="fas fa-exclamation-triangle text-lg"></i></div>
                <div><p class="text-2xl font-bold">{{ $stats['warning'] ?? 0 }}</p><p class="text-xs text-amber-100">Warning</p></div>
            </div>
        </div>
        <div class="bg-gradient-to-r from-red-500 to-rose-600 rounded-xl p-4 text-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center"><i class="fas fa-times-circle text-lg"></i></div>
                <div><p class="text-2xl font-bold">{{ $stats['error'] ?? 0 }}</p><p class="text-xs text-red-100">Error</p></div>
            </div>
        </div>
    </div>

    {{-- Preview Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        {{-- Filters --}}
        <div class="px-5 py-4 border-b border-gray-100 flex flex-wrap items-center gap-3">
            <div class="inline-flex rounded-xl bg-gray-100 p-1">
                @foreach(['all' => 'Semua', 'valid' => 'Valid', 'warning' => 'Warning', 'error' => 'Error'] as $key => $label)
                <button wire:click="$set('filterStatus', '{{ $key }}')" 
                    class="px-4 py-2 text-sm font-medium rounded-lg transition {{ $filterStatus === $key ? ($key === 'valid' ? 'bg-emerald-500 text-white' : ($key === 'warning' ? 'bg-amber-500 text-white' : ($key === 'error' ? 'bg-red-500 text-white' : 'bg-white text-gray-900 shadow'))) : 'text-gray-600 hover:text-gray-900' }}">
                    {{ $label }}
                </button>
                @endforeach
            </div>
            <div class="relative flex-1 max-w-xs">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari judul/penulis..." 
                    class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-xl bg-gray-50 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" />
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-2 py-2 text-left text-xs font-semibold text-gray-600 w-8">#</th>
                        <th class="px-2 py-2 text-left text-xs font-semibold text-gray-600 w-12"></th>
                        <th class="px-2 py-2 text-left text-xs font-semibold text-gray-600">Judul & Penulis</th>
                        <th class="px-2 py-2 text-left text-xs font-semibold text-gray-600 w-32">Penerbit</th>
                        <th class="px-2 py-2 text-left text-xs font-semibold text-gray-600 w-28">DDC & Call#</th>
                        <th class="px-2 py-2 text-left text-xs font-semibold text-gray-600 w-24">Subjek</th>
                        <th class="px-2 py-2 text-center text-xs font-semibold text-gray-600 w-20">Bahasa/Media</th>
                        <th class="px-2 py-2 text-left text-xs font-semibold text-gray-600 w-20">Status</th>
                        <th class="px-2 py-2 w-8"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($filteredPreview as $index => $row)
                    <tr class="hover:bg-gray-50/50 transition {{ $row['status'] === 'error' ? 'bg-red-50/50' : ($row['status'] === 'warning' ? 'bg-amber-50/50' : '') }}">
                        <td class="px-2 py-2 font-mono text-xs text-gray-400">{{ $index + 1 }}</td>
                        <td class="px-2 py-2">
                            <div class="relative w-10 h-14">
                                <div class="w-full h-full rounded overflow-hidden border border-gray-200">
                                    @if($row['data']['cover_preview_url'] ?? false)
                                        <img src="{{ $row['data']['cover_preview_url'] }}" alt="" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                                            <i class="fas fa-image text-gray-300 text-xs"></i>
                                        </div>
                                    @endif
                                </div>
                                <span class="absolute -top-2 -right-2 z-10 w-5 h-5 bg-blue-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center border-2 border-white shadow">{{ $row['data']['quantity'] ?? 1 }}</span>
                            </div>
                        </td>
                        <td class="px-2 py-2">
                            <p class="font-medium text-gray-900 line-clamp-1">{{ $row['data']['title'] ?? '-' }}</p>
                            <p class="text-xs text-gray-500 line-clamp-1">{{ $row['data']['authors'] ?? '-' }}</p>
                            <p class="text-xs text-gray-400">{{ $row['data']['isbn'] ?: '-' }} · {{ $row['data']['location'] ?: 'Rak -' }}</p>
                            @foreach($row['errors'] ?? [] as $err)
                            <p class="text-xs text-red-600"><i class="fas fa-times-circle mr-1"></i>{{ $err }}</p>
                            @endforeach
                            @foreach($row['warnings'] ?? [] as $warn)
                            <p class="text-xs text-amber-600"><i class="fas fa-exclamation-triangle mr-1"></i>{{ $warn }}</p>
                            @endforeach
                        </td>
                        <td class="px-2 py-2 text-xs">
                            <p class="text-gray-700 truncate">{{ $row['data']['publisher'] ?: '-' }}</p>
                            <p class="text-gray-400">{{ $row['data']['publish_place'] ?: '-' }}, {{ $row['data']['year'] ?: '-' }}</p>
                        </td>
                        <td class="px-2 py-2 text-xs font-mono">
                            <p class="text-gray-500">{{ $row['data']['ddc'] ?: '-' }}</p>
                            <p class="text-gray-700">{{ $row['data']['call_number'] ?: '-' }}</p>
                        </td>
                        <td class="px-2 py-2 text-xs text-gray-600 truncate">{{ $row['data']['subject'] ?: '-' }}</td>
                        <td class="px-2 py-2 text-xs text-gray-500 text-center">
                            <p>{{ strtoupper($row['data']['language'] ?? 'id') }}</p>
                            <p class="text-gray-400 truncate">{{ $row['data']['media'] ?: '-' }}</p>
                        </td>
                        <td class="px-2 py-2">
                            <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium {{ $row['status'] === 'valid' ? 'bg-emerald-100 text-emerald-700' : ($row['status'] === 'warning' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                                {{ ucfirst($row['status']) }}
                            </span>
                        </td>
                        <td class="px-2 py-2">
                            <button wire:click="showDetail({{ $index }})" class="text-gray-400 hover:text-blue-600"><i class="fas fa-eye"></i></button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="px-4 py-12 text-center text-gray-500">Tidak ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Import Actions --}}
        <div class="px-5 py-4 bg-gray-50 border-t border-gray-100">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" wire:model="includeWarnings" class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                    <span class="text-sm text-gray-700">Sertakan data dengan warning</span>
                </label>
                <div class="flex gap-3">
                    <button wire:click="cancelImport" class="px-5 py-2.5 text-gray-700 hover:bg-gray-200 font-medium rounded-xl transition">
                        <i class="fas fa-times mr-2"></i>Batalkan
                    </button>
                    <button wire:click="executeImport" wire:loading.attr="disabled" wire:target="executeImport" class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold rounded-xl shadow-lg shadow-blue-500/25 transition disabled:opacity-50"
                        {{ ($stats['valid'] ?? 0) + ($includeWarnings ? ($stats['warning'] ?? 0) : 0) === 0 ? 'disabled' : '' }}>
                        <span wire:loading.remove wire:target="executeImport"><i class="fas fa-check mr-2"></i>Import {{ ($stats['valid'] ?? 0) + ($includeWarnings ? ($stats['warning'] ?? 0) : 0) }} Buku</span>
                        <span wire:loading wire:target="executeImport"><i class="fas fa-spinner fa-spin mr-2"></i>Mengimport...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Detail Modal --}}
    @if($showDetailModal && $selectedRow)
    @teleport('body')
    <div class="fixed inset-0 z-[99999] overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" wire:click="$set('showDetailModal', false)"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-2xl w-full overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-white"><i class="fas fa-book mr-2"></i>Detail Buku</h3>
                    <button wire:click="$set('showDetailModal', false)" class="text-white/80 hover:text-white"><i class="fas fa-times text-xl"></i></button>
                </div>
                <div class="p-6">
                    <div class="grid md:grid-cols-3 gap-5">
                        <div class="flex flex-col items-center">
                            <div class="w-28 h-40 rounded-xl flex items-center justify-center border-2 overflow-hidden {{ $selectedRow['data']['cover_found'] ?? false ? 'border-emerald-300' : 'border-dashed border-gray-300' }}">
                                @if($selectedRow['data']['cover_preview_url'] ?? false)
                                    <img src="{{ $selectedRow['data']['cover_preview_url'] }}" alt="Cover" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                        <i class="fas fa-image text-3xl {{ $selectedRow['data']['cover_found'] ?? false ? 'text-emerald-500' : 'text-gray-400' }}"></i>
                                    </div>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500 mt-2">{{ $selectedRow['data']['cover_file'] ?: 'Tidak ada cover' }}</p>
                        </div>
                        <div class="md:col-span-2 space-y-3">
                            <div><p class="text-xs text-gray-500">Judul</p><p class="font-semibold text-gray-900">{{ $selectedRow['data']['title'] ?? '-' }}</p></div>
                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div><p class="text-xs text-gray-500">Penulis</p><p class="text-gray-900">{{ $selectedRow['data']['authors'] ?? '-' }}</p></div>
                                <div><p class="text-xs text-gray-500">ISBN</p><p class="text-gray-900">{{ $selectedRow['data']['isbn'] ?: '-' }}</p></div>
                                <div><p class="text-xs text-gray-500">Penerbit</p><p class="text-gray-900">{{ $selectedRow['data']['publisher'] ?: '-' }}</p></div>
                                <div><p class="text-xs text-gray-500">Tahun</p><p class="text-gray-900">{{ $selectedRow['data']['year'] ?: '-' }}</p></div>
                            </div>
                            <div class="pt-3 border-t border-gray-100 grid grid-cols-2 gap-3 text-sm">
                                <div><p class="text-xs text-gray-500">DDC</p><p class="text-gray-900">{{ $selectedRow['data']['ddc'] ?: '-' }}</p></div>
                                <div><p class="text-xs text-gray-500">Call Number</p><p class="font-mono text-gray-900">{{ $selectedRow['data']['call_number'] ?: '-' }}</p></div>
                                <div><p class="text-xs text-gray-500">Eksemplar</p><p class="text-gray-900">{{ $selectedRow['data']['quantity'] ?? 1 }}</p></div>
                                <div><p class="text-xs text-gray-500">Lokasi</p><p class="text-gray-900">{{ $selectedRow['data']['location'] ?: '-' }}</p></div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 p-4 rounded-xl {{ $selectedRow['status'] === 'error' ? 'bg-red-50' : ($selectedRow['status'] === 'warning' ? 'bg-amber-50' : 'bg-emerald-50') }}">
                        <p class="font-medium text-gray-900 mb-2">Status Validasi</p>
                        @if($selectedRow['status'] === 'valid')<p class="text-emerald-600 text-sm"><i class="fas fa-check-circle mr-1"></i>Semua validasi berhasil</p>@endif
                        @foreach($selectedRow['errors'] ?? [] as $e)<p class="text-red-600 text-sm"><i class="fas fa-times-circle mr-1"></i>{{ $e }}</p>@endforeach
                        @foreach($selectedRow['warnings'] ?? [] as $w)<p class="text-amber-600 text-sm"><i class="fas fa-exclamation-triangle mr-1"></i>{{ $w }}</p>@endforeach
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end">
                    <button wire:click="$set('showDetailModal', false)" class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-xl transition">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    @endteleport
    @endif

    {{-- Success Dialog --}}
    @if($showSuccessModal)
    @teleport('body')
    <div class="fixed inset-0 z-[99999] overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" wire:click="$set('showSuccessModal', false)"></div>
            <div class="relative bg-white rounded-3xl shadow-2xl max-w-md w-full p-8 text-center">
                <div class="w-20 h-20 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg shadow-emerald-500/30">
                    <i class="fas fa-check text-4xl text-white"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Import Berhasil!</h3>
                <p class="text-gray-600 mb-6">
                    <span class="font-bold text-emerald-600">{{ $importedCount }}</span> buku berhasil diimport.
                    @if($skippedCount > 0)
                    <span class="block text-sm text-gray-500 mt-1">{{ $skippedCount }} data dilewati.</span>
                    @endif
                </p>
                <div class="flex gap-3">
                    <button wire:click="$set('showSuccessModal', false)" onclick="window.location.reload()" class="flex-1 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition">
                        Import Lagi
                    </button>
                    <a href="{{ route('staff.biblio.index') }}" class="flex-1 px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-semibold rounded-xl shadow-lg transition">
                        Lihat Katalog
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endteleport
    @endif

    {{-- Error Dialog --}}
    @if($showErrorModal)
    @teleport('body')
    <div class="fixed inset-0 z-[99999] overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" wire:click="$set('showErrorModal', false)"></div>
            <div class="relative bg-white rounded-3xl shadow-2xl max-w-md w-full p-8 text-center">
                <div class="w-20 h-20 bg-gradient-to-br from-red-400 to-rose-500 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg shadow-red-500/30">
                    <i class="fas fa-times text-4xl text-white"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Import Gagal</h3>
                <p class="text-gray-600 mb-6">{{ $errorMessage }}</p>
                <button wire:click="$set('showErrorModal', false)" class="w-full px-6 py-3 bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white font-semibold rounded-xl shadow-lg transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>
    @endteleport
    @endif
</div>
