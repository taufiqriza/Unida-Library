<x-opac.layout :title="$page['title']">
    <x-opac.page-header 
        :title="$page['title']" 
        :subtitle="$page['subtitle'] ?? null"
        :breadcrumbs="[['label' => $page['title']]]"
    />

    <section class="max-w-4xl mx-auto px-4 py-6 lg:py-10">
        @if($page['content'])
            <div class="bg-white rounded-xl p-5 lg:p-6 shadow-sm border border-gray-100">
                <div class="prose prose-sm lg:prose-base prose-blue max-w-none">
                    {!! $page['content'] !!}
                </div>
            </div>
        @else
            <div class="bg-gray-50 rounded-xl p-8 lg:p-12 text-center border border-gray-200">
                <div class="w-16 h-16 bg-gray-200 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-hard-hat text-gray-400 text-2xl"></i>
                </div>
                <h3 class="font-bold text-gray-900 mb-2">Halaman Dalam Pengembangan</h3>
                <p class="text-gray-500 text-sm mb-4">Konten untuk halaman ini sedang dalam proses pembuatan.</p>
                <a href="{{ route('opac.home') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 transition">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        @endif
    </section>
</x-opac.layout>
