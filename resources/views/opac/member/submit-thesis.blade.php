<x-opac.layout title="Unggah Tugas Akhir">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-primary-600 to-primary-800 text-white">
        <div class="max-w-5xl mx-auto px-4 py-6">
            <div class="flex items-center gap-3">
                <a href="{{ route('opac.member.submissions') }}" class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center hover:bg-white/30 transition">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-xl font-bold">Unggah Tugas Akhir</h1>
                    <p class="text-primary-200 text-sm">Form pengajuan skripsi, tesis, atau disertasi</p>
                </div>
            </div>
        </div>
    </div>

    <section class="px-4 py-6 lg:py-10">
        @livewire('thesis-submission-form', ['submissionId' => $submissionId ?? null])
    </section>
</x-opac.layout>
