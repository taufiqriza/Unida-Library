@extends('staff.layouts.app')

@section('title', $book ? 'Edit Bibliografi' : 'Tambah Bibliografi')

@section('content')
<div class="space-y-5 max-w-4xl">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ $book ? route('staff.biblio.show', $book) : route('staff.biblio.index') }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $book ? 'Edit Bibliografi' : 'Tambah Bibliografi' }}</h1>
            <p class="text-sm text-gray-500">{{ $book ? 'Perbarui data bibliografi' : 'Masukkan data bibliografi baru' }}</p>
        </div>
    </div>

    <form action="{{ $book ? route('staff.biblio.update', $book) : route('staff.biblio.store') }}" method="POST" class="space-y-5">
        @csrf
        @if($book) @method('PUT') @endif

        <!-- Main Info -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-info-circle text-blue-500"></i> Informasi Utama
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $book?->title) }}" required
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('title') border-red-500 @enderror">
                    @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ISBN</label>
                    <input type="text" name="isbn" value="{{ old('isbn', $book?->isbn) }}"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Penerbit</label>
                    <select name="publisher_id" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">-- Pilih Penerbit --</option>
                        @foreach($publishers as $pub)
                            <option value="{{ $pub->id }}" {{ old('publisher_id', $book?->publisher_id) == $pub->id ? 'selected' : '' }}>{{ $pub->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Terbit</label>
                    <input type="text" name="publish_year" value="{{ old('publish_year', $book?->publish_year) }}" maxlength="4"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Klasifikasi</label>
                    <input type="text" name="classification" value="{{ old('classification', $book?->classification) }}"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Panggil</label>
                    <input type="text" name="call_number" value="{{ old('call_number', $book?->call_number) }}"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                @if(!$book)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Eksemplar</label>
                    <input type="number" name="item_qty" value="{{ old('item_qty', 1) }}" min="0" max="100"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">Eksemplar akan dibuat otomatis dengan barcode</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Authors & Subjects -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-users text-purple-500"></i> Penulis & Subjek
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Penulis</label>
                    <select name="authors[]" multiple class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent h-32">
                        @foreach($authors as $author)
                            <option value="{{ $author->id }}" {{ in_array($author->id, old('authors', $book?->authors->pluck('id')->toArray() ?? [])) ? 'selected' : '' }}>{{ $author->name }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Ctrl+Click untuk pilih multiple</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subjek</label>
                    <select name="subjects[]" multiple class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent h-32">
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ in_array($subject->id, old('subjects', $book?->subjects->pluck('id')->toArray() ?? [])) ? 'selected' : '' }}>{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex items-center gap-3">
            <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-semibold rounded-xl shadow-lg shadow-blue-500/25 hover:shadow-xl transition">
                <i class="fas fa-save mr-2"></i> {{ $book ? 'Update' : 'Simpan' }}
            </button>
            <a href="{{ $book ? route('staff.biblio.show', $book) : route('staff.biblio.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
