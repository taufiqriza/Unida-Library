{{-- Details Section --}}
<div class="grid lg:grid-cols-2 gap-6 mb-6">
    {{-- Top Pages --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-file-alt text-blue-500"></i> Halaman Populer
            </h3>
            <span class="text-xs text-gray-500">Top 15</span>
        </div>
        <div class="divide-y divide-gray-50 max-h-96 overflow-y-auto">
            @forelse($topPages as $i => $page)
            <div class="px-6 py-3 hover:bg-gray-50 transition flex items-center gap-4">
                <span class="w-6 h-6 rounded-full {{ $i < 3 ? 'bg-gradient-to-br from-amber-400 to-orange-500 text-white' : 'bg-gray-100 text-gray-600' }} text-xs font-bold flex items-center justify-center">{{ $i + 1 }}</span>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate" title="{{ $page['path'] }}">{{ $page['path'] }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ Str::limit($page['title'], 40) }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-bold text-gray-900">{{ number_format($page['views']) }}</p>
                    <p class="text-xs text-gray-500">{{ $page['users'] }} users</p>
                </div>
            </div>
            @empty
            <div class="px-6 py-8 text-center text-gray-400">Tidak ada data</div>
            @endforelse
        </div>
    </div>

    {{-- Traffic Sources --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-share-alt text-purple-500"></i> Sumber Traffic
            </h3>
        </div>
        <div class="p-6">
            @php $totalSources = collect($trafficSources)->sum('value') ?: 1; @endphp
            <div class="space-y-4">
                @foreach($trafficSources as $source)
                @php 
                    $pct = round($source['value'] / $totalSources * 100);
                    $colors = [
                        'Direct' => ['bg' => 'bg-blue-500', 'light' => 'bg-blue-100', 'text' => 'text-blue-600'],
                        'Organic Search' => ['bg' => 'bg-green-500', 'light' => 'bg-green-100', 'text' => 'text-green-600'],
                        'Referral' => ['bg' => 'bg-purple-500', 'light' => 'bg-purple-100', 'text' => 'text-purple-600'],
                        'Social' => ['bg' => 'bg-pink-500', 'light' => 'bg-pink-100', 'text' => 'text-pink-600'],
                        'Email' => ['bg' => 'bg-amber-500', 'light' => 'bg-amber-100', 'text' => 'text-amber-600'],
                    ];
                    $color = $colors[$source['name']] ?? ['bg' => 'bg-gray-500', 'light' => 'bg-gray-100', 'text' => 'text-gray-600'];
                    $icon = match($source['name']) {
                        'Direct' => 'fa-link',
                        'Organic Search' => 'fa-search',
                        'Referral' => 'fa-external-link-alt',
                        'Social' => 'fa-share-alt',
                        'Email' => 'fa-envelope',
                        default => 'fa-globe'
                    };
                @endphp
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 {{ $color['light'] }} {{ $color['text'] }} rounded-lg flex items-center justify-center">
                                <i class="fas {{ $icon }} text-sm"></i>
                            </div>
                            <span class="font-medium text-gray-900">{{ $source['name'] }}</span>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-gray-900">{{ number_format($source['value']) }}</span>
                            <span class="text-gray-500 text-sm ml-1">({{ $pct }}%)</span>
                        </div>
                    </div>
                    <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full {{ $color['bg'] }} rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- Geographic Data --}}
<div class="grid lg:grid-cols-2 gap-6">
    {{-- Countries --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-globe-asia text-green-500"></i> Negara
            </h3>
            <span class="text-xs text-gray-500">{{ count($countries) }} negara</span>
        </div>
        <div class="divide-y divide-gray-50 max-h-80 overflow-y-auto">
            @php $totalCountryUsers = collect($countries)->sum('users') ?: 1; @endphp
            @forelse($countries as $i => $country)
            @php $pct = round($country['users'] / $totalCountryUsers * 100); @endphp
            <div class="px-6 py-3 hover:bg-gray-50 transition">
                <div class="flex items-center gap-3">
                    <span class="text-xs text-gray-400 w-4">{{ $i + 1 }}</span>
                    @if($country['code'])
                    <img src="https://flagcdn.com/24x18/{{ strtolower($country['code']) }}.png" class="w-6 h-4 rounded shadow-sm" alt="{{ $country['name'] }}" onerror="this.style.display='none'">
                    @else
                    <div class="w-6 h-4 bg-gray-200 rounded"></div>
                    @endif
                    <span class="flex-1 font-medium text-gray-900">{{ $country['name'] }}</span>
                    <div class="w-24">
                        <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-green-400 to-emerald-500 rounded-full" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                    <span class="text-sm font-bold text-gray-900 w-16 text-right">{{ number_format($country['users']) }}</span>
                </div>
            </div>
            @empty
            <div class="px-6 py-8 text-center text-gray-400">Tidak ada data</div>
            @endforelse
        </div>
    </div>

    {{-- Cities --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-city text-indigo-500"></i> Kota
            </h3>
            <span class="text-xs text-gray-500">Top 10</span>
        </div>
        <div class="divide-y divide-gray-50 max-h-80 overflow-y-auto">
            @php $totalCityUsers = collect($cities)->sum('value') ?: 1; @endphp
            @forelse($cities as $i => $city)
            @php $pct = round($city['value'] / $totalCityUsers * 100); @endphp
            <div class="px-6 py-3 hover:bg-gray-50 transition">
                <div class="flex items-center gap-3">
                    <span class="w-6 h-6 rounded-full {{ $i < 3 ? 'bg-gradient-to-br from-indigo-400 to-purple-500 text-white' : 'bg-gray-100 text-gray-600' }} text-xs font-bold flex items-center justify-center">{{ $i + 1 }}</span>
                    <span class="flex-1 font-medium text-gray-900">{{ $city['name'] === '(not set)' ? 'Tidak diketahui' : $city['name'] }}</span>
                    <div class="w-24">
                        <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-indigo-400 to-purple-500 rounded-full" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                    <span class="text-sm font-bold text-gray-900 w-16 text-right">{{ number_format($city['value']) }}</span>
                </div>
            </div>
            @empty
            <div class="px-6 py-8 text-center text-gray-400">Tidak ada data</div>
            @endforelse
        </div>
    </div>
</div>
