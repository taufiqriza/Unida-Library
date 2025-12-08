@props(['title', 'subtitle' => null, 'breadcrumbs' => []])

<!-- Compact Page Header -->
<section class="bg-gradient-to-r from-primary-600 to-primary-800 text-white py-6 lg:py-8">
    <div class="max-w-6xl mx-auto px-4">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-primary-200 mb-2">
            <a href="{{ route('opac.home') }}" class="hover:text-white"><i class="fas fa-home"></i></a>
            @foreach($breadcrumbs as $crumb)
                <i class="fas fa-chevron-right text-[8px]"></i>
                @if(isset($crumb['url']))
                    <a href="{{ $crumb['url'] }}" class="hover:text-white">{{ $crumb['label'] }}</a>
                @else
                    <span class="text-white">{{ $crumb['label'] }}</span>
                @endif
            @endforeach
        </nav>
        <!-- Title -->
        <h1 class="text-xl lg:text-2xl font-bold">{{ $title }}</h1>
        @if($subtitle)
            <p class="text-primary-200 text-sm mt-1">{{ $subtitle }}</p>
        @endif
    </div>
</section>
