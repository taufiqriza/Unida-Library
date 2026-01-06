{{-- Modules Tab --}}
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h3 class="font-bold text-gray-900">Modul & Materi</h3>
        <button wire:click="openModuleModal()" class="px-4 py-2 bg-violet-600 text-white rounded-xl font-semibold hover:bg-violet-700 transition">
            <i class="fas fa-plus mr-1"></i> Tambah Modul
        </button>
    </div>

    @if($course->modules->count() > 0)
    <div class="space-y-4">
        @foreach($course->modules as $index => $module)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden" x-data="{ open: true }">
            {{-- Module Header --}}
            <div class="p-4 bg-gray-50 flex items-center justify-between cursor-pointer" @click="open = !open">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-violet-100 rounded-xl flex items-center justify-center text-violet-600 font-bold">{{ $index + 1 }}</div>
                    <div>
                        <h4 class="font-bold text-gray-900">{{ $module->title }}</h4>
                        <p class="text-sm text-gray-500">{{ $module->materials->count() }} materi</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button wire:click.stop="openModuleModal({{ $module->id }})" class="p-2 text-gray-400 hover:text-violet-600 hover:bg-violet-50 rounded-lg transition">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button wire:click.stop="deleteModule({{ $module->id }})" wire:confirm="Hapus modul ini beserta semua materinya?" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition">
                        <i class="fas fa-trash"></i>
                    </button>
                    <i class="fas fa-chevron-down text-gray-400 transition-transform" :class="{ 'rotate-180': open }"></i>
                </div>
            </div>

            {{-- Materials --}}
            <div x-show="open" x-collapse>
                <div class="p-4 border-t border-gray-100">
                    @if($module->materials->count() > 0)
                    <div class="space-y-2">
                        @foreach($module->materials as $material)
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition group">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center
                                {{ $material->type === 'video' ? 'bg-red-100 text-red-600' : '' }}
                                {{ $material->type === 'document' ? 'bg-blue-100 text-blue-600' : '' }}
                                {{ $material->type === 'text' ? 'bg-green-100 text-green-600' : '' }}
                                {{ $material->type === 'link' ? 'bg-amber-100 text-amber-600' : '' }}
                                {{ $material->type === 'quiz' ? 'bg-purple-100 text-purple-600' : '' }}">
                                <i class="fas {{ $material->type === 'video' ? 'fa-play' : ($material->type === 'document' ? 'fa-file-pdf' : ($material->type === 'text' ? 'fa-file-alt' : ($material->type === 'link' ? 'fa-link' : 'fa-question-circle'))) }} text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">{{ $material->title }}</p>
                                <p class="text-xs text-gray-500">{{ ucfirst($material->type) }} @if($material->duration_minutes)• {{ $material->duration_minutes }} menit @endif</p>
                            </div>
                            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition">
                                <button wire:click="openMaterialModal({{ $module->id }}, {{ $material->id }})" class="p-2 text-gray-400 hover:text-violet-600 hover:bg-violet-50 rounded-lg">
                                    <i class="fas fa-edit text-sm"></i>
                                </button>
                                <button wire:click="deleteMaterial({{ $material->id }})" wire:confirm="Hapus materi ini?" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                    <button wire:click="openMaterialModal({{ $module->id }})" class="mt-3 w-full py-2 border-2 border-dashed border-gray-200 rounded-xl text-gray-500 hover:border-violet-300 hover:text-violet-600 transition">
                        <i class="fas fa-plus mr-1"></i> Tambah Materi
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="bg-white rounded-2xl p-12 text-center shadow-sm border border-gray-100">
        <div class="w-16 h-16 bg-violet-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-layer-group text-violet-400 text-2xl"></i>
        </div>
        <h3 class="font-bold text-gray-900 mb-2">Belum Ada Modul</h3>
        <p class="text-gray-500 mb-4">Tambahkan modul untuk mengorganisir materi pembelajaran</p>
        <button wire:click="openModuleModal()" class="px-5 py-2.5 bg-violet-600 text-white rounded-xl font-semibold hover:bg-violet-700 transition">
            <i class="fas fa-plus mr-1"></i> Tambah Modul Pertama
        </button>
    </div>
    @endif
</div>
