<div class="space-y-6">
    {{-- Header Section --}}
    <div class="bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-700 rounded-xl p-6 text-white">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h3 class="text-2xl font-bold mb-2">🏆 Ranking Staff Teraktif</h3>
                <p class="text-blue-100">Analisis komprehensif aktivitas staff berdasarkan login, penggunaan sistem, dan keterlibatan</p>
            </div>
            
            {{-- Time Range Filter --}}
            <div class="flex flex-col sm:flex-row gap-3">
                <select wire:model.live="timeRange" class="px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/70 backdrop-blur-sm">
                    <option value="7" class="text-gray-900">7 Hari Terakhir</option>
                    <option value="30" class="text-gray-900">30 Hari Terakhir</option>
                    <option value="90" class="text-gray-900">90 Hari Terakhir</option>
                    <option value="365" class="text-gray-900">1 Tahun Terakhir</option>
                </select>
                <div class="text-sm text-blue-100 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                    </svg>
                    {{ $timeRange }} hari data
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Summary --}}
    @if(isset($totalStats) && $totalStats['total_users'] > 0)
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-4 text-white">
                <div class="text-2xl font-bold">{{ $totalStats['total_users'] }}</div>
                <div class="text-blue-100 text-sm">Total Staff</div>
            </div>
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-4 text-white">
                <div class="text-2xl font-bold">{{ $totalStats['active_users'] }}</div>
                <div class="text-green-100 text-sm">Staff Aktif</div>
            </div>
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg p-4 text-white">
                <div class="text-2xl font-bold">{{ $totalStats['total_login_days'] }}</div>
                <div class="text-purple-100 text-sm">Total Hari Login</div>
            </div>
            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg p-4 text-white">
                <div class="text-2xl font-bold">{{ number_format($totalStats['total_activities']) }}</div>
                <div class="text-orange-100 text-sm">Total Aktivitas</div>
            </div>
            <div class="bg-gradient-to-br from-pink-500 to-pink-600 rounded-lg p-4 text-white">
                <div class="text-2xl font-bold">{{ number_format($totalStats['avg_score']) }}</div>
                <div class="text-pink-100 text-sm">Rata-rata Skor</div>
            </div>
        </div>
    @endif

    {{-- Rankings Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rank</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Staff</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role & Cabang</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Login</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aktivitas</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Modul</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Terakhir Aktif</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Skor</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($rankings as $index => $user)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($index < 3)
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-lg
                                            {{ $index === 0 ? 'bg-gradient-to-r from-yellow-400 to-yellow-600' : ($index === 1 ? 'bg-gradient-to-r from-gray-300 to-gray-500' : 'bg-gradient-to-r from-amber-500 to-amber-700') }}">
                                            @if($index === 0) 🥇 @elseif($index === 1) 🥈 @else 🥉 @endif
                                        </div>
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 font-medium text-sm">
                                            {{ $index + 1 }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold text-sm shadow-md">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mb-1
                                        {{ $user->role === 'admin' ? 'bg-red-100 text-red-800' : 
                                           ($user->role === 'librarian' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800') }}">
                                        {{ $user->role_label }}
                                    </span>
                                    <span class="text-xs text-gray-600">{{ $user->branch_name ?? 'Pusat' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mb-1">
                                        {{ $user->login_days }} hari
                                    </span>
                                    <span class="text-xs text-gray-500">{{ $user->login_count }} kali</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ number_format($user->total_activities) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    {{ $user->modules_used }} modul
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($user->last_activity)
                                    <div class="flex flex-col">
                                        <span>{{ \Carbon\Carbon::parse($user->last_activity)->diffForHumans() }}</span>
                                        <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($user->last_activity)->format('d/m H:i') }}</span>
                                    </div>
                                @else
                                    <span class="text-gray-400">Tidak ada aktivitas</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="text-sm font-bold text-gray-900 mr-3">{{ number_format($user->activity_score) }}</div>
                                    <div class="flex-1 bg-gray-200 rounded-full h-2 w-20">
                                        <div class="bg-gradient-to-r from-green-400 to-blue-500 h-2 rounded-full transition-all duration-300" 
                                             style="width: {{ $rankings->count() > 0 ? min(100, ($user->activity_score / ($rankings->first()->activity_score ?? 1)) * 100) : 0 }}%"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada data aktivitas</h3>
                                    <p class="text-gray-500">Belum ada aktivitas staff dalam periode {{ $timeRange }} hari terakhir</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
