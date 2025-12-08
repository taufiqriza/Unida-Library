<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Submission Info --}}
        {{ $this->submissionInfolist }}

        {{-- History Log --}}
        <x-filament::section collapsible>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-clock class="w-5 h-5" />
                    Riwayat Aktivitas
                </div>
            </x-slot>
            <x-slot name="description">
                Timeline perubahan status submission
            </x-slot>

            <div class="relative">
                {{-- Timeline line --}}
                <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200 dark:bg-gray-700"></div>
                
                <div class="space-y-4">
                    @forelse($record->logs()->with(['user', 'member'])->latest()->get() as $log)
                        <div class="relative flex items-start gap-4 pl-10">
                            {{-- Timeline dot --}}
                            <div class="absolute left-0 flex items-center justify-center">
                                @switch($log->action)
                                    @case('submitted')
                                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center ring-4 ring-white dark:ring-gray-900">
                                            <x-heroicon-o-paper-airplane class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                        </div>
                                        @break
                                    @case('review_started')
                                        <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900 rounded-full flex items-center justify-center ring-4 ring-white dark:ring-gray-900">
                                            <x-heroicon-o-eye class="w-4 h-4 text-yellow-600 dark:text-yellow-400" />
                                        </div>
                                        @break
                                    @case('approved')
                                        <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center ring-4 ring-white dark:ring-gray-900">
                                            <x-heroicon-o-check-circle class="w-4 h-4 text-green-600 dark:text-green-400" />
                                        </div>
                                        @break
                                    @case('rejected')
                                        <div class="w-8 h-8 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center ring-4 ring-white dark:ring-gray-900">
                                            <x-heroicon-o-x-circle class="w-4 h-4 text-red-600 dark:text-red-400" />
                                        </div>
                                        @break
                                    @case('revision_requested')
                                        <div class="w-8 h-8 bg-orange-100 dark:bg-orange-900 rounded-full flex items-center justify-center ring-4 ring-white dark:ring-gray-900">
                                            <x-heroicon-o-pencil-square class="w-4 h-4 text-orange-600 dark:text-orange-400" />
                                        </div>
                                        @break
                                    @case('published')
                                        <div class="w-8 h-8 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center ring-4 ring-white dark:ring-gray-900">
                                            <x-heroicon-o-globe-alt class="w-4 h-4 text-primary-600 dark:text-primary-400" />
                                        </div>
                                        @break
                                    @default
                                        <div class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center ring-4 ring-white dark:ring-gray-900">
                                            <x-heroicon-o-clock class="w-4 h-4 text-gray-600 dark:text-gray-400" />
                                        </div>
                                @endswitch
                            </div>

                            {{-- Content --}}
                            <div class="flex-1 min-w-0 pb-4">
                                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-1">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                            {{ $log->action_label }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $log->created_at->format('d M Y H:i') }}
                                        </p>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        oleh {{ $log->actor_name }}
                                    </p>
                                    @if($log->notes)
                                        <div class="mt-2 p-3 bg-white dark:bg-gray-700 rounded-md border border-gray-200 dark:border-gray-600">
                                            <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $log->notes }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <x-heroicon-o-clock class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-3" />
                            <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada riwayat aktivitas</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
