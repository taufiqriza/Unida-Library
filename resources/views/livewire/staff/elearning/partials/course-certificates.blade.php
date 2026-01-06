{{-- Certificates Tab --}}
<div class="space-y-4">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-900">Sertifikat Diterbitkan</h3>
        </div>
        
        @php
            $certificates = $course->enrollments()->whereHas('certificate')->with(['member', 'certificate.issuedBy'])->get();
        @endphp

        @if($certificates->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">No. Sertifikat</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Peserta</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Nilai Akhir</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Tanggal Terbit</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Diterbitkan Oleh</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($certificates as $enrollment)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <span class="font-mono text-sm text-violet-600">{{ $enrollment->certificate->certificate_number }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($enrollment->member->name) }}&size=32&background=random" class="w-8 h-8 rounded-full">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $enrollment->member->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $enrollment->member->member_id }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-bold text-green-600">{{ $enrollment->final_score ?? '-' }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            {{ $enrollment->certificate->issued_at->format('d M Y H:i') }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            {{ $enrollment->certificate->issuedBy->name }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-1">
                                <button class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg" title="Download">
                                    <i class="fas fa-download"></i>
                                </button>
                                <button class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg" title="Preview">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="p-12 text-center">
            <div class="w-16 h-16 bg-violet-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-certificate text-violet-400 text-2xl"></i>
            </div>
            <h3 class="font-bold text-gray-900 mb-2">Belum Ada Sertifikat</h3>
            <p class="text-gray-500">Sertifikat akan muncul setelah diterbitkan untuk peserta yang lulus</p>
        </div>
        @endif
    </div>
</div>
