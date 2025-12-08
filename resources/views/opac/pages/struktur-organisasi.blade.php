<x-opac.layout title="Struktur Organisasi">
    <x-opac.page-header 
        title="Struktur Organisasi" 
        subtitle="Tim profesional yang melayani dengan sepenuh hati"
        :breadcrumbs="[['label' => 'Profil'], ['label' => 'Struktur Organisasi']]"
    />

    <section class="max-w-5xl mx-auto px-4 py-6 lg:py-10">
        <!-- Org Chart Image -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6 lg:mb-8">
            <div class="bg-gradient-to-r from-primary-600 to-primary-700 px-4 py-3">
                <h2 class="text-white font-semibold text-sm lg:text-base">Bagan Organisasi</h2>
            </div>
            <div class="p-4">
                <img src="{{ asset('storage/struktur-organisasi.png') }}" alt="Struktur Organisasi" class="w-full rounded-lg" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="hidden aspect-[2/1] bg-gray-50 rounded-lg items-center justify-center">
                    <div class="text-center">
                        <i class="fas fa-sitemap text-gray-300 text-4xl mb-2"></i>
                        <p class="text-gray-400 text-sm">Bagan Struktur Organisasi</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Team -->
        <h3 class="font-bold text-gray-900 mb-4 text-base lg:text-lg">Tim Perpustakaan</h3>
        
        <!-- Kepala -->
        <div class="bg-gradient-to-r from-primary-600 to-primary-700 rounded-xl p-4 mb-4 flex items-center gap-4">
            <div class="w-14 h-14 lg:w-16 lg:h-16 rounded-xl bg-white/20 overflow-hidden flex-shrink-0">
                <img src="{{ asset('storage/staff/kepala.jpg') }}" alt="Kepala" class="w-full h-full object-cover" onerror="this.src='https://ui-avatars.com/api/?name=HSU&background=ffffff&color=2563eb&size=64'">
            </div>
            <div class="text-white">
                <span class="text-xs bg-white/20 px-2 py-0.5 rounded">Kepala Perpustakaan</span>
                <h4 class="font-bold text-sm lg:text-base mt-1">H. Syamsul Hadi Untung M.A, M.Ls</h4>
            </div>
        </div>

        <!-- Staff Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @php
            $staff = [
                ['name' => 'Laili Triana Mustikasari, A.Md.', 'role' => 'Layanan Pengguna & Teknis 1', 'color' => 'blue', 'img' => 'lpt1.jpg', 'initials' => 'LTM'],
                ['name' => 'Alfian Haris, SJP', 'role' => 'Layanan Pengguna & Teknis 2', 'color' => 'emerald', 'img' => 'lpt2.jpg', 'initials' => 'AHS'],
                ['name' => 'Febriana Maghfiroh A.Md.', 'role' => 'Tata Usaha 1', 'color' => 'purple', 'img' => 'tu1.jpg', 'initials' => 'FMA'],
                ['name' => 'Ernis Prasetiyo Wati S.E', 'role' => 'Tata Usaha 2', 'color' => 'pink', 'img' => 'tu2.jpg', 'initials' => 'EPW'],
                ['name' => 'Muhamad Taufiq Riza, S.Kom', 'role' => 'IT Infrastruktur 1', 'color' => 'cyan', 'img' => 'it1.jpg', 'initials' => 'MTR'],
                ['name' => 'Amalul Fahrul Handika A.Md.T', 'role' => 'IT Infrastruktur 2', 'color' => 'indigo', 'img' => 'it2.jpg', 'initials' => 'AFH'],
            ];
            $colors = [
                'blue' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-600', 'badge' => 'bg-blue-50 text-blue-600'],
                'emerald' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'badge' => 'bg-emerald-50 text-emerald-600'],
                'purple' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-600', 'badge' => 'bg-purple-50 text-purple-600'],
                'pink' => ['bg' => 'bg-pink-100', 'text' => 'text-pink-600', 'badge' => 'bg-pink-50 text-pink-600'],
                'cyan' => ['bg' => 'bg-cyan-100', 'text' => 'text-cyan-600', 'badge' => 'bg-cyan-50 text-cyan-600'],
                'indigo' => ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-600', 'badge' => 'bg-indigo-50 text-indigo-600'],
            ];
            @endphp
            
            @foreach($staff as $s)
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl {{ $colors[$s['color']]['bg'] }} overflow-hidden flex-shrink-0">
                    <img src="{{ asset('storage/staff/' . $s['img']) }}" alt="{{ $s['name'] }}" class="w-full h-full object-cover" onerror="this.src='https://ui-avatars.com/api/?name={{ $s['initials'] }}&background=e0e7ff&color=4f46e5&size=48'">
                </div>
                <div class="min-w-0">
                    <h4 class="font-semibold text-gray-900 text-sm truncate">{{ $s['name'] }}</h4>
                    <span class="text-xs {{ $colors[$s['color']]['badge'] }} px-2 py-0.5 rounded inline-block mt-1">{{ $s['role'] }}</span>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Contact -->
        <div class="mt-6 lg:mt-8 bg-gradient-to-r from-primary-600 to-primary-700 rounded-xl p-4 lg:p-5 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-white text-center sm:text-left">
                <h4 class="font-bold">Butuh Bantuan?</h4>
                <p class="text-primary-200 text-sm">Tim kami siap membantu Anda</p>
            </div>
            <div class="flex gap-2">
                <a href="mailto:library@unida.gontor.ac.id" class="px-4 py-2 bg-white text-primary-600 rounded-lg text-sm font-medium hover:bg-primary-50 transition">
                    <i class="fas fa-envelope mr-1"></i> Email
                </a>
                <a href="https://wa.me/6285183053934" target="_blank" class="px-4 py-2 bg-white/10 text-white rounded-lg text-sm font-medium hover:bg-white/20 transition">
                    <i class="fab fa-whatsapp mr-1"></i> WhatsApp
                </a>
            </div>
        </div>
    </section>
</x-opac.layout>
