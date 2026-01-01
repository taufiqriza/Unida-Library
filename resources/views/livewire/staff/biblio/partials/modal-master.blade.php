{{-- Master Data CRUD Modal --}}
@php
$tabLabels = [
    'authors' => ['label' => 'Penulis', 'gradient' => 'from-purple-500 to-violet-600', 'icon' => 'fa-user-edit'],
    'publishers' => ['label' => 'Penerbit', 'gradient' => 'from-indigo-500 to-blue-600', 'icon' => 'fa-building'], 
    'subjects' => ['label' => 'Subjek', 'gradient' => 'from-amber-500 to-orange-600', 'icon' => 'fa-tags'],
    'locations' => ['label' => 'Lokasi Rak', 'gradient' => 'from-rose-500 to-pink-600', 'icon' => 'fa-map-marker-alt'],
    'places' => ['label' => 'Kota Terbit', 'gradient' => 'from-cyan-500 to-teal-600', 'icon' => 'fa-city'],
    'gmd' => ['label' => 'Jenis Media', 'gradient' => 'from-violet-500 to-purple-600', 'icon' => 'fa-compact-disc'],
];
$config = $tabLabels[$activeTab] ?? ['label' => 'Data', 'gradient' => 'from-gray-500 to-gray-600', 'icon' => 'fa-database'];
@endphp

<template x-teleport="body">
    <div x-data="{ show: @entangle('showModal').live }" x-show="show" x-cloak style="position: fixed; inset: 0; z-index: 99999;" @keydown.escape.window="$wire.closeModal()">
        <div x-show="show" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" style="position: fixed; inset: 0; background: rgba(0,0,0,0.6);" @click="$wire.closeModal()"></div>
        <div style="position: fixed; inset: 0; display: flex; align-items: center; justify-content: center; padding: 1rem; pointer-events: none;">
            <div x-show="show" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden" style="pointer-events: auto;">
                {{-- Header --}}
                <div class="p-5 bg-gradient-to-r {{ $config['gradient'] }} text-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                                <i class="fas {{ $config['icon'] }} text-lg"></i>
                            </div>
                            <h3 class="text-lg font-bold">{{ $modalMode === 'edit' ? 'Edit' : 'Tambah' }} {{ $config['label'] }}</h3>
                        </div>
                        <button @click="$wire.closeModal()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white/20 transition">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                
                {{-- Form --}}
                <form wire:submit="saveModal" class="p-5 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama {{ $config['label'] }}</label>
                        <input type="text" wire:model="formName" class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" placeholder="Masukkan nama..." autofocus>
                        @error('formName') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    @if($activeTab === 'locations' && $isSuperAdmin)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Cabang Perpustakaan</label>
                        <select wire:model="formLocationBranch" class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1.5 flex items-center gap-1"><i class="fas fa-info-circle"></i> Lokasi rak spesifik per cabang</p>
                    </div>
                    @endif
                </form>
                
                {{-- Footer --}}
                <div class="p-5 border-t border-gray-100 bg-gray-50 flex items-center justify-end gap-3">
                    <button wire:click="closeModal" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition">
                        Batal
                    </button>
                    <button wire:click="saveModal" class="px-5 py-2.5 bg-gradient-to-r {{ $config['gradient'] }} hover:shadow-lg text-white font-medium rounded-xl transition shadow-md">
                        <i class="fas fa-check mr-1.5"></i> Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
