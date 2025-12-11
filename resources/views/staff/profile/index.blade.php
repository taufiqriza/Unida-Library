@extends('staff.layouts.app')

@section('title', 'Profil')

@section('content')
@php $user = auth()->user(); @endphp

<div class="max-w-2xl mx-auto space-y-6">
    {{-- Profile Card --}}
    <div class="section-card">
        <div class="section-header">
            <i class="fas fa-user"></i>
            <span>Informasi Profil</span>
        </div>
        <div class="p-6">
            <div class="flex items-center gap-6 mb-6">
                <div class="w-20 h-20 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-2xl font-bold">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-900">{{ $user->name }}</h2>
                    <p class="text-slate-500">{{ $user->email }}</p>
                    <span class="badge badge-info mt-2">{{ ucfirst($user->role) }}</span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-slate-500">Cabang</p>
                    <p class="font-medium text-slate-900">{{ $user->branch?->name ?? 'Semua Cabang' }}</p>
                </div>
                <div>
                    <p class="text-slate-500">Bergabung</p>
                    <p class="font-medium text-slate-900">{{ $user->created_at?->format('d M Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Google Account Link --}}
    <div class="section-card">
        <div class="section-header">
            <i class="fab fa-google"></i>
            <span>Hubungkan Google Account</span>
        </div>
        <div class="p-6">
            @php
                $googleAccount = $user->socialAccounts?->where('provider', 'google')->first();
            @endphp

            @if($googleAccount)
                <div class="flex items-center gap-4 p-4 bg-green-50 rounded-xl border border-green-200">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fab fa-google text-green-600"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-green-800">Terhubung</p>
                        <p class="text-sm text-green-600">{{ $googleAccount->provider_email }}</p>
                    </div>
                    <form action="#" method="POST">
                        @csrf
                        <button type="button" class="text-sm text-red-600 hover:text-red-700">Putuskan</button>
                    </form>
                </div>
            @else
                <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-xl border border-slate-200">
                    <div class="w-10 h-10 bg-slate-100 rounded-full flex items-center justify-center">
                        <i class="fab fa-google text-slate-400"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-slate-700">Belum Terhubung</p>
                        <p class="text-sm text-slate-500">Hubungkan untuk login lebih mudah</p>
                    </div>
                    <a href="{{ route('auth.google') }}?link_staff=1" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                        Hubungkan
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- Change Password --}}
    <div class="section-card">
        <div class="section-header">
            <i class="fas fa-key"></i>
            <span>Ubah Password</span>
        </div>
        <div class="p-6">
            <form action="#" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Password Lama</label>
                    <input type="password" name="current_password" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Password Baru</label>
                    <input type="password" name="password" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                    Simpan Password
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
