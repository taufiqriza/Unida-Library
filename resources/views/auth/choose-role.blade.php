<x-opac.layout title="Pilih Portal">
    <div class="min-h-[70vh] flex items-center justify-center py-8 px-4">
        <div class="w-full max-w-2xl">
            <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 overflow-hidden">
                {{-- Header --}}
                <div class="bg-gradient-to-r from-primary-600 to-primary-800 px-6 py-5 text-center">
                    <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-user-check text-2xl text-white"></i>
                    </div>
                    <h1 class="text-xl font-bold text-white">Pilih Portal</h1>
                    <p class="text-primary-200 text-sm mt-1">Akun Anda terdaftar di dua portal</p>
                </div>

                {{-- Content --}}
                <div class="p-6">
                    <div class="grid sm:grid-cols-2 gap-4">
                        {{-- Staff Portal --}}
                        @if($staff)
                        <a href="{{ route('auth.select-role', 'staff') }}" 
                           class="group relative bg-gray-50 hover:bg-primary-50 border-2 border-gray-200 hover:border-primary-500 rounded-2xl p-5 transition-all duration-200">
                            <div class="absolute top-4 right-4 w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                                <i class="fas fa-arrow-right text-primary-600 text-sm"></i>
                            </div>
                            
                            <div class="w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-700 rounded-xl flex items-center justify-center mb-4 shadow-lg shadow-primary-500/25">
                                <i class="fas fa-user-tie text-white text-lg"></i>
                            </div>
                            
                            <h3 class="text-lg font-bold text-gray-900 mb-1">Staff Portal</h3>
                            <p class="text-gray-500 text-sm mb-4">Akses panel staf perpustakaan</p>
                            
                            <div class="flex items-center gap-3 p-3 bg-white rounded-xl border border-gray-200">
                                <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center">
                                    <span class="text-primary-700 font-bold text-sm">{{ strtoupper(substr($staff->name, 0, 2)) }}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-gray-900 font-medium text-sm truncate">{{ $staff->name }}</p>
                                    <p class="text-gray-500 text-xs">{{ $staff->getRoleLabel() }}</p>
                                </div>
                            </div>
                        </a>
                        @endif

                        {{-- Member Portal --}}
                        @if($member)
                        <a href="{{ route('auth.select-role', 'member') }}" 
                           class="group relative bg-gray-50 hover:bg-emerald-50 border-2 border-gray-200 hover:border-emerald-500 rounded-2xl p-5 transition-all duration-200">
                            <div class="absolute top-4 right-4 w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                                <i class="fas fa-arrow-right text-emerald-600 text-sm"></i>
                            </div>
                            
                            <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center mb-4 shadow-lg shadow-emerald-500/25">
                                <i class="fas fa-user-graduate text-white text-lg"></i>
                            </div>
                            
                            <h3 class="text-lg font-bold text-gray-900 mb-1">Member Portal</h3>
                            <p class="text-gray-500 text-sm mb-4">Akses layanan anggota perpustakaan</p>
                            
                            <div class="flex items-center gap-3 p-3 bg-white rounded-xl border border-gray-200">
                                <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                                    <span class="text-emerald-700 font-bold text-sm">{{ strtoupper(substr($member->name, 0, 2)) }}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-gray-900 font-medium text-sm truncate">{{ $member->name }}</p>
                                    <p class="text-gray-500 text-xs">{{ $member->member_id }}</p>
                                </div>
                            </div>
                        </a>
                        @endif
                    </div>

                    {{-- Back Link --}}
                    <div class="text-center mt-6 pt-4 border-t border-gray-100">
                        <a href="{{ route('login') }}" class="text-gray-500 hover:text-primary-600 text-sm transition inline-flex items-center gap-2">
                            <i class="fas fa-arrow-left"></i> Kembali ke halaman login
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-opac.layout>
