<x-opac.layout title="Submission Saya">
    <x-opac.page-header 
        title="Submission Tugas Akhir" 
        subtitle="Kelola pengajuan tugas akhir Anda"
        :breadcrumbs="[['label' => 'Dashboard', 'url' => route('opac.member.dashboard')], ['label' => 'Submission Saya']]"
    />

    <section class="max-w-5xl mx-auto px-4 py-6 lg:py-10">
        @livewire('my-submissions')
    </section>
</x-opac.layout>
