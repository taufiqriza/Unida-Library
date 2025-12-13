{{-- Portal Switcher - Only show if user has member account --}}
@php
    $memberAccount = \App\Models\Member::where('email', auth()->user()->email)->first();
@endphp

@if($memberAccount)
<a href="{{ route('auth.switch-portal', 'member') }}" 
   class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-emerald-700 text-sm font-medium transition"
   title="Beralih ke Member Portal">
    <i class="fas fa-exchange-alt"></i>
    <span class="hidden xl:inline">Member</span>
</a>
@endif
