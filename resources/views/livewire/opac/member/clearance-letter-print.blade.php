<div>
    <div class="min-h-screen bg-gray-100 py-8">
        {{-- Print Controls --}}
        <div class="no-print max-w-4xl mx-auto mb-4 px-4">
            <div class="bg-white rounded-xl shadow-sm p-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <a href="{{ route('opac.member.dashboard') }}" class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center hover:bg-gray-200 transition">
                        <i class="fas fa-arrow-left text-gray-600"></i>
                    </a>
                    <div>
                        <p class="font-semibold text-gray-900">Surat Bebas Pustaka</p>
                        <p class="text-xs text-gray-500">{{ $letter->letter_number }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('opac.member.clearance-letter.download', $letter) }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                        <i class="fas fa-file-pdf"></i> Download PDF
                    </a>
                    <button onclick="window.print()" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition flex items-center gap-2">
                        <i class="fas fa-print"></i> Cetak
                    </button>
                </div>
            </div>
        </div>
        
        {{-- Letter Content --}}
        <div class="max-w-4xl mx-auto bg-white shadow-xl print:shadow-none" style="width: 210mm; min-height: 297mm; padding: 15mm 20mm; box-sizing: border-box; font-family: 'Times New Roman', Times, serif;">
            {{-- Header --}}
            <div class="border-b-4 border-blue-800 pb-4 mb-6">
                <table class="w-full">
                    <tr>
                        <td class="w-20 align-middle">
                            <img src="{{ asset('storage/logo-portal.png') }}" alt="Logo" class="w-16 h-16 object-contain" onerror="this.style.display='none'">
                        </td>
                        <td class="text-center align-middle">
                            <h1 class="text-xl font-bold text-blue-800 uppercase tracking-wide">PERPUSTAKAAN</h1>
                            <p class="text-base font-semibold text-gray-700">Universitas Darussalam Gontor</p>
                            <p class="text-xs text-gray-500 mt-1">Jl. Raya Siman KM. 6 Ponorogo 63471 Jawa Timur</p>
                            <p class="text-xs text-gray-500">Email: library@unida.gontor.ac.id | WA: +62 821-1704-9501</p>
                        </td>
                        <td class="w-20 text-right align-top">
                            <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 text-xs font-bold border border-blue-200">Receipt 3</span>
                        </td>
                    </tr>
                </table>
            </div>
            
            {{-- Title --}}
            <div class="text-center mb-6">
                <h2 class="text-base font-bold uppercase underline">Surat Keterangan Bebas Pustaka</h2>
                <p class="text-sm text-gray-600 mt-1">Nomor: {{ $letter->letter_number }}</p>
            </div>
            
            {{-- Body --}}
            <div class="text-justify leading-relaxed mb-6" style="font-size: 13px;">
                <p class="mb-4">Yang bertanda tangan di bawah ini, Perpustakaan UNIDA Gontor menerangkan bahwa:</p>
                
                <table class="w-full mb-4">
                    <tr><td class="py-1 w-32">Nama</td><td class="py-1 w-4">:</td><td class="py-1 font-semibold">{{ $letter->member->name }}</td></tr>
                    <tr><td class="py-1">NIM</td><td class="py-1">:</td><td class="py-1 font-semibold font-mono">{{ $letter->member->member_id }}</td></tr>
                    @if($letter->thesisSubmission)
                    <tr><td class="py-1">Program Studi</td><td class="py-1">:</td><td class="py-1">{{ $letter->thesisSubmission->department?->name ?? '-' }}</td></tr>
                    <tr><td class="py-1">Fakultas</td><td class="py-1">:</td><td class="py-1">{{ $letter->thesisSubmission->department?->faculty?->name ?? '-' }}</td></tr>
                    @endif
                </table>
                
                <p class="mb-3">Adalah benar mahasiswa tersebut telah memenuhi persyaratan administrasi perpustakaan:</p>
                
                <div class="bg-green-50 p-3 rounded border border-green-200 mb-4">
                    <p class="mb-2"><span class="font-bold text-green-700">1.</span> <strong>Bebas Peminjaman Buku</strong> — Tidak memiliki tanggungan peminjaman.</p>
                    <p><span class="font-bold text-green-700">2.</span> <strong>Telah Mengunggah Tugas Akhir</strong> — Karya ilmiah telah diunggah ke repositori.</p>
                </div>
                
                @if($letter->thesisSubmission)
                <p class="mb-2">Karya ilmiah yang telah diunggah:</p>
                <div class="bg-blue-50 border-l-4 border-blue-500 p-3 mb-4">
                    <p class="font-semibold italic">"{{ $letter->thesisSubmission->title }}"</p>
                    <p class="text-sm text-gray-600 mt-1">
                        <span class="inline-block px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs font-medium mr-2">{{ ucfirst($letter->thesisSubmission->type) }}</span>
                        Tahun {{ $letter->thesisSubmission->year }}
                    </p>
                </div>
                @endif
                
                <p>Demikian surat keterangan ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.</p>
            </div>
            
            {{-- Date --}}
            <div class="text-right mb-4" style="font-size: 13px;">
                Ponorogo, {{ $letter->approved_at?->translatedFormat('d F Y') ?? now()->translatedFormat('d F Y') }}
            </div>
            
            {{-- Signatures --}}
            <div class="flex justify-between mt-6">
                <div class="text-center" style="width: 180px;">
                    <p class="text-sm mb-2">Mahasiswa Pengaju,</p>
                    <div class="w-14 h-14 mx-auto mb-2 bg-gray-100 flex items-center justify-center">
                        {!! QrCode::size(56)->generate(json_encode(['d'=>'SBP','i'=>$letter->id,'n'=>$letter->member->member_id])) !!}
                    </div>
                    <p class="font-bold text-sm border-t border-gray-400 pt-1">{{ $letter->member->name }}</p>
                    <p class="text-xs text-gray-600">NIM. {{ $letter->member->member_id }}</p>
                </div>
                <div class="text-center" style="width: 180px;">
                    <p class="text-sm mb-2">Admin Perpustakaan,</p>
                    <div class="w-14 h-14 mx-auto mb-2 bg-gray-100 flex items-center justify-center">
                        {!! QrCode::size(56)->generate(json_encode(['d'=>'SBP','i'=>$letter->id,'a'=>$letter->approved_by])) !!}
                    </div>
                    <p class="font-bold text-sm border-t border-gray-400 pt-1">{{ $letter->approver?->name ?? 'Pustakawan' }}</p>
                    @if($letter->approver?->employee_id)
                    <p class="text-xs text-gray-600">NIP. {{ $letter->approver->employee_id }}</p>
                    @endif
                </div>
            </div>
            
            {{-- Footer --}}
            <div class="mt-10 pt-3 border-t border-gray-200 text-xs text-gray-500 text-center">
                <p>Surat ini dicetak secara otomatis dan dapat diverifikasi melalui QR Code di atas.</p>
                <p>Dicetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
            </div>
        </div>
    </div>

    <style>
        @media print {
            @page { size: A4; margin: 0; }
            body { margin: 0; padding: 0; background: white !important; }
            .no-print { display: none !important; }
        }
    </style>
</div>
