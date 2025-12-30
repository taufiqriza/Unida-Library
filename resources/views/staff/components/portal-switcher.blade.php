{{-- Portal Switcher --}}
@php
    $user = auth()->user();
    $memberAccount = \App\Models\Member::withoutGlobalScope('branch')->where('email', $user->email)->first();
    $isSuperAdmin = $user->role === 'super_admin';
@endphp

<div class="flex items-center gap-2">
    {{-- Admin Panel Switcher - Only for super_admin --}}
    @if($isSuperAdmin)
    <a href="{{ route('filament.admin.pages.dashboard') }}" 
       class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-violet-50 hover:bg-violet-100 border border-violet-200 text-violet-700 text-sm font-medium transition"
       title="Beralih ke Admin Panel">
        <i class="fas fa-shield-halved"></i>
        <span class="hidden xl:inline">Admin</span>
    </a>
    @endif

    {{-- Member Portal Switcher - If user has member account --}}
    @if($memberAccount)
    <a href="{{ route('auth.switch-portal', 'member') }}" 
       class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-emerald-700 text-sm font-medium transition"
       title="Beralih ke Member Portal">
        <i class="fas fa-exchange-alt"></i>
        <span class="hidden xl:inline">Member</span>
    </a>
    @endif
</div>
