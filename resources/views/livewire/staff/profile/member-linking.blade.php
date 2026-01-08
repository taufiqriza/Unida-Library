<div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
    <div class="p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-link text-emerald-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Hubungkan dengan Data Member</h3>
                    <p class="text-sm text-gray-500">Sambungkan akun staff dengan data mahasiswa/dosen</p>
                </div>
            </div>
        </div>

        @if($linkedMember)
            {{-- Already Linked --}}
            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <i class="fas fa-check-circle text-emerald-600"></i>
                            <span class="text-sm font-medium text-emerald-800">Akun Terhubung</span>
                        </div>
                        
                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-emerald-700 font-medium">{{ $linkedMember->name }}</span>
                                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 text-xs rounded-full">
                                    {{ $linkedMember->memberType->name ?? 'Member' }}
                                </span>
                            </div>
                            
                            @if($linkedMember->member_id)
                                <p class="text-sm text-emerald-600">
                                    <i class="fas fa-id-card mr-1"></i>
                                    {{ $linkedMember->member_id }}
                                </p>
                            @endif
                            
                            @if($linkedMember->nim_nidn)
                                <p class="text-sm text-emerald-600">
                                    <i class="fas fa-graduation-cap mr-1"></i>
                                    {{ $linkedMember->nim_nidn }}
                                </p>
                            @endif
                            
                            @if($linkedMember->faculty)
                                <p class="text-sm text-emerald-600">
                                    <i class="fas fa-university mr-1"></i>
                                    {{ $linkedMember->faculty->name }}
                                </p>
                            @endif
                        </div>
                    </div>
                    
                    <button wire:click="unlinkMember" 
                            wire:confirm="Yakin ingin memutus hubungan dengan data member?"
                            class="px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 text-sm rounded-lg transition">
                        <i class="fas fa-unlink mr-1"></i>
                        Putus Hubungan
                    </button>
                </div>
                
                <div class="mt-4 pt-4 border-t border-emerald-200">
                    <p class="text-sm text-emerald-700 mb-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Anda dapat mengakses Member Portal dengan akun ini
                    </p>
                    <a href="{{ route('auth.switch-portal', 'member') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition">
                        <i class="fas fa-exchange-alt"></i>
                        Beralih ke Member Portal
                    </a>
                </div>
            </div>
        @else
            {{-- Not Linked --}}
            <div class="space-y-4">
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-info-circle text-blue-600 mt-0.5"></i>
                        <div class="flex-1">
                            <h4 class="text-sm font-medium text-blue-800 mb-1">Mengapa perlu menghubungkan akun?</h4>
                            <ul class="text-sm text-blue-700 space-y-1">
                                <li>• Akses Member Portal untuk layanan mahasiswa/dosen</li>
                                <li>• Peminjaman buku dan akses koleksi digital</li>
                                <li>• Riwayat akademik dan layanan perpustakaan</li>
                                <li>• Satu akun untuk dua portal (Staff & Member)</li>
                            </ul>
                        </div>
                    </div>
                </div>

                @if(!$showLinkingSection)
                    <button wire:click="toggleLinkingSection" 
                            class="w-full px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl transition flex items-center justify-center gap-2">
                        <i class="fas fa-search"></i>
                        Cari & Hubungkan Data Member
                    </button>
                @else
                    {{-- Search Section --}}
                    <div class="space-y-4">
                        <div class="flex gap-2">
                            <div class="flex-1">
                                <input type="text" 
                                       wire:model.live.debounce.500ms="searchQuery"
                                       placeholder="Cari berdasarkan nama, NIM/NIDN, atau ID member..."
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                            </div>
                            <button wire:click="toggleLinkingSection" 
                                    class="px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl transition">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        @if($isSearching)
                            <div class="text-center py-4">
                                <i class="fas fa-spinner fa-spin text-emerald-600 text-xl"></i>
                                <p class="text-sm text-gray-500 mt-2">Mencari data member...</p>
                            </div>
                        @endif

                        @if(!empty($searchResults))
                            <div class="space-y-2">
                                <p class="text-sm font-medium text-gray-700">Hasil Pencarian:</p>
                                @foreach($searchResults as $member)
                                    <div class="border border-gray-200 rounded-xl p-4 hover:bg-gray-50 transition">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2 mb-2">
                                                    <h4 class="font-medium text-gray-900">{{ $member['name'] }}</h4>
                                                    <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs rounded-full">
                                                        {{ $member['member_type'] }}
                                                    </span>
                                                </div>
                                                
                                                <div class="grid grid-cols-2 gap-4 text-sm text-gray-600">
                                                    @if($member['member_id'])
                                                        <div>
                                                            <i class="fas fa-id-card mr-1"></i>
                                                            ID: {{ $member['member_id'] }}
                                                        </div>
                                                    @endif
                                                    
                                                    @if($member['nim_nidn'])
                                                        <div>
                                                            <i class="fas fa-graduation-cap mr-1"></i>
                                                            {{ $member['nim_nidn'] }}
                                                        </div>
                                                    @endif
                                                    
                                                    @if($member['faculty'] !== '-')
                                                        <div>
                                                            <i class="fas fa-university mr-1"></i>
                                                            {{ $member['faculty'] }}
                                                        </div>
                                                    @endif
                                                    
                                                    @if($member['department'] !== '-')
                                                        <div>
                                                            <i class="fas fa-building mr-1"></i>
                                                            {{ $member['department'] }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            <button wire:click="linkMember({{ $member['id'] }})"
                                                    wire:confirm="Yakin ingin menghubungkan akun dengan {{ $member['name'] }}?"
                                                    class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition">
                                                <i class="fas fa-link mr-1"></i>
                                                Hubungkan
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @elseif(strlen($searchQuery) >= 3 && !$isSearching)
                            <div class="text-center py-8">
                                <i class="fas fa-search text-gray-300 text-3xl mb-3"></i>
                                <p class="text-gray-500">Tidak ada data member yang ditemukan</p>
                                <p class="text-sm text-gray-400 mt-1">Coba gunakan kata kunci yang berbeda</p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
