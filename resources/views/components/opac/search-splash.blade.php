{{-- Search Loading Splash Component --}}
<div id="searchSplash" class="fixed inset-0 z-[9999] hidden">
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-gradient-to-br from-blue-900/95 via-blue-800/95 to-indigo-900/95 backdrop-blur-sm"></div>
    
    {{-- Content --}}
    <div class="relative h-full flex flex-col items-center justify-center px-4">
        {{-- Animated rings --}}
        <div class="relative w-32 h-32 mb-8">
            <div class="absolute inset-0 border-4 border-blue-400/30 rounded-full animate-ping"></div>
            <div class="absolute inset-2 border-4 border-blue-300/40 rounded-full animate-ping" style="animation-delay: 0.2s"></div>
            <div class="absolute inset-4 border-4 border-white/50 rounded-full animate-ping" style="animation-delay: 0.4s"></div>
            
            {{-- Center icon --}}
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-2xl shadow-blue-500/50">
                    <i class="fas fa-search text-2xl text-blue-600 animate-pulse"></i>
                </div>
            </div>
        </div>
        
        {{-- Search query display --}}
        <div class="text-center">
            <p class="text-blue-200 text-sm mb-2">Mencari</p>
            <p id="splashQuery" class="text-white text-xl font-semibold mb-6 max-w-md truncate px-4">"..."</p>
        </div>
        
        {{-- Progress bar --}}
        <div class="w-64 h-1.5 bg-blue-900/50 rounded-full overflow-hidden">
            <div id="splashProgress" class="h-full bg-gradient-to-r from-blue-400 via-white to-blue-400 rounded-full" style="width: 0%; transition: width 0.3s ease-out"></div>
        </div>
        
        {{-- Status text --}}
        <p id="splashStatus" class="text-blue-300 text-xs mt-4">Mempersiapkan pencarian...</p>
        
        {{-- Floating particles --}}
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-1/4 left-1/4 w-2 h-2 bg-blue-400/30 rounded-full animate-float"></div>
            <div class="absolute top-1/3 right-1/4 w-3 h-3 bg-white/20 rounded-full animate-float" style="animation-delay: 1s"></div>
            <div class="absolute bottom-1/3 left-1/3 w-2 h-2 bg-blue-300/30 rounded-full animate-float" style="animation-delay: 2s"></div>
            <div class="absolute bottom-1/4 right-1/3 w-1.5 h-1.5 bg-white/30 rounded-full animate-float" style="animation-delay: 0.5s"></div>
        </div>
    </div>
</div>

<style>
@keyframes float {
    0%, 100% { transform: translateY(0) scale(1); opacity: 0.5; }
    50% { transform: translateY(-20px) scale(1.2); opacity: 1; }
}
.animate-float { animation: float 3s ease-in-out infinite; }
</style>

<script>
window.SearchSplash = {
    el: null,
    progress: null,
    query: null,
    status: null,
    interval: null,
    
    init() {
        this.el = document.getElementById('searchSplash');
        this.progress = document.getElementById('splashProgress');
        this.query = document.getElementById('splashQuery');
        this.status = document.getElementById('splashStatus');
    },
    
    show(searchQuery) {
        if (!this.el) this.init();
        
        this.query.textContent = `"${searchQuery || '...'}"`;
        this.progress.style.width = '0%';
        this.status.textContent = 'Mempersiapkan pencarian...';
        
        this.el.classList.remove('hidden');
        this.el.style.opacity = '0';
        requestAnimationFrame(() => {
            this.el.style.transition = 'opacity 0.3s ease-out';
            this.el.style.opacity = '1';
        });
        
        this.animateProgress();
    },
    
    animateProgress() {
        const statuses = [
            { pct: 20, text: 'Mengakses database...' },
            { pct: 45, text: 'Mencari koleksi buku...' },
            { pct: 65, text: 'Memproses hasil...' },
            { pct: 85, text: 'Menyiapkan tampilan...' }
        ];
        
        let step = 0;
        this.interval = setInterval(() => {
            if (step < statuses.length) {
                this.progress.style.width = statuses[step].pct + '%';
                this.status.textContent = statuses[step].text;
                step++;
            }
        }, 400);
    },
    
    complete() {
        if (this.interval) clearInterval(this.interval);
        this.progress.style.width = '100%';
        this.status.textContent = 'Selesai!';
    },
    
    hide() {
        if (!this.el) return;
        if (this.interval) clearInterval(this.interval);
        
        this.el.style.opacity = '0';
        setTimeout(() => {
            this.el.classList.add('hidden');
        }, 300);
    }
};
</script>
