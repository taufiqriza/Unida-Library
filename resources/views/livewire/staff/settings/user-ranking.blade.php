<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Ranking User Staff</h2>
            <p class="text-sm text-gray-500">Analisis aktivitas dan performa staff berdasarkan login, online time, dan aktivitas</p>
        </div>
        
        {{-- Time Range Filter --}}
        <div class="flex items-center gap-2">
            <label class="text-sm font-medium text-gray-700">Periode:</label>
            <select wire:model.live="timeRange" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="7">7 Hari</option>
                <option value="30">30 Hari</option>
                <option value="90">90 Hari</option>
                <option value="365">1 Tahun</option>
            </select>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-4 text-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-lg"></i>
                </div>
                <div>
                    <p class="text-sm opacity-90">Total Staff</p>
                    <p class="text-xl font-bold">{{ $rankings->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-4 text-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-line text-lg"></i>
                </div>
                <div>
                    <p class="text-sm opacity-90">Aktif {{ $timeRange }} Hari</p>
                    <p class="text-xl font-bold">{{ $rankings->where('login_days', '>', 0)->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-4 text-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-fire text-lg"></i>
                </div>
                <div>
                    <p class="text-sm opacity-90">Avg Activities</p>
                    <p class="text-xl font-bold">{{ $rankings->avg('total_activities') ? number_format($rankings->avg('total_activities'), 0) : 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-amber-500 to-amber-600 rounded-xl p-4 text-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-trophy text-lg"></i>
                </div>
                <div>
                    <p class="text-sm opacity-90">Top Score</p>
                    <p class="text-xl font-bold">{{ $rankings->first()->activity_score ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Rankings Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-900">Ranking Staff Berdasarkan Aktivitas</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rank</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Staff</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Login Days</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Activities</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Frequency</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Last Active</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($rankings as $index => $user)
                    <tr class="hover:bg-gray-50 transition">
                        {{-- Rank --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($index < 3)
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold text-sm
                                        {{ $index === 0 ? 'bg-yellow-500' : ($index === 1 ? 'bg-gray-400' : 'bg-amber-600') }}">
                                        {{ $index + 1 }}
                                    </div>
                                @else
                                    <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 font-medium text-sm">
                                        {{ $index + 1 }}
                                    </div>
                                @endif
                            </div>
                        </td>
                        
                        {{-- Staff Info --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ ucfirst($user->role) }} • {{ $user->branch->name ?? 'No Branch' }}</div>
                                </div>
                            </div>
                        </td>
                        
                        {{-- Activity Score --}}
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $user->activity_score >= 200 ? 'bg-green-100 text-green-800' : 
                                   ($user->activity_score >= 100 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ number_format($user->activity_score) }}
                            </div>
                        </td>
                        
                        {{-- Login Days --}}
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="text-sm font-medium text-gray-900">{{ $user->login_days }}</div>
                            <div class="text-xs text-gray-500">dari {{ $timeRange }} hari</div>
                        </td>
                        
                        {{-- Total Activities --}}
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="text-sm font-medium text-gray-900">{{ number_format($user->total_activities) }}</div>
                        </td>
                        
                        {{-- Activity Frequency --}}
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="text-sm font-medium text-gray-900">{{ $user->login_frequency }}</div>
                            <div class="text-xs text-gray-500">per hari</div>
                        </td>
                        
                        {{-- Last Active --}}
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($user->last_activity)
                                <div class="text-sm text-gray-900">{{ $user->last_activity->diffForHumans() }}</div>
                            @else
                                <span class="text-xs text-gray-400">Tidak ada</span>
                            @endif
                        </td>
                        
                        {{-- Status --}}
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @php
                                $daysSinceActive = $user->last_activity ? now()->diffInDays($user->last_activity) : 999;
                            @endphp
                            
                            @if($daysSinceActive <= 1)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <span class="w-1.5 h-1.5 bg-green-400 rounded-full mr-1.5"></span>
                                    Aktif
                                </span>
                            @elseif($daysSinceActive <= 7)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <span class="w-1.5 h-1.5 bg-yellow-400 rounded-full mr-1.5"></span>
                                    Jarang
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <span class="w-1.5 h-1.5 bg-red-400 rounded-full mr-1.5"></span>
                                    Tidak Aktif
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        @if($rankings->isEmpty())
        <div class="text-center py-12">
            <i class="fas fa-chart-bar text-4xl text-gray-300 mb-4"></i>
            <p class="text-gray-500">Tidak ada data aktivitas untuk periode ini</p>
        </div>
        @endif
    </div>
</div>
