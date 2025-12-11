@extends('staff.layouts.app')

@section('title', 'Katalog Bibliografi')

@section('content')
<div class="space-y-5">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-500/25">
                <i class="fas fa-book text-xl"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Katalog Bibliografi</h1>
                <p class="text-sm text-gray-500">{{ $books->total() }} judul ditemukan</p>
            </div>
        </div>
        <a href="{{ route('staff.biblio.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm font-semibold rounded-xl shadow-lg shadow-blue-500/25 hover:shadow-xl transition-all">
            <i class="fas fa-plus"></i>
            Tambah Bibliografi
        </a>
    </div>

    <!-- Search -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
        <form method="GET" class="flex gap-3">
            <div class="relative flex-1">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari judul, ISBN, atau no. panggil..."
                       class="w-full pl-11 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <button type="submit" class="px-5 py-2.5 bg-gray-900 text-white text-sm font-semibold rounded-xl hover:bg-gray-800 transition">
                Cari
            </button>
            @if(request('search'))
            <a href="{{ route('staff.biblio.index') }}" class="px-4 py-2.5 bg-gray-100 text-gray-600 text-sm font-semibold rounded-xl hover:bg-gray-200 transition">
                Reset
            </a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="text-left text-xs text-gray-500 uppercase tracking-wider bg-gray-50">
                        <th class="px-5 py-4 font-semibold">Judul</th>
                        <th class="px-5 py-4 font-semibold">ISBN</th>
                        <th class="px-5 py-4 font-semibold">No. Panggil</th>
                        <th class="px-5 py-4 font-semibold text-center">Eks</th>
                        <th class="px-5 py-4 font-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($books as $book)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-14 bg-gradient-to-br from-slate-100 to-slate-200 rounded-lg flex items-center justify-center flex-shrink-0 overflow-hidden">
                                    @if($book->image)
                                        <img src="{{ asset('storage/'.$book->image) }}" class="w-full h-full object-cover">
                                    @else
                                        <i class="fas fa-book text-slate-400"></i>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <a href="{{ route('staff.biblio.show', $book) }}" class="font-semibold text-gray-900 hover:text-blue-600 line-clamp-1">{{ $book->title }}</a>
                                    <p class="text-xs text-gray-500">{{ $book->authors->pluck('name')->implode(', ') ?: '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-sm text-gray-600 font-mono">{{ $book->isbn ?: '-' }}</td>
                        <td class="px-5 py-4 text-sm text-gray-600 font-mono">{{ $book->call_number ?: '-' }}</td>
                        <td class="px-5 py-4 text-center">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-sm font-bold {{ $book->items_count > 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $book->items_count }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('staff.biblio.show', $book) }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-blue-600 hover:bg-blue-50 transition" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('staff.biblio.edit', $book) }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-amber-600 hover:bg-amber-50 transition" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-16 text-center">
                            <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-book text-gray-300 text-2xl"></i>
                            </div>
                            <p class="text-gray-500 font-medium">Tidak ada bibliografi ditemukan</p>
                            <a href="{{ route('staff.biblio.create') }}" class="inline-flex items-center gap-2 mt-4 text-sm text-blue-600 hover:text-blue-700 font-medium">
                                <i class="fas fa-plus"></i> Tambah Bibliografi Baru
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($books->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $books->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
