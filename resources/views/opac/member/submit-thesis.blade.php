<x-opac.layout title="Unggah Tugas Akhir">
    <x-opac.page-header 
        title="Unggah Tugas Akhir" 
        subtitle="Form pengajuan skripsi, tesis, atau disertasi"
        :breadcrumbs="[['label' => 'Dashboard', 'url' => route('opac.member.dashboard')], ['label' => 'Unggah Tugas Akhir']]"
    />

    <section class="px-4 py-6 lg:py-10">
        @livewire('thesis-submission-form', ['submissionId' => $submissionId ?? null])
    </section>
</x-opac.layout>
