<x-opac.layout title="Submission Saya">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-primary-600 to-primary-800 text-white">
        <div class="max-w-5xl mx-auto px-4 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <a href="{{ route('opac.member.dashboard') }}" class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center hover:bg-white/30 transition">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="text-xl font-bold">Riwayat Unggah Mandiri</h1>
                        <p class="text-primary-200 text-sm">Kelola pengajuan tugas akhir Anda</p>
                    </div>
                </div>
                <a href="{{ route('opac.member.submit-thesis') }}" class="px-4 py-2 bg-white/20 hover:bg-white/30 rounded-xl transition flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    <span class="hidden sm:inline">Unggah Baru</span>
                </a>
            </div>
        </div>
    </div>

    <section class="max-w-5xl mx-auto px-4 py-6 lg:py-10">
        @livewire('my-submissions')
    </section>
</x-opac.layout>
