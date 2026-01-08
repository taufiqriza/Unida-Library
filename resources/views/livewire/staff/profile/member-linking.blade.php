{{-- Member Linking Status & Button --}}
<div x-data="{ showLinkingModal: false }">
@if($linkedMember)
    <div class="space-y-1">
        <p class="text-xs text-emerald-200 font-medium">
            <i class="fas fa-link mr-1"></i>{{ $linkedMember->memberType->name ?? 'Member' }}
        </p>
        <a href="{{ route('auth.switch-portal', 'member') }}" 
           class="inline-flex items-center gap-1 px-2 py-1 bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-200 text-xs font-medium rounded transition">
            <i class="fas fa-exchange-alt"></i>
            <span>Portal</span>
        </a>
    </div>
@else
    <div>
        <p class="text-xs text-blue-200 mb-1">Member</p>
        <button @click="showLinkingModal = true" 
                class="inline-flex items-center gap-1 px-2 py-1 bg-white/15 hover:bg-white/25 text-white text-xs font-medium rounded transition">
            <i class="fas fa-link"></i>
            <span>Hubungkan</span>
        </button>
    </div>
@endif

{{-- Member Linking Modal --}}
<template x-teleport="body">
    <div x-show="showLinkingModal" 
         x-cloak
         @keydown.escape.window="showLinkingModal = false"
         class="fixed inset-0 z-[99999] flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showLinkingModal = false"></div>
        
        {{-- Modal Content --}}
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             @click.stop>
            
            {{-- Icon Header --}}
            <div class="pt-8 pb-4 flex justify-center">
                <div class="w-20 h-20 rounded-full bg-gradient-to-br from-emerald-100 to-emerald-50 flex items-center justify-center">
                    <div class="w-14 h-14 rounded-full bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center shadow-lg shadow-emerald-500/30">
                        <i class="fas fa-link text-white text-2xl"></i>
                    </div>
                </div>
            </div>
            
            {{-- Content --}}
            <div class="px-6 pb-6">
                <div class="text-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Hubungkan dengan Data Member</h3>
                    <p class="text-gray-500 text-sm">
                        Sambungkan akun staff dengan data mahasiswa/dosen untuk akses Member Portal
                    </p>
                </div>

                {{-- Search Section --}}
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-search mr-2 text-emerald-500"></i>Cari Data Member
                        </label>
                        <div class="flex gap-2">
                            <input type="text" 
                                   wire:model="searchName" 
                                   wire:keydown.enter="searchPddikti"
                                   placeholder="Masukkan NIM, NIDN, atau Nama (min. 2 karakter)..."
                                   class="flex-1 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                            <button type="button" 
                                    wire:click="testMethod" 
                                    wire:loading.attr="disabled"
                                    class="px-5 py-3 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition disabled:opacity-50">
                                <span wire:loading.remove wire:target="testMethod"><i class="fas fa-search"></i></span>
                                <span wire:loading wire:target="testMethod"><i class="fas fa-spinner fa-spin"></i></span>
                            </button>
                        </div>
                    </div>

                    {{-- Loading State --}}
                    @if($isSearching)
                        <div class="text-center py-8">
                            <i class="fas fa-spinner fa-spin text-emerald-600 text-2xl mb-3"></i>
                            <p class="text-sm text-gray-500">Mencari data member...</p>
                        </div>
                    @endif

                    {{-- Search Results --}}
                    @if(!empty($searchResults) && !$isSearching)
                        <div class="max-h-96 overflow-y-auto space-y-3">
                            <p class="text-sm font-medium text-gray-700 sticky top-0 bg-white py-2">
                                Hasil Pencarian ({{ count($searchResults) }} ditemukan):
                            </p>
                            @foreach($searchResults as $result)
                                <div class="border border-gray-200 rounded-xl p-4 hover:bg-gray-50 transition">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-2">
                                                <h4 class="font-medium text-gray-900">{{ $result['name'] }}</h4>
                                                <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs rounded-full">
                                                    {{ $result['member_type'] }}
                                                </span>
                                                @if($result['match_score'] >= 90)
                                                    <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs rounded-full">
                                                        <i class="fas fa-star mr-1"></i>{{ $result['match_score'] }}%
                                                    </span>
                                                @elseif($result['match_score'] >= 70)
                                                    <span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 text-xs rounded-full">
                                                        {{ $result['match_score'] }}%
                                                    </span>
                                                @else
                                                    <span class="px-2 py-0.5 bg-gray-100 text-gray-700 text-xs rounded-full">
                                                        {{ $result['match_score'] }}%
                                                    </span>
                                                @endif
                                            </div>
                                            
                                            <div class="grid grid-cols-2 gap-4 text-sm text-gray-600">
                                                @if($result['member_id'] && $result['member_id'] !== '-')
                                                    <div>
                                                        <i class="fas fa-id-card mr-1"></i>
                                                        ID: {{ $result['member_id'] }}
                                                    </div>
                                                @endif
                                                
                                                @if($result['nim_nidn'] && $result['nim_nidn'] !== '-')
                                                    <div>
                                                        <i class="fas fa-graduation-cap mr-1"></i>
                                                        {{ $result['type'] === 'employee' ? 'NIDN/NIY' : 'NIM' }}: {{ $result['nim_nidn'] }}
                                                    </div>
                                                @endif
                                                
                                                @if($result['faculty'] !== '-')
                                                    <div>
                                                        <i class="fas fa-university mr-1"></i>
                                                        {{ $result['faculty'] }}
                                                    </div>
                                                @endif
                                                
                                                @if($result['department'] !== '-')
                                                    <div>
                                                        <i class="fas fa-building mr-1"></i>
                                                        {{ $result['department'] }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <button wire:click="linkMember({{ $result['id'] }}, '{{ $result['type'] }}')"
                                                wire:confirm="Yakin ingin menghubungkan akun dengan {{ $result['name'] }}?"
                                                class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition">
                                            <i class="fas fa-link mr-1"></i>
                                            Hubungkan
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @elseif(strlen($searchName) >= 2 && !$isSearching && empty($searchResults))
                        <div class="text-center py-8">
                            <i class="fas fa-search text-gray-300 text-3xl mb-3"></i>
                            <p class="text-gray-500">Tidak ada data member yang ditemukan</p>
                            <p class="text-sm text-gray-400 mt-1">Coba gunakan kata kunci yang berbeda</p>
                        </div>
                    @elseif(strlen($searchName) < 2)
                        <div class="text-center py-8">
                            <i class="fas fa-info-circle text-blue-500 text-3xl mb-3"></i>
                            <p class="text-gray-600">Ketik minimal 2 karakter untuk mulai pencarian</p>
                            <p class="text-sm text-gray-500 mt-1">Cari berdasarkan nama, NIM/NIDN, atau ID member</p>
                        </div>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                    <button @click="showLinkingModal = false" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

</div> {{-- Close x-data div --}}

