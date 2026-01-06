{{-- Participants Tab --}}
<div class="space-y-4">
    {{-- Filters --}}
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" wire:model.live.debounce.300ms="enrollmentSearch" placeholder="Cari peserta..." 
                       class="w-full pl-11 pr-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500">
            </div>
            <select wire:model.live="enrollmentStatus" class="px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500">
                <option value="">Semua Status</option>
                <option value="pending">Menunggu</option>
                <option value="approved">Disetujui</option>
                <option value="completed">Lulus</option>
                <option value="rejected">Ditolak</option>
            </select>
        </div>
    </div>

    {{-- Participants Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        @if($enrollments->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Peserta</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Progress</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Nilai</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Terdaftar</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($enrollments as $enrollment)
                    <tr class="hover:bg-gray-50">
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
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                {{ $enrollment->status === 'pending' ? 'bg-amber-100 text-amber-700' : '' }}
                                {{ $enrollment->status === 'approved' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $enrollment->status === 'completed' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $enrollment->status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}">
                                {{ $enrollment->status === 'pending' ? 'Menunggu' : ($enrollment->status === 'approved' ? 'Aktif' : ($enrollment->status === 'completed' ? 'Lulus' : 'Ditolak')) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-24 h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-violet-500 rounded-full" style="width: {{ $enrollment->progress_percent }}%"></div>
                                </div>
                                <span class="text-xs text-gray-600">{{ $enrollment->progress_percent }}%</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-medium {{ ($enrollment->final_score ?? 0) >= $course->passing_score ? 'text-green-600' : 'text-gray-600' }}">
                                {{ $enrollment->final_score ?? '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            {{ $enrollment->created_at->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-1">
                                @if($enrollment->status === 'pending')
                                <button wire:click="approveEnrollment({{ $enrollment->id }})" class="p-2 text-green-600 hover:bg-green-50 rounded-lg" title="Setujui">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button wire:click="rejectEnrollment({{ $enrollment->id }})" class="p-2 text-red-600 hover:bg-red-50 rounded-lg" title="Tolak">
                                    <i class="fas fa-times"></i>
                                </button>
                                @elseif($enrollment->status === 'approved')
                                <button wire:click="markAsCompleted({{ $enrollment->id }})" class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg" title="Tandai Lulus">
                                    <i class="fas fa-award"></i>
                                </button>
                                @elseif($enrollment->status === 'completed' && $course->has_certificate && !$enrollment->certificate)
                                <button wire:click="issueCertificate({{ $enrollment->id }})" class="p-2 text-violet-600 hover:bg-violet-50 rounded-lg" title="Terbitkan Sertifikat">
                                    <i class="fas fa-certificate"></i>
                                </button>
                                @elseif($enrollment->certificate)
                                <span class="text-xs text-green-600"><i class="fas fa-check-circle mr-1"></i>Sertifikat</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">
            {{ $enrollments->links() }}
        </div>
        @else
        <div class="p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-users text-gray-400 text-2xl"></i>
            </div>
            <h3 class="font-bold text-gray-900 mb-2">Belum Ada Peserta</h3>
            <p class="text-gray-500">Peserta akan muncul setelah mendaftar ke kelas ini</p>
        </div>
        @endif
    </div>
</div>
