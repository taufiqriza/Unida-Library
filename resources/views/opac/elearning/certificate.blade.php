<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat - {{ $cert->certificate_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@400;500;600&display=swap');
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen py-8">
    {{-- Print Button --}}
    <div class="no-print max-w-4xl mx-auto mb-4 px-4 flex gap-3">
        <button onclick="window.print()" class="px-6 py-2.5 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700 transition flex items-center gap-2">
            <i class="fas fa-print"></i> Cetak Sertifikat
        </button>
        <a href="{{ url()->previous() }}" class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- Certificate --}}
    <div class="max-w-4xl mx-auto bg-white shadow-2xl" style="aspect-ratio: 1.414;">
        <div class="relative w-full h-full p-8 overflow-hidden">
            {{-- Border Design --}}
            <div class="absolute inset-4 border-4 border-blue-600 rounded-lg"></div>
            <div class="absolute inset-6 border-2 border-blue-300 rounded-lg"></div>
            
            {{-- Corner Decorations --}}
            <div class="absolute top-8 left-8 w-16 h-16 border-t-4 border-l-4 border-amber-500 rounded-tl-lg"></div>
            <div class="absolute top-8 right-8 w-16 h-16 border-t-4 border-r-4 border-amber-500 rounded-tr-lg"></div>
            <div class="absolute bottom-8 left-8 w-16 h-16 border-b-4 border-l-4 border-amber-500 rounded-bl-lg"></div>
            <div class="absolute bottom-8 right-8 w-16 h-16 border-b-4 border-r-4 border-amber-500 rounded-br-lg"></div>

            {{-- Content --}}
            <div class="relative h-full flex flex-col items-center justify-center text-center px-12">
                {{-- Logo --}}
                <div class="mb-4">
                    <img src="{{ asset('storage/logo-portal.png') }}" alt="Logo" class="h-16 mx-auto">
                </div>

                {{-- Title --}}
                <h1 class="text-3xl font-bold text-blue-800 tracking-wider mb-1" style="font-family: 'Playfair Display', serif;">
                    SERTIFIKAT
                </h1>
                <p class="text-gray-500 text-sm tracking-widest mb-6">CERTIFICATE OF COMPLETION</p>

                {{-- Recipient --}}
                <p class="text-gray-600 mb-2">Diberikan kepada:</p>
                <h2 class="text-4xl font-bold text-gray-900 mb-6" style="font-family: 'Playfair Display', serif;">
                    {{ $cert->member_name }}
                </h2>

                {{-- Description --}}
                <p class="text-gray-600 max-w-lg mb-2">
                    Telah berhasil menyelesaikan kelas:
                </p>
                <h3 class="text-xl font-bold text-blue-700 mb-6 max-w-lg">
                    "{{ $cert->course_title }}"
                </h3>

                {{-- Date --}}
                <p class="text-gray-500 text-sm mb-8">
                    Diterbitkan pada {{ $cert->issued_at->translatedFormat('d F Y') }}
                </p>

                {{-- Signature Area --}}
                <div class="flex items-end justify-center gap-24 mt-auto">
                    <div class="text-center">
                        <div class="w-32 border-b-2 border-gray-400 mb-2"></div>
                        <p class="text-sm font-semibold text-gray-700">{{ $cert->enrollment->course->instructor->name ?? 'Instruktur' }}</p>
                        <p class="text-xs text-gray-500">Instruktur</p>
                    </div>
                </div>

                {{-- Certificate Number --}}
                <div class="absolute bottom-10 left-1/2 -translate-x-1/2">
                    <p class="text-xs text-gray-400">No: {{ $cert->certificate_number }}</p>
                </div>

                {{-- QR Placeholder --}}
                <div class="absolute bottom-10 right-12">
                    <div class="w-16 h-16 bg-gray-100 rounded flex items-center justify-center">
                        <i class="fas fa-qrcode text-gray-300 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
