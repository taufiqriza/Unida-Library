<div>
    {{-- Modal Reader --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-2 lg:p-4 bg-black/80 backdrop-blur-sm"
         x-data="{ fontSize: 20 }"
         @keydown.escape.window="$wire.closeModal()"
         @keydown.arrow-right.window="$wire.nextPage()"
         @keydown.arrow-left.window="$wire.prevPage()">
        
        {{-- Modal Content --}}
        <div class="relative w-full max-w-5xl h-[95vh] bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col"
             @click.away="$wire.closeModal()">
            
            {{-- Header --}}
            <div class="flex items-center justify-between px-4 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white flex-shrink-0">
                <div class="flex items-center gap-3 min-w-0 flex-1">
                    <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-book-quran"></i>
                    </div>
                    <div class="min-w-0" dir="rtl">
                        @if($bookInfo)
                        <h3 class="font-bold text-base line-clamp-1">{{ $bookInfo['title'] }}</h3>
                        <p class="text-blue-200 text-xs">{{ $bookInfo['author'] ?? 'المكتبة الشاملة' }}</p>
                        @else
                        <h3 class="font-bold">جارٍ التحميل...</h3>
                        @endif
                    </div>
                </div>
                
                {{-- Font Size Controls --}}
                <div class="flex items-center gap-2 mr-4">
                    <button @click="fontSize = Math.max(14, fontSize - 2)" class="w-8 h-8 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center transition">
                        <i class="fas fa-minus text-xs"></i>
                    </button>
                    <span class="text-xs w-8 text-center" x-text="fontSize + 'px'"></span>
                    <button @click="fontSize = Math.min(32, fontSize + 2)" class="w-8 h-8 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center transition">
                        <i class="fas fa-plus text-xs"></i>
                    </button>
                </div>
                
                {{-- Close Button --}}
                <button wire:click="closeModal" class="w-10 h-10 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            {{-- Page Navigation Top --}}
            <div class="flex items-center justify-between px-4 py-2 bg-gray-100 border-b border-gray-200 flex-shrink-0">
                <button wire:click="prevPage" 
                        class="px-3 py-1.5 bg-white rounded-lg shadow-sm hover:bg-gray-50 transition flex items-center gap-2 text-sm {{ $currentPage <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}"
                        {{ $currentPage <= 1 ? 'disabled' : '' }}>
                    <i class="fas fa-chevron-right"></i>
                    <span class="hidden sm:inline">السابق</span>
                </button>
                
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-600">صفحة</span>
                    <input type="number" 
                           wire:model.defer="currentPage"
                           wire:keydown.enter="loadPage"
                           min="1" 
                           max="{{ $totalPages }}"
                           class="w-16 px-2 py-1 text-center border rounded-lg text-sm">
                    <span class="text-sm text-gray-600">من {{ number_format($totalPages) }}</span>
                </div>
                
                <button wire:click="nextPage" 
                        class="px-3 py-1.5 bg-white rounded-lg shadow-sm hover:bg-gray-50 transition flex items-center gap-2 text-sm {{ $currentPage >= $totalPages ? 'opacity-50 cursor-not-allowed' : '' }}"
                        {{ $currentPage >= $totalPages ? 'disabled' : '' }}>
                    <span class="hidden sm:inline">التالي</span>
                    <i class="fas fa-chevron-left"></i>
                </button>
            </div>
            
            {{-- Content Area --}}
            <div class="flex-1 overflow-y-auto bg-amber-50/30" dir="rtl">
                @if($loading)
                {{-- Loading --}}
                <div class="flex items-center justify-center h-full">
                    <div class="text-center">
                        <div class="w-12 h-12 border-4 border-blue-500 border-t-transparent rounded-full animate-spin mx-auto mb-3"></div>
                        <p class="text-gray-600">جارٍ تحميل الصفحة...</p>
                    </div>
                </div>
                @elseif($error)
                {{-- Error --}}
                <div class="flex items-center justify-center h-full">
                    <div class="text-center p-6">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
                        </div>
                        <h4 class="font-bold text-gray-900 mb-2">خطأ</h4>
                        <p class="text-gray-600">{{ $error }}</p>
                    </div>
                </div>
                @elseif($pageData)
                {{-- Page Content --}}
                <div class="p-6 lg:p-10 max-w-4xl mx-auto" 
                     :style="'font-size: ' + fontSize + 'px'"
                     style="font-family: 'Amiri', 'Traditional Arabic', serif; line-height: 2;">
                    
                    {{-- Main Content --}}
                    <div class="prose prose-lg max-w-none text-gray-900 shamela-content select-none"
                         x-on:contextmenu.prevent=""
                         x-on:copy.prevent="">
                        {!! nl2br(e($pageData['body'])) !!}
                    </div>
                    
                    {{-- Footnotes --}}
                    @if(!empty($pageData['foot']))
                    <div class="mt-8 pt-6 border-t-2 border-blue-200">
                        <h4 class="font-bold text-blue-700 mb-4 flex items-center gap-2">
                            <i class="fas fa-sticky-note"></i>
                            الحواشي
                        </h4>
                        <div class="text-gray-600 shamela-footnotes" style="font-size: 0.9em;">
                            {!! nl2br(e($pageData['foot'])) !!}
                        </div>
                    </div>
                    @endif
                </div>
                @else
                {{-- Empty --}}
                <div class="flex items-center justify-center h-full">
                    <p class="text-gray-500">لا يوجد محتوى لهذه الصفحة</p>
                </div>
                @endif
            </div>
            
            {{-- Footer --}}
            <div class="px-4 py-2 bg-gray-100 border-t border-gray-200 flex items-center justify-between flex-shrink-0">
                <div class="flex items-center gap-2 text-xs text-gray-500">
                    <i class="fas fa-keyboard"></i>
                    <span class="hidden sm:inline">استخدم الأسهم للتنقل | ESC للإغلاق</span>
                </div>
                
                <div class="flex items-center gap-2">
                    {{-- Watermark --}}
                    <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs rounded-full">
                        <i class="fas fa-book-quran mr-1"></i>
                        المكتبة الشاملة - قراءة فقط
                    </span>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    {{-- Anti-copy CSS --}}
    <style>
        .shamela-content, .shamela-footnotes {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        
        .shamela-content::selection, .shamela-footnotes::selection {
            background: transparent;
        }
        
        @media print {
            .shamela-content, .shamela-footnotes {
                display: none !important;
            }
        }
    </style>
    
    {{-- Load Amiri Arabic Font --}}
    <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
</div>
