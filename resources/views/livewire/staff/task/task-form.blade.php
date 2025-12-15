@section('title', $task ? 'Edit Tugas' : 'Buat Tugas Baru')

<div class="space-y-5">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('staff.task.index') }}" 
               class="w-10 h-10 bg-gray-100 hover:bg-gray-200 rounded-xl flex items-center justify-center transition">
                <i class="fas fa-arrow-left text-gray-600"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">
                    {{ $task ? 'Edit Tugas' : 'Buat Tugas Baru' }}
                </h1>
                <p class="text-sm text-gray-500">
                    {{ $task ? '#' . $task->id . ' - ' . Str::limit($task->title, 30) : 'Tambahkan tugas baru untuk tim' }}
                </p>
            </div>
        </div>

        <button wire:click="save" 
                wire:loading.attr="disabled"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-700 hover:to-purple-700 text-white font-medium rounded-xl shadow-lg shadow-purple-500/25 transition disabled:opacity-50">
            <span wire:loading.remove wire:target="save"><i class="fas fa-save mr-1"></i> Simpan</span>
            <span wire:loading wire:target="save"><i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...</span>
        </button>
    </div>

    {{-- Form --}}
    <form wire:submit="save" class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-5">
            {{-- Detail Tugas --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <div class="w-8 h-8 bg-violet-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clipboard-list text-violet-600 text-sm"></i>
                    </div>
                    Detail Tugas
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Judul Tugas <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="title" 
                               placeholder="Masukkan judul tugas..."
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500">
                        @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                        <textarea wire:model="description" rows="4"
                                  placeholder="Jelaskan detail tugas..."
                                  class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                            <select wire:model="type" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500">
                                <option value="general">Tugas Umum</option>
                                <option value="collection">Pengembangan Koleksi</option>
                                <option value="service">Pelayanan</option>
                                <option value="admin">Administrasi</option>
                                <option value="event">Kegiatan/Event</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Prioritas</label>
                            <select wire:model="priority" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500">
                                <option value="low">Rendah</option>
                                <option value="medium">Sedang</option>
                                <option value="high">Tinggi</option>
                                <option value="urgent">Mendesak</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Jadwal --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar text-blue-600 text-sm"></i>
                    </div>
                    Jadwal
                </h3>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                        <input type="date" wire:model="start_date" 
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tenggat Waktu</label>
                        <input type="date" wire:model="due_date" 
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500">
                        @error('due_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estimasi Waktu (jam)</label>
                        <input type="number" wire:model="estimated_hours" min="0" step="0.5"
                               placeholder="0"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Label/Tags</label>
                        <input type="text" wire:model="tags" 
                               placeholder="label1, label2, label3"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500">
                        <p class="text-xs text-gray-400 mt-1">Pisahkan dengan koma</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-5">
            {{-- Status & Penugasan --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-check text-emerald-600 text-sm"></i>
                    </div>
                    Status & Penugasan
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                        <select wire:model="status_id" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500">
                            @foreach($statuses as $status)
                                <option value="{{ $status->id }}">{{ $status->name }}</option>
                            @endforeach
                        </select>
                        @error('status_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">PIC (Penanggung Jawab)</label>
                        <select wire:model="assigned_to" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500">
                            <option value="">Belum ditugaskan</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Proyek & Divisi --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-folder text-amber-600 text-sm"></i>
                    </div>
                    Proyek & Divisi
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Proyek</label>
                        <select wire:model.live="project_id" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500">
                            <option value="">Tidak ada proyek</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Divisi</label>
                        <select wire:model="division_id" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500">
                            <option value="">Tidak ada divisi</option>
                            @foreach($divisions as $division)
                                <option value="{{ $division->id }}">{{ $division->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Preview Card --}}
            <div class="bg-gradient-to-br from-violet-50 to-purple-50 rounded-xl border border-violet-100 p-4">
                <p class="text-xs text-violet-600 font-medium mb-2"><i class="fas fa-eye mr-1"></i>Preview</p>
                <div class="bg-white rounded-lg p-3 shadow-sm border border-violet-200">
                    <div class="flex items-center justify-between mb-2">
                        @php
                            $categoryStyles = [
                                'collection' => ['bg-emerald-100 text-emerald-700', 'Koleksi'],
                                'service' => ['bg-blue-100 text-blue-700', 'Pelayanan'],
                                'admin' => ['bg-amber-100 text-amber-700', 'Administrasi'],
                                'event' => ['bg-pink-100 text-pink-700', 'Kegiatan'],
                            ];
                            $cat = $categoryStyles[$type] ?? ['bg-gray-100 text-gray-600', 'Umum'];
                            
                            $priorityStyles = [
                                'urgent' => ['bg-red-100 text-red-700', 'Mendesak'],
                                'high' => ['bg-orange-100 text-orange-700', 'Tinggi'],
                                'medium' => ['bg-yellow-100 text-yellow-700', 'Sedang'],
                                'low' => ['bg-green-100 text-green-700', 'Rendah'],
                            ];
                            $pri = $priorityStyles[$priority] ?? $priorityStyles['medium'];
                        @endphp
                        <span class="text-[10px] px-2 py-0.5 rounded font-semibold {{ $cat[0] }}">{{ $cat[1] }}</span>
                        <span class="text-[10px] px-2 py-0.5 rounded font-semibold {{ $pri[0] }}">{{ $pri[1] }}</span>
                    </div>
                    <p class="font-medium text-gray-900 text-sm line-clamp-2">{{ $title ?: 'Judul tugas...' }}</p>
                </div>
            </div>
        </div>
    </form>
</div>
