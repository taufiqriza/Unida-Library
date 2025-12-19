<div class="min-h-screen bg-gray-50">
    {{-- Hero Header --}}
    <div class="bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 relative overflow-hidden">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"0.05\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-50"></div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2 blur-3xl"></div>
        
        <div class="relative max-w-5xl mx-auto px-4 py-10 lg:py-14">
            <div class="text-center">
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full text-white/80 text-sm mb-4">
                    <i class="fas fa-university"></i>
                    <span>{{ __('opac.database_access.consortium') }}</span>
                </div>
                <h1 class="text-2xl lg:text-4xl font-bold text-white mb-3">{{ __('opac.database_access.title') }}</h1>
                <p class="text-blue-200 max-w-2xl mx-auto">
                    {{ __('opac.database_access.subtitle') }}
                </p>
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 py-8">
        {{-- Login Notice --}}
        @guest('member')
        <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-xl flex items-start gap-3">
            <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-amber-600"></i>
            </div>
            <div>
                <h3 class="font-bold text-gray-900">{{ __('opac.database_access.login_required') }}</h3>
                <p class="text-sm text-gray-600 mb-3">{{ __('opac.database_access.login_required_desc') }}</p>
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white text-sm font-semibold rounded-lg hover:bg-primary-700 transition">
                    <i class="fas fa-sign-in-alt"></i> {{ __('opac.database_access.login_now') }}
                </a>
            </div>
        </div>
        @endguest

        {{-- How It Works --}}
        <div class="mb-8 p-5 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl border border-blue-100">
            <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-info-circle text-blue-600"></i>
                {{ __('opac.database_access.how_to_access') }}
            </h3>
            <div class="grid sm:grid-cols-3 gap-4">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 bg-blue-600 text-white rounded-lg flex items-center justify-center font-bold text-sm flex-shrink-0">1</div>
                    <div>
                        <p class="font-medium text-gray-900 text-sm">{{ __('opac.database_access.step_1') }}</p>
                        <p class="text-xs text-gray-500">{{ __('opac.database_access.step_1_desc') }}</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 bg-blue-600 text-white rounded-lg flex items-center justify-center font-bold text-sm flex-shrink-0">2</div>
                    <div>
                        <p class="font-medium text-gray-900 text-sm">{{ __('opac.database_access.step_2') }}</p>
                        <p class="text-xs text-gray-500">{{ __('opac.database_access.step_2_desc') }}</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 bg-blue-600 text-white rounded-lg flex items-center justify-center font-bold text-sm flex-shrink-0">3</div>
                    <div>
                        <p class="font-medium text-gray-900 text-sm">{{ __('opac.database_access.step_3') }}</p>
                        <p class="text-xs text-gray-500">{{ __('opac.database_access.step_3_desc') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Databases Grid --}}
        <div class="space-y-6">
            @foreach($databases as $key => $db)
            @php
                $colors = [
                    'orange' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-600', 'gradient' => 'from-orange-500 to-amber-500'],
                    'purple' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-600', 'gradient' => 'from-purple-500 to-indigo-500'],
                    'blue' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-600', 'gradient' => 'from-blue-500 to-cyan-500'],
                ];
                $c = $colors[$db['color']] ?? $colors['blue'];
                $isRevealed = in_array($key, $revealedCredentials);
            @endphp
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-all">
                <div class="p-5 lg:p-6">
                    <div class="flex flex-col lg:flex-row lg:items-start gap-5">
                        {{-- Icon & Info --}}
                        <div class="flex-1">
                            <div class="flex items-start gap-4">
                                <div class="w-14 h-14 bg-gradient-to-br {{ $c['gradient'] }} rounded-xl flex items-center justify-center shadow-lg flex-shrink-0">
                                    <i class="fas {{ $db['icon'] }} text-white text-xl"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-lg font-bold text-gray-900">{{ $db['name'] }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">{{ $db['description'] }}</p>
                                    
                                    <div class="flex flex-wrap items-center gap-2 mt-3">
                                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 text-gray-700 text-xs font-medium rounded-lg">
                                            <i class="fas fa-building text-gray-400"></i>
                                            {{ $db['provider'] }}
                                        </span>
                                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-emerald-100 text-emerald-700 text-xs font-medium rounded-lg">
                                            <i class="fas fa-layer-group"></i>
                                            {{ $db['collections'] }} {{ $db['type'] }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Subjects --}}
                            <div class="mt-4 flex flex-wrap gap-1.5">
                                @foreach($db['subjects'] as $subject)
                                <span class="px-2 py-0.5 {{ $c['bg'] }} {{ $c['text'] }} text-xs rounded-full">{{ __('opac.database_access.subjects.' . $subject) }}</span>
                                @endforeach
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="lg:w-72 flex-shrink-0 space-y-3">
                            {{-- Access Button --}}
                            <button wire:click="accessDatabase('{{ $key }}')" 
                                    class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-gradient-to-r {{ $c['gradient'] }} text-white font-semibold rounded-xl hover:opacity-90 transition shadow-lg"
                                    @guest('member') disabled title="{{ __('opac.database_access.login_required') }}" @endguest>
                                <i class="fas fa-external-link-alt"></i>
                                {{ __('opac.database_access.access_database') }}
                            </button>

                            {{-- Credentials Section --}}
                            @auth('member')
                            <div class="bg-gray-50 rounded-xl p-3 space-y-2" x-data="{ copied: null }">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-semibold text-gray-500">{{ __('opac.database_access.credentials') }}</span>
                                    <button wire:click="revealCredential('{{ $key }}')" 
                                            class="text-xs {{ $isRevealed ? 'text-red-600' : 'text-primary-600' }} hover:underline">
                                        <i class="fas {{ $isRevealed ? 'fa-eye-slash' : 'fa-eye' }} mr-1"></i>
                                        {{ $isRevealed ? __('opac.database_access.hide') : __('opac.database_access.show') }}
                                    </button>
                                </div>
                                
                                @if($isRevealed)
                                <div class="space-y-2">
                                    {{-- Username --}}
                                    <div class="flex items-center gap-2 bg-white rounded-lg px-3 py-2 border border-gray-200">
                                        <span class="text-xs text-gray-400 w-16">{{ __('opac.database_access.username') }}</span>
                                        <code class="flex-1 text-sm font-mono text-gray-900">{{ $db['username'] }}</code>
                                        <button @click="navigator.clipboard.writeText('{{ $db['username'] }}'); copied = 'user-{{ $key }}'; setTimeout(() => copied = null, 2000)"
                                                class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-gray-100 transition"
                                                :class="copied === 'user-{{ $key }}' ? 'bg-emerald-100 text-emerald-600' : 'text-gray-400'">
                                            <i :class="copied === 'user-{{ $key }}' ? 'fas fa-check' : 'fas fa-copy'" class="text-xs"></i>
                                        </button>
                                    </div>
                                    {{-- Password --}}
                                    <div class="flex items-center gap-2 bg-white rounded-lg px-3 py-2 border border-gray-200">
                                        <span class="text-xs text-gray-400 w-16">{{ __('opac.database_access.password') }}</span>
                                        <code class="flex-1 text-sm font-mono text-gray-900">{{ $db['password'] }}</code>
                                        <button @click="navigator.clipboard.writeText('{{ $db['password'] }}'); copied = 'pass-{{ $key }}'; setTimeout(() => copied = null, 2000)"
                                                class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-gray-100 transition"
                                                :class="copied === 'pass-{{ $key }}' ? 'bg-emerald-100 text-emerald-600' : 'text-gray-400'">
                                            <i :class="copied === 'pass-{{ $key }}' ? 'fas fa-check' : 'fas fa-copy'" class="text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                                @else
                                <div class="text-center py-3 text-gray-400 text-sm">
                                    <i class="fas fa-lock mr-1"></i>
                                    {{ __('opac.database_access.click_to_show') }}
                                </div>
                                @endif
                            </div>
                            @else
                            <div class="bg-gray-50 rounded-xl p-4 text-center text-gray-400 text-sm">
                                <i class="fas fa-lock mr-1"></i>
                                {{ __('opac.database_access.login_to_view') }}
                            </div>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Info Box --}}
        <div class="mt-8 p-5 bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl text-white">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-amber-500/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-shield-alt text-amber-400 text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-lg mb-2">{{ __('opac.database_access.important_info') }}</h3>
                    <ul class="space-y-2 text-sm text-slate-300">
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check-circle text-emerald-400 mt-0.5"></i>
                            <span>{{ __('opac.database_access.info_1') }}</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check-circle text-emerald-400 mt-0.5"></i>
                            <span>{{ __('opac.database_access.info_2') }}</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-exclamation-triangle text-amber-400 mt-0.5"></i>
                            <span>{{ __('opac.database_access.info_3') }}</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-headset text-blue-400 mt-0.5"></i>
                            <span>{{ __('opac.database_access.info_4') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Back Link --}}
        <div class="mt-6 text-center">
            <a href="{{ route('opac.page', 'journal-subscription') }}" class="inline-flex items-center gap-2 text-primary-600 hover:text-primary-700 font-medium">
                <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}"></i>
                {{ __('opac.database_access.back_to_resources') }}
            </a>
        </div>
    </div>
</div>
