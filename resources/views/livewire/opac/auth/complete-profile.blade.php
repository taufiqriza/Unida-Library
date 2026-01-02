<div>
    <div class="min-h-[70vh] flex items-center justify-center py-8 px-4">
        <div class="w-full max-w-xl lg:max-w-2xl">
            <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 overflow-hidden">
                {{-- Header --}}
                <div class="bg-gradient-to-r from-primary-600 to-primary-800 px-6 py-5 flex items-center gap-4">
                    <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas {{ $step === 1 ? 'fa-search' : 'fa-user-edit' }} text-2xl text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-white">
                            {{ $step === 1 ? 'Cari Data Anda' : __('opac.auth.complete_profile.title') }}
                        </h1>
                        <p class="text-primary-200">
                            {{ $step === 1 ? 'Temukan data Anda di sistem SIAKAD / SDM' : __('opac.auth.complete_profile.subtitle') }}
                        </p>
                    </div>
                </div>

                <div class="p-6 lg:p-8">
                    {{-- Step Indicator --}}
                    <div class="flex items-center justify-center gap-4 mb-6">
                        <div class="flex items-center gap-2">
                            <div class="w-9 h-9 rounded-full {{ $step >= 1 ? 'bg-primary-600 text-white' : 'bg-gray-200 text-gray-500' }} flex items-center justify-center font-bold">1</div>
                            <span class="font-medium {{ $step >= 1 ? 'text-primary-600' : 'text-gray-400' }}">Cari Data</span>
                        </div>
                        <div class="w-12 h-0.5 {{ $step >= 2 ? 'bg-primary-600' : 'bg-gray-200' }}"></div>
                        <div class="flex items-center gap-2">
                            <div class="w-9 h-9 rounded-full {{ $step >= 2 ? 'bg-primary-600 text-white' : 'bg-gray-200 text-gray-500' }} flex items-center justify-center font-bold">2</div>
                            <span class="font-medium {{ $step >= 2 ? 'text-primary-600' : 'text-gray-400' }}">Lengkapi Profil</span>
                        </div>
                    </div>

                    {{-- Logged in user info --}}
                    <div class="bg-gray-50 rounded-xl p-4 mb-5 flex items-center gap-3">
                        <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm">
                            <svg class="w-5 h-5" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-700">{{ $member->name }}</p>
                            <p class="text-xs text-gray-400">{{ $member->email }}</p>
                        </div>
                    </div>

                    @if($step === 1)
                    
                    @if($autoDetected && $selectedPddikti)
                    {{-- AUTO-DETECTED: Quick Confirm View --}}
                    <div class="space-y-4">
                        @if($selectedEmployee)
                        {{-- Auto-detected Employee (Dosen/Tendik) --}}
                        <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                            <div class="flex items-center gap-2 mb-3">
                                <i class="fas fa-link text-green-600"></i>
                                <span class="font-semibold text-green-700">Data {{ $entryMode === 'dosen' ? 'Dosen' : 'Tendik' }} Terdeteksi!</span>
                            </div>
                            <div class="bg-white rounded-lg p-4 border border-green-100">
                                <p class="font-bold text-gray-800 text-lg">{{ $selectedEmployee->full_name ?? $selectedEmployee->name }}</p>
                                <div class="mt-2 space-y-1 text-sm text-gray-600">
                                    <p><i class="fas fa-id-badge w-5 text-gray-400"></i> NIY: {{ $selectedEmployee->niy ?? '-' }}</p>
                                    @if($selectedEmployee->nidn)<p><i class="fas fa-id-card w-5 text-gray-400"></i> NIDN: {{ $selectedEmployee->nidn }}</p>@endif
                                    @if($selectedEmployee->faculty)<p><i class="fas fa-university w-5 text-gray-400"></i> {{ $selectedEmployee->faculty }}</p>@endif
                                    @if($selectedEmployee->prodi)<p><i class="fas fa-graduation-cap w-5 text-gray-400"></i> {{ $selectedEmployee->prodi }}</p>@endif
                                </div>
                            </div>
                        </div>
                        
                        <button type="button" wire:click="quickConfirmEmployee" wire:loading.attr="disabled"
                            class="w-full py-3.5 bg-gradient-to-r from-green-500 to-green-600 text-white font-bold rounded-xl shadow-lg shadow-green-500/30 hover:shadow-xl transition flex items-center justify-center gap-2">
                            <span wire:loading.remove wire:target="quickConfirmEmployee"><i class="fas fa-check-circle"></i> Ya, Ini Data Saya</span>
                            <span wire:loading wire:target="quickConfirmEmployee"><i class="fas fa-spinner fa-spin"></i> Memproses...</span>
                        </button>
                        
                        @elseif($selectedPddikti)
                        {{-- Auto-detected Mahasiswa --}}
                        <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                            <div class="flex items-center gap-2 mb-3">
                                <i class="fas fa-link text-green-600"></i>
                                <span class="font-semibold text-green-700">Data Mahasiswa Terdeteksi!</span>
                            </div>
                            <div class="bg-white rounded-lg p-4 border border-green-100">
                                <p class="font-bold text-gray-800 text-lg">{{ $selectedPddikti->name }}</p>
                                <div class="mt-2 space-y-1 text-sm text-gray-600">
                                    <p><i class="fas fa-id-card w-5 text-gray-400"></i> {{ $selectedPddikti->member_id }}</p>
                                    @if($selectedPddikti->department)
                                    <p><i class="fas fa-graduation-cap w-5 text-gray-400"></i> {{ $selectedPddikti->department->name }}</p>
                                    @endif
                                    @if($selectedPddikti->branch)
                                    <p><i class="fas fa-building w-5 text-gray-400"></i> {{ $selectedPddikti->branch->name }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <button type="button" wire:click="quickConfirm" wire:loading.attr="disabled"
                            class="w-full py-3.5 bg-gradient-to-r from-green-500 to-green-600 text-white font-bold rounded-xl shadow-lg shadow-green-500/30 hover:shadow-xl transition flex items-center justify-center gap-2">
                            <span wire:loading.remove wire:target="quickConfirm"><i class="fas fa-check-circle"></i> Ya, Ini Data Saya</span>
                            <span wire:loading wire:target="quickConfirm"><i class="fas fa-spinner fa-spin"></i> Memproses...</span>
                        </button>
                        @endif
                        
                        <button type="button" wire:click="$set('autoDetected', false)" 
                            class="w-full py-2.5 text-gray-500 text-sm hover:text-gray-700 transition">
                            <i class="fas fa-search mr-1"></i> Bukan data saya, cari manual
                        </button>
                    </div>
                    
                    @else
                    {{-- STEP 1: Unified Search (Mahasiswa + Dosen + Tendik) --}}
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fas fa-search mr-1 text-primary-500"></i> Cari Data Anda
                            </label>
                            <div class="flex gap-2">
                                <input type="text" wire:model="searchName" wire:keydown.enter="searchPddikti"
                                    placeholder="Masukkan NIM, NIY, NIDN, atau Nama..."
                                    class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500">
                                <button type="button" wire:click="searchPddikti" wire:loading.attr="disabled"
                                    class="px-5 py-2.5 bg-primary-600 text-white rounded-xl hover:bg-primary-700 transition disabled:opacity-50">
                                    <span wire:loading.remove wire:target="searchPddikti"><i class="fas fa-search"></i></span>
                                    <span wire:loading wire:target="searchPddikti"><i class="fas fa-spinner fa-spin"></i></span>
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-1.5">
                                <i class="fas fa-lightbulb text-amber-500 mr-1"></i>
                                Cari mahasiswa, dosen, atau tendik sekaligus
                            </p>
                        </div>

                        {{-- Mahasiswa Results --}}
                        @if(count($searchResults) > 0)
                        <div class="border border-gray-200 rounded-xl overflow-hidden">
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
                                <label class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0 transition {{ $selectedPddiktiId == $result->id ? 'bg-primary-50 border-l-4 border-l-primary-500' : '' }}">
                                    <input type="radio" wire:click="selectPddikti({{ $result->id }})" name="data_selection" value="m_{{ $result->id }}"
                                        {{ $selectedPddiktiId == $result->id ? 'checked' : '' }} class="w-4 h-4 text-primary-600">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2">
                                            <p class="text-sm font-medium text-gray-900 truncate">{{ $result->name }}</p>
                                            <span class="text-[10px] px-1.5 py-0.5 rounded border font-medium {{ $colorClass }}">{{ $score }}%</span>
                                        </div>
                                        <p class="text-xs text-gray-500">{{ $result->member_id ?? '-' }}@if($result->department) · {{ $result->department->code ?? $result->department->name }}@endif</p>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        {{-- Dosen/Tendik Results --}}
                        @if(isset($employeeResults) && count($employeeResults) > 0)
                        <div class="border border-gray-200 rounded-xl overflow-hidden">
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
                                    $typeLabel = $emp->type === 'dosen' ? 'Dosen' : 'Tendik';
                                @endphp
                                <label class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0 transition {{ $selectedEmployee && $selectedEmployee->id == $emp->id ? 'bg-primary-50 border-l-4 border-l-primary-500' : '' }}">
                                    <input type="radio" wire:click="selectEmployee({{ $emp->id }})" name="data_selection" value="e_{{ $emp->id }}"
                                        {{ $selectedEmployee && $selectedEmployee->id == $emp->id ? 'checked' : '' }} class="w-4 h-4 text-primary-600">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2">
                                            <p class="text-sm font-medium text-gray-900 truncate">{{ $emp->full_name ?? $emp->name }}</p>
                                            <span class="text-[9px] px-1.5 py-0.5 rounded {{ $emp->type === 'dosen' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">{{ $typeLabel }}</span>
                                            <span class="text-[10px] px-1.5 py-0.5 rounded border font-medium {{ $colorClass }}">{{ $score }}%</span>
                                        </div>
                                        <p class="text-xs text-gray-500">NIY: {{ $emp->niy ?? '-' }}@if($emp->nidn) · NIDN: {{ $emp->nidn }}@endif</p>
                                    </div>
                                </label>
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

                        {{-- Selected Info --}}
                        @if($selectedPddikti)
                        <div class="bg-green-50 border border-green-200 rounded-xl p-3">
                            <p class="text-sm text-green-700">
                                <i class="fas fa-check-circle mr-1"></i>
                                <strong>{{ $selectedPddikti->name }}</strong> ({{ $selectedPddikti->member_id }}) - Mahasiswa
                            </p>
                        </div>
                        @elseif($selectedEmployee)
                        <div class="bg-green-50 border border-green-200 rounded-xl p-3">
                            <p class="text-sm text-green-700">
                                <i class="fas fa-check-circle mr-1"></i>
                                <strong>{{ $selectedEmployee->full_name ?? $selectedEmployee->name }}</strong> ({{ $selectedEmployee->niy }}) - {{ $selectedEmployee->type === 'dosen' ? 'Dosen' : 'Tendik' }}
                            </p>
                        </div>
                        @endif

                        {{-- Action Buttons --}}
                        <div class="flex flex-col gap-2 pt-2">
                            @if($selectedPddiktiId)
                            <button type="button" wire:click="quickConfirm" wire:loading.attr="disabled"
                                class="w-full py-3 bg-gradient-to-r from-green-500 to-green-600 text-white font-semibold rounded-xl shadow-lg shadow-green-500/30 hover:shadow-xl transition">
                                <span wire:loading.remove wire:target="quickConfirm"><i class="fas fa-check mr-2"></i> Konfirmasi Data Mahasiswa</span>
                                <span wire:loading wire:target="quickConfirm"><i class="fas fa-spinner fa-spin"></i></span>
                            </button>
                            @elseif($selectedEmployee)
                            <button type="button" wire:click="quickConfirmEmployee" wire:loading.attr="disabled"
                                class="w-full py-3 bg-gradient-to-r from-green-500 to-green-600 text-white font-semibold rounded-xl shadow-lg shadow-green-500/30 hover:shadow-xl transition">
                                <span wire:loading.remove wire:target="quickConfirmEmployee"><i class="fas fa-check mr-2"></i> Konfirmasi Data {{ $selectedEmployee->type === 'dosen' ? 'Dosen' : 'Tendik' }}</span>
                                <span wire:loading wire:target="quickConfirmEmployee"><i class="fas fa-spinner fa-spin"></i></span>
                            </button>
                            @endif
                            
                            <button type="button" wire:click="skipToManualEntry" class="w-full py-2.5 text-gray-500 text-sm hover:text-gray-700 transition">
                                <i class="fas fa-edit mr-1"></i> Data tidak ditemukan? Input manual
                            </button>
                        </div>
                    </div>
                    @endif {{-- End of autoDetected else --}}

                    @else
                    {{-- STEP 2: Profile Form --}}
                    @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-600 text-sm p-4 rounded-xl mb-5">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                    </div>
                    @endif

                    @if($selectedPddikti)
                    {{-- SIMPLIFIED: Data Selected from SIAKAD --}}
                    <div class="space-y-4">
                        <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                            <div class="flex items-center gap-2 mb-3">
                                <i class="fas fa-check-circle text-green-600"></i>
                                <span class="font-semibold text-green-700">Data SIAKAD Terhubung</span>
                            </div>
                            <div class="bg-white rounded-lg p-4 border border-green-100">
                                <p class="font-bold text-gray-800 text-lg">{{ $selectedPddikti->name }}</p>
                                <div class="mt-2 space-y-1 text-sm text-gray-600">
                                    <p><i class="fas fa-id-card w-5 text-gray-400"></i> {{ $selectedPddikti->member_id }}</p>
                                    @if($selectedPddikti->department)
                                    <p><i class="fas fa-graduation-cap w-5 text-gray-400"></i> {{ $selectedPddikti->department->name }}</p>
                                    @endif
                                    @if($selectedPddikti->branch)
                                    <p><i class="fas fa-building w-5 text-gray-400"></i> {{ $selectedPddikti->branch->name }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        {{-- Only need phone number --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">No. HP (WhatsApp)</label>
                            <input type="text" wire:model="phone" required placeholder="081234567890"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        
                        <div class="flex gap-3 pt-2">
                            <button type="button" wire:click="backToSearch" class="flex-1 py-3 bg-gray-100 text-gray-700 font-medium rounded-xl hover:bg-gray-200 transition">
                                <i class="fas fa-arrow-left mr-2"></i> Kembali
                            </button>
                            <button type="button" wire:click="quickConfirm" 
                                wire:loading.attr="disabled"
                                class="flex-1 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white font-bold rounded-xl shadow-lg shadow-green-500/30 hover:shadow-xl transition">
                                <span wire:loading.remove wire:target="quickConfirm">
                                    <i class="fas fa-check mr-2"></i> Konfirmasi
                                </span>
                                <span wire:loading wire:target="quickConfirm">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </span>
                            </button>
                        </div>
                    </div>
                    
                    @else
                    
                    @if($entryMode === 'tendik')
                    {{-- TENDIK FORM: Simplified --}}
                    <form wire:submit.prevent="save" class="space-y-4">
                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 mb-4">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-user-cog text-blue-500"></i>
                                <span class="font-medium text-blue-700">Registrasi Tendik</span>
                            </div>
                            <p class="text-xs text-blue-600 mt-1">Tenaga Kependidikan - Perpustakaan Pusat</p>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">NIY</label>
                                <input type="text" wire:model.live.debounce.500ms="nim" required placeholder="Nomor Induk Yayasan"
                                    class="w-full px-4 py-2.5 border {{ $niyExistingEmployee ? 'border-green-400 bg-green-50' : 'border-gray-200' }} rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500">
                                
                                @if($niyExistingEmployee)
                                <div class="mt-2 p-3 bg-green-50 border border-green-200 rounded-xl">
                                    <p class="text-xs text-green-700 font-medium mb-1">
                                        <i class="fas fa-check-circle mr-1"></i> Data {{ $niyExistingEmployee->type === 'dosen' ? 'Dosen' : 'Tendik' }} ditemukan!
                                    </p>
                                    <p class="text-sm text-green-800 font-semibold">{{ $niyExistingEmployee->full_name ?? $niyExistingEmployee->name }}</p>
                                    <button type="button" wire:click="linkToExistingEmployee" 
                                        class="mt-2 w-full py-2 bg-green-600 text-white text-xs font-semibold rounded-lg hover:bg-green-700 transition">
                                        <i class="fas fa-link mr-1"></i> Hubungkan
                                    </button>
                                </div>
                                @endif
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                                <select wire:model="gender" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500">
                                    <option value="">Pilih</option>
                                    <option value="M">Laki-laki</option>
                                    <option value="F">Perempuan</option>
                                </select>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Satuan Kerja</label>
                            <input type="text" wire:model="satker" required placeholder="Contoh: Perpustakaan, IT Center, dll"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">No. HP (WhatsApp)</label>
                            <input type="text" wire:model="phone" required placeholder="081234567890"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        
                        <div class="flex gap-3 pt-4">
                            <button type="button" wire:click="backToSearch" class="flex-1 py-3 bg-gray-100 text-gray-700 font-medium rounded-xl hover:bg-gray-200 transition">
                                <i class="fas fa-arrow-left mr-2"></i> Kembali
                            </button>
                            <button type="submit" class="flex-1 py-3 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-semibold rounded-xl shadow-lg shadow-primary-500/30 hover:shadow-xl transition">
                                <i class="fas fa-check mr-2"></i> Daftar
                            </button>
                        </div>
                    </form>
                    
                    @else
                    {{-- FULL FORM: Manual Entry --}}
                    <form wire:submit.prevent="save" class="space-y-4">
                        {{-- Photo Upload --}}
                        <div class="flex justify-center">
                            <label class="cursor-pointer group">
                                <div class="w-20 h-20 rounded-xl bg-gray-100 border-2 border-dashed border-gray-300 flex flex-col items-center justify-center group-hover:border-primary-500 transition overflow-hidden" x-data x-ref="preview">
                                    @if($photo)
                                        <img src="{{ $photo->temporaryUrl() }}" class="w-full h-full object-cover">
                                    @else
                                        <i class="fas fa-camera text-gray-400 text-lg group-hover:text-primary-500"></i>
                                        <span class="text-xs text-gray-400 mt-1">Foto</span>
                                    @endif
                                </div>
                                <input type="file" wire:model="photo" accept="image/*" class="hidden">
                            </label>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">NIM/NIY/NIDN</label>
                                <input type="text" wire:model.live.debounce.500ms="nim" required placeholder="Nomor Induk"
                                    class="w-full px-4 py-2.5 border {{ ($nimExistingMember || $niyExistingEmployee) ? 'border-green-400 bg-green-50' : 'border-gray-200' }} rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500">
                                
                                @if($niyExistingEmployee)
                                <div class="mt-2 p-3 bg-green-50 border border-green-200 rounded-xl">
                                    <p class="text-xs text-green-700 font-medium mb-1">
                                        <i class="fas fa-check-circle mr-1"></i> Data {{ $niyExistingEmployee->type === 'dosen' ? 'Dosen' : 'Tendik' }} ditemukan!
                                    </p>
                                    <p class="text-sm text-green-800 font-semibold">{{ $niyExistingEmployee->full_name ?? $niyExistingEmployee->name }}</p>
                                    <p class="text-xs text-green-600">NIY: {{ $niyExistingEmployee->niy }}@if($niyExistingEmployee->faculty) · {{ $niyExistingEmployee->faculty }}@endif</p>
                                    <button type="button" wire:click="linkToExistingEmployee" 
                                        class="mt-2 w-full py-2 bg-green-600 text-white text-xs font-semibold rounded-lg hover:bg-green-700 transition">
                                        <i class="fas fa-link mr-1"></i> Hubungkan dengan Data Ini
                                    </button>
                                </div>
                                @elseif($nimExistingMember)
                                <div class="mt-2 p-3 bg-green-50 border border-green-200 rounded-xl">
                                    <p class="text-xs text-green-700 font-medium mb-1">
                                        <i class="fas fa-check-circle mr-1"></i> Data Mahasiswa ditemukan!
                                    </p>
                                    <p class="text-sm text-green-800 font-semibold">{{ $nimExistingMember->name }}</p>
                                    <p class="text-xs text-green-600">
                                        {{ $nimExistingMember->member_id }}
                                        @if($nimExistingMember->department) · {{ $nimExistingMember->department->name }}@endif
                                    </p>
                                    <button type="button" wire:click="linkToExistingMember" 
                                        class="mt-2 w-full py-2 bg-green-600 text-white text-xs font-semibold rounded-lg hover:bg-green-700 transition">
                                        <i class="fas fa-link mr-1"></i> Hubungkan dengan Data Ini
                                    </button>
                                </div>
                                @endif
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kampus</label>
                                <select wire:model="branch_id" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500">
                                    <option value="">Pilih Kampus</option>
                                    @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Anggota</label>
                            <select wire:model="member_type_id" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500">
                                <option value="">Pilih Jenis Anggota</option>
                                @foreach($memberTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fakultas</label>
                            <select wire:model.live="faculty_id" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500">
                                <option value="">Pilih Fakultas</option>
                                @foreach($faculties as $faculty)
                                <option value="{{ $faculty->id }}">{{ $faculty->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jurusan/Prodi</label>
                            <select wire:model="department_id" required :disabled="!$faculty_id" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 disabled:bg-gray-100">
                                <option value="">Pilih Jurusan</option>
                                @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                                <select wire:model="gender" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500">
                                    <option value="">Pilih</option>
                                    <option value="M">Laki-laki</option>
                                    <option value="F">Perempuan</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">No. HP</label>
                                <input type="text" wire:model="phone" required 
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500">
                            </div>
                        </div>
                        
                        <div class="flex gap-3 pt-4">
                            <button type="button" wire:click="backToSearch" class="flex-1 py-3 bg-gray-100 text-gray-700 font-medium rounded-xl hover:bg-gray-200 transition">
                                <i class="fas fa-arrow-left mr-2"></i> Kembali
                            </button>
                            <button type="submit" class="flex-1 py-3 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-semibold rounded-xl shadow-lg shadow-primary-500/30 hover:shadow-xl transition">
                                <i class="fas fa-check mr-2"></i> Simpan
                            </button>
                        </div>
                    </form>
                    @endif {{-- End of entryMode tendik --}}
                    @endif {{-- End of selectedPddikti --}}
                    @endif {{-- End of step 1 --}}
                </div>
            </div>
        </div>
    </div>
</div>
