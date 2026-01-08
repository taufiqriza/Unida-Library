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
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95">
        
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showLinkingModal = false"></div>
        
        {{-- Modal Content --}}
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden">
            {{-- Header --}}
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Hubungkan Akun Member</h3>
                    <p class="text-sm text-gray-500 mt-1">Cari dan hubungkan data mahasiswa atau dosen/tendik</p>
                </div>
                <button @click="showLinkingModal = false" 
                        class="p-2 hover:bg-gray-100 rounded-lg transition">
                    <i class="fas fa-times text-gray-400"></i>
                </button>
            </div>

            {{-- Content --}}
            <div class="p-6 max-h-[70vh] overflow-y-auto">
                {{-- Flash Messages --}}
                @if (session()->has('success'))
                    <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                        <p class="text-sm text-green-700">{{ session('success') }}</p>
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-sm text-red-700">{{ session('error') }}</p>
                    </div>
                @endif

                {{-- Search Form --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Cari Data Member
                    </label>
                    <div class="flex gap-3">
                        <input type="text" 
                               wire:model="searchName" 
                               wire:keydown.enter="searchPddikti"
                               placeholder="Masukkan NIM, NIDN, atau Nama (min. 2 karakter)..."
                               class="flex-1 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                        <button type="button" 
                                wire:click="searchPddikti" 
                                wire:loading.attr="disabled"
                                class="px-5 py-3 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition disabled:opacity-50">
                            <span wire:loading.remove wire:target="searchPddikti"><i class="fas fa-search"></i></span>
                            <span wire:loading wire:target="searchPddikti"><i class="fas fa-spinner fa-spin"></i></span>
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

                {{-- Mahasiswa Results --}}
                @if(count($searchResults) > 0)
                <div class="border border-gray-200 rounded-xl overflow-hidden mb-4">
                    <div class="bg-blue-50 px-4 py-2 border-b border-gray-200">
                        <p class="text-sm font-medium text-blue-700">
                            <i class="fas fa-user-graduate mr-1"></i> Mahasiswa ({{ count($searchResults) }})
                        </p>
                    </div>
                    <div class="max-h-40 overflow-y-auto">
                        @foreach($searchResults as $result)
                        @php
                            $score = $result->_matchScore ?? 0;
                            $colorClass = $score >= 90 ? 'bg-green-100 text-green-700 border-green-300' : 
                                         ($score >= 70 ? 'bg-blue-100 text-blue-700 border-blue-300' : 
                                         ($score >= 50 ? 'bg-cyan-100 text-cyan-700 border-cyan-300' : 'bg-amber-100 text-amber-700 border-amber-300'));
                        @endphp
                        <div class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0 transition"
                             wire:click="linkMember({{ $result->id }}, 'member')">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $result->name }}</p>
                                    <span class="text-[10px] px-1.5 py-0.5 rounded border font-medium {{ $colorClass }}">{{ $score }}%</span>
                                </div>
                                <p class="text-xs text-gray-500">{{ $result->member_id ?? '-' }}@if($result->department) · {{ $result->department->code ?? $result->department->name }}@endif</p>
                            </div>
                            <i class="fas fa-link text-emerald-600"></i>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Employee Results --}}
                @if(isset($employeeResults) && count($employeeResults) > 0)
                <div class="border border-gray-200 rounded-xl overflow-hidden mb-4">
                    <div class="bg-green-50 px-4 py-2 border-b border-gray-200">
                        <p class="text-sm font-medium text-green-700">
                            <i class="fas fa-chalkboard-teacher mr-1"></i> Dosen / Tendik ({{ count($employeeResults) }})
                        </p>
                    </div>
                    <div class="max-h-40 overflow-y-auto">
                        @foreach($employeeResults as $emp)
                        @php
                            $score = $emp->_matchScore ?? 0;
                            $colorClass = $score >= 90 ? 'bg-green-100 text-green-700 border-green-300' : 
                                         ($score >= 70 ? 'bg-blue-100 text-blue-700 border-blue-300' : 
                                         ($score >= 50 ? 'bg-cyan-100 text-cyan-700 border-cyan-300' : 'bg-amber-100 text-amber-700 border-amber-300'));
                        @endphp
                        <div class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0 transition"
                             wire:click="linkMember({{ $emp->id }}, 'employee')">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $emp->full_name ?? $emp->name }}</p>
                                    <span class="text-[10px] px-1.5 py-0.5 rounded border font-medium {{ $colorClass }}">{{ $score }}%</span>
                                </div>
                                <p class="text-xs text-gray-500">NIY: {{ $emp->niy ?? '-' }}@if($emp->nidn) · NIDN: {{ $emp->nidn }}@endif</p>
                            </div>
                            <i class="fas fa-link text-emerald-600"></i>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- No Results --}}
                @if(count($searchResults) === 0 && (!isset($employeeResults) || count($employeeResults) === 0) && strlen($searchName) >= 2 && !$isSearching)
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-center">
                    <p class="text-sm text-amber-700 font-medium"><i class="fas fa-exclamation-triangle mr-1"></i> Data tidak ditemukan</p>
                    <p class="text-xs text-amber-600 mt-2">Coba gunakan NIM/NIY/NIDN untuk hasil lebih akurat</p>
                </div>
                @endif
            </div>

            {{-- Footer --}}
            <div class="flex justify-end gap-3 p-6 border-t border-gray-200 bg-gray-50">
                <button @click="showLinkingModal = false" 
                        class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</template>

</div> {{-- Close x-data div --}}
