<x-filament-panels::page>
    <div class="space-y-6" x-data="{ previewUrl: null, previewTitle: '' }">
        {{-- Submission Info --}}
        {{ $this->submissionInfolist }}

        {{-- Document Preview Section --}}
        <x-filament::section collapsible>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-document class="w-5 h-5" />
                    Preview Dokumen
                </div>
            </x-slot>
            <x-slot name="description">
                Klik untuk preview dokumen di modal
            </x-slot>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                {{-- Cover --}}
                <div class="text-center">
                    <div class="w-full aspect-[3/4] bg-gray-100 dark:bg-gray-800 rounded-lg overflow-hidden mb-2 border">
                        @if($record->cover_file)
                            <img src="{{ route('admin.thesis.file', [$record, 'cover']) }}" alt="Cover" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <x-heroicon-o-photo class="w-12 h-12 text-gray-300" />
                            </div>
                        @endif
                    </div>
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Cover</p>
                    @if($record->cover_file)
                        <span class="text-xs text-green-600">✓ Ada</span>
                    @else
                        <span class="text-xs text-gray-400">Tidak ada</span>
                    @endif
                </div>

                {{-- Approval --}}
                <div class="text-center">
                    <div class="w-full aspect-[3/4] bg-gray-100 dark:bg-gray-800 rounded-lg overflow-hidden mb-2 border flex items-center justify-center">
                        @if($record->approval_file)
                            <button 
                                type="button"
                                x-on:click="previewUrl = '{{ route('admin.thesis.file', [$record, 'approval']) }}'; previewTitle = 'Lembar Pengesahan'"
                                class="w-full h-full flex flex-col items-center justify-center hover:bg-gray-200 dark:hover:bg-gray-700 transition cursor-pointer"
                            >
                                <x-heroicon-o-document-text class="w-12 h-12 text-red-500 mb-2" />
                                <span class="text-xs text-primary-600 font-medium">Klik Preview</span>
                            </button>
                        @else
                            <x-heroicon-o-document class="w-12 h-12 text-gray-300" />
                        @endif
                    </div>
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Pengesahan</p>
                    @if($record->approval_file)
                        <span class="text-xs text-green-600">✓ Ada</span>
                    @else
                        <span class="text-xs text-gray-400">Tidak ada</span>
                    @endif
                </div>

                {{-- Preview (BAB 1-3) --}}
                <div class="text-center">
                    <div class="w-full aspect-[3/4] bg-gray-100 dark:bg-gray-800 rounded-lg overflow-hidden mb-2 border flex items-center justify-center">
                        @if($record->preview_file)
                            <button 
                                type="button"
                                x-on:click="previewUrl = '{{ route('admin.thesis.file', [$record, 'preview']) }}'; previewTitle = 'BAB 1-3 (Preview)'"
                                class="w-full h-full flex flex-col items-center justify-center hover:bg-gray-200 dark:hover:bg-gray-700 transition cursor-pointer"
                            >
                                <x-heroicon-o-document-text class="w-12 h-12 text-blue-500 mb-2" />
                                <span class="text-xs text-primary-600 font-medium">Klik Preview</span>
                            </button>
                        @else
                            <x-heroicon-o-document class="w-12 h-12 text-gray-300" />
                        @endif
                    </div>
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">BAB 1-3</p>
                    @if($record->preview_file)
                        <span class="text-xs text-green-600">✓ Ada</span>
                    @else
                        <span class="text-xs text-gray-400">Tidak ada</span>
                    @endif
                </div>

                {{-- Full Text --}}
                <div class="text-center">
                    <div class="w-full aspect-[3/4] bg-gray-100 dark:bg-gray-800 rounded-lg overflow-hidden mb-2 border flex items-center justify-center">
                        @if($record->fulltext_file)
                            <button 
                                type="button"
                                x-on:click="previewUrl = '{{ route('admin.thesis.file', [$record, 'fulltext']) }}'; previewTitle = 'Full Text'"
                                class="w-full h-full flex flex-col items-center justify-center hover:bg-gray-200 dark:hover:bg-gray-700 transition cursor-pointer"
                            >
                                <x-heroicon-o-document-text class="w-12 h-12 text-emerald-500 mb-2" />
                                <span class="text-xs text-primary-600 font-medium">Klik Preview</span>
                            </button>
                        @else
                            <x-heroicon-o-document class="w-12 h-12 text-gray-300" />
                        @endif
                    </div>
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Full Text</p>
                    @if($record->fulltext_file)
                        <span class="text-xs text-green-600">✓ Ada</span>
                    @else
                        <span class="text-xs text-gray-400">Tidak ada</span>
                    @endif
                </div>
            </div>
        </x-filament::section>

        {{-- PDF Preview Modal --}}
        <div 
            x-show="previewUrl" 
            x-cloak 
            @keydown.escape.window="previewUrl = null"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70"
        >
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-2xl w-full max-w-5xl h-[90vh] flex flex-col">
                {{-- Modal Header --}}
                <div class="flex items-center justify-between p-4 border-b dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="previewTitle"></h3>
                    <div class="flex items-center gap-2">
                        <a :href="previewUrl" target="_blank" class="px-3 py-1.5 text-sm bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">
                            <span class="flex items-center gap-1">
                                <x-heroicon-o-arrow-top-right-on-square class="w-4 h-4" />
                                Buka Tab Baru
                            </span>
                        </a>
                        <button @click="previewUrl = null" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition">
                            <x-heroicon-o-x-mark class="w-5 h-5 text-gray-500" />
                        </button>
                    </div>
                </div>
                {{-- Modal Body --}}
                <div class="flex-1 p-2">
                    <iframe :src="previewUrl" class="w-full h-full rounded-lg border dark:border-gray-700"></iframe>
                </div>
            </div>
        </div>
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
