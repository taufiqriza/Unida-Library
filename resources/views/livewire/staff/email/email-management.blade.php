<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-gradient-to-br from-violet-500 to-purple-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-violet-500/30">
                <i class="fas fa-envelope text-2xl"></i>
            </div>
            <div>
                <h1 class="text-xl lg:text-2xl font-bold text-gray-900">Email Management</h1>
                <p class="text-sm text-gray-500">Kelola penerima, template & kirim email promosi</p>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center"><i class="fas fa-users text-blue-600"></i></div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_recipients'] }}</p>
                    <p class="text-xs text-gray-500">Total Penerima</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center"><i class="fas fa-check-circle text-green-600"></i></div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['active_recipients'] }}</p>
                    <p class="text-xs text-gray-500">Aktif</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-violet-100 rounded-xl flex items-center justify-center"><i class="fas fa-bullhorn text-violet-600"></i></div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_campaigns'] }}</p>
                    <p class="text-xs text-gray-500">Campaign</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center"><i class="fas fa-paper-plane text-amber-600"></i></div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_sent'] }}</p>
                    <p class="text-xs text-gray-500">Email Terkirim</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))<div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-2"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>@endif
    @if(session('error'))<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center gap-2"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>@endif

    {{-- Tabs --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex border-b border-gray-200 overflow-x-auto">
            @foreach(['recipients' => ['Penerima', 'fa-address-book'], 'templates' => ['Template', 'fa-palette'], 'campaigns' => ['Campaign', 'fa-bullhorn'], 'logs' => ['Riwayat', 'fa-history']] as $tab => $info)
            <button wire:click="$set('activeTab', '{{ $tab }}')" class="flex-1 min-w-[120px] px-4 py-4 font-semibold text-sm transition whitespace-nowrap {{ $activeTab === $tab ? 'text-violet-600 border-b-2 border-violet-600 bg-violet-50/50' : 'text-gray-500 hover:text-gray-700' }}">
                <i class="fas {{ $info[1] }} mr-2"></i>{{ $info[0] }}
            </button>
            @endforeach
        </div>

        <div class="p-6">
            {{-- Tab: Recipients --}}
            @if($activeTab === 'recipients')
            <div class="space-y-4">
                <div class="flex flex-col lg:flex-row gap-3 justify-between">
                    <div class="flex gap-2 flex-1">
                        <div class="relative flex-1 max-w-xs">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari..." class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500">
                        </div>
                        <select wire:model.live="categoryFilter" class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm">
                            <option value="">Semua</option>
                            <option value="faculty">Fakultas</option>
                            <option value="bureau">Biro</option>
                            <option value="prodi">Prodi</option>
                            <option value="other">Lainnya</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        @if(count($selectedRecipients) > 0)
                        <button wire:click="openCampaignModal" class="px-4 py-2.5 bg-gradient-to-r from-violet-600 to-purple-600 text-white rounded-xl text-sm font-semibold hover:shadow-lg transition">
                            <i class="fas fa-paper-plane mr-1"></i> Kirim ({{ count($selectedRecipients) }})
                        </button>
                        @endif
                        <button wire:click="openRecipientModal" class="px-4 py-2.5 bg-gray-900 text-white rounded-xl text-sm font-semibold hover:bg-gray-800 transition">
                            <i class="fas fa-plus mr-1"></i> Tambah
                        </button>
                    </div>
                </div>

                <label class="flex items-center gap-2 text-sm text-gray-600">
                    <input type="checkbox" wire:model.live="selectAll" class="w-4 h-4 rounded border-gray-300 text-violet-600">
                    Pilih Semua Aktif
                </label>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase w-10"></th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nama</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Email</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Kategori</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($recipients as $r)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <input type="checkbox" wire:model.live="selectedRecipients" value="{{ $r->id }}" class="w-4 h-4 rounded border-gray-300 text-violet-600" {{ !$r->is_active ? 'disabled' : '' }}>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-gray-900">{{ $r->name }}</p>
                                    @if($r->phone)<p class="text-xs text-gray-500">{{ $r->phone }}</p>@endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $r->email }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2.5 py-1 text-xs font-medium rounded-full {{ $r->category === 'faculty' ? 'bg-blue-100 text-blue-700' : ($r->category === 'bureau' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-700') }}">{{ ucfirst($r->category) }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <button wire:click="toggleActive({{ $r->id }})" class="px-2.5 py-1 text-xs font-medium rounded-full {{ $r->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">{{ $r->is_active ? 'Aktif' : 'Nonaktif' }}</button>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button wire:click="openRecipientModal({{ $r->id }})" class="p-2 text-gray-400 hover:text-blue-600"><i class="fas fa-edit"></i></button>
                                    <button wire:click="deleteRecipient({{ $r->id }})" wire:confirm="Hapus?" class="p-2 text-gray-400 hover:text-red-600"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">Tidak ada data</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $recipients->links() }}</div>
            </div>
            @endif

            {{-- Tab: Templates --}}
            @if($activeTab === 'templates')
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($templates as $key => $tpl)
                <div class="bg-gray-50 rounded-xl p-5 border border-gray-100 hover:border-violet-200 hover:shadow-md transition group">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-{{ $tpl['color'] }}-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas {{ $tpl['icon'] }} text-{{ $tpl['color'] }}-600 text-lg"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-gray-900">{{ $tpl['name'] }}</h3>
                            <p class="text-sm text-gray-500 mt-1">{{ $tpl['desc'] }}</p>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-200 flex gap-2">
                        <button wire:click="previewTemplate('{{ $key }}')" class="flex-1 px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm text-center text-gray-600 hover:bg-gray-50 transition">
                            <i class="fas fa-eye mr-1"></i> Preview
                        </button>
                        @if($key === 'service-promotion')
                        <button wire:click="$set('activeTab', 'recipients')" class="flex-1 px-3 py-2 bg-violet-600 text-white rounded-lg text-sm text-center hover:bg-violet-700 transition">
                            <i class="fas fa-paper-plane mr-1"></i> Kirim
                        </button>
                        @else
                        <span class="flex-1 px-3 py-2 bg-gray-100 text-gray-400 rounded-lg text-sm text-center cursor-not-allowed">
                            <i class="fas fa-robot mr-1"></i> Otomatis
                        </span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-6 p-4 bg-blue-50 rounded-xl border border-blue-100">
                <p class="text-sm text-blue-700"><i class="fas fa-info-circle mr-2"></i><strong>Info:</strong> Template selain "Promosi Layanan" dikirim otomatis oleh sistem saat event terjadi (publikasi disetujui, hasil plagiasi, dll).</p>
            </div>
            @endif

            {{-- Tab: Campaigns --}}
            @if($activeTab === 'campaigns')
            <div class="space-y-4">
                <div class="flex justify-end">
                    <button wire:click="openNewCampaignModal" class="px-4 py-2.5 bg-gradient-to-r from-violet-600 to-purple-600 text-white rounded-xl text-sm font-semibold hover:shadow-lg transition">
                        <i class="fas fa-plus mr-1"></i> Buat Campaign
                    </button>
                </div>
                @forelse($campaigns as $c)
                <div class="bg-gray-50 rounded-xl p-5 border border-gray-100">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="font-semibold text-gray-900">{{ $c->name }}</h3>
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full {{ $c->status === 'draft' ? 'bg-gray-200 text-gray-600' : ($c->status === 'sent' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700') }}">{{ ucfirst($c->status) }}</span>
                            </div>
                            <p class="text-sm text-gray-500 mb-2">{{ $c->subject }}</p>
                            <div class="flex flex-wrap items-center gap-3 text-xs text-gray-500">
                                <span><i class="fas fa-palette mr-1"></i>{{ $templates[$c->template]['name'] ?? $c->template }}</span>
                                <span><i class="fas fa-users mr-1"></i>{{ $c->total_recipients }} penerima</span>
                                @if($c->status === 'sent')
                                <span class="text-green-600"><i class="fas fa-check mr-1"></i>{{ $c->sent_count }} terkirim</span>
                                @if($c->failed_count > 0)<span class="text-red-600"><i class="fas fa-times mr-1"></i>{{ $c->failed_count }} gagal</span>@endif
                                <span><i class="fas fa-clock mr-1"></i>{{ $c->sent_at->format('d M Y H:i') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex gap-2">
                            @if($c->status === 'draft')
                            <button wire:click="confirmSend({{ $c->id }})" class="px-4 py-2 bg-gradient-to-r from-violet-600 to-purple-600 text-white rounded-lg text-sm font-semibold hover:shadow-lg transition"><i class="fas fa-paper-plane mr-1"></i> Kirim</button>
                            @endif
                            <button wire:click="deleteCampaign({{ $c->id }})" wire:confirm="Hapus campaign?" class="px-4 py-2 bg-red-100 text-red-600 rounded-lg text-sm font-semibold hover:bg-red-200"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-12 text-gray-500">
                    <i class="fas fa-bullhorn text-4xl mb-3 text-gray-300"></i>
                    <p>Belum ada campaign</p>
                </div>
                @endforelse
                <div class="mt-4">{{ $campaigns->links() }}</div>
            </div>
            @endif

            {{-- Tab: Logs --}}
            @if($activeTab === 'logs')
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Waktu</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Penerima</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Subject</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($logs as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $log->sent_at->format('d M Y H:i') }}</td>
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-900 text-sm">{{ $log->recipient->name ?? '-' }}</p>
                                <p class="text-xs text-gray-500">{{ $log->email }}</p>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ Str::limit($log->subject, 40) }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full {{ $log->status === 'sent' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}" @if($log->status === 'failed') title="{{ $log->error_message }}" @endif>{{ $log->status === 'sent' ? 'Terkirim' : 'Gagal' }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">Belum ada riwayat</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $logs->links() }}</div>
            @endif
        </div>
    </div>

    {{-- Modal: Add/Edit Recipient --}}
    @teleport('body')
    <div x-data="{ show: @entangle('showRecipientModal') }" x-show="show" x-cloak class="fixed inset-0 z-[99999] flex items-center justify-center p-4" style="background: rgba(0,0,0,0.5)">
        <div @click.outside="show = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-md" x-show="show" x-transition>
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-900">{{ $editingRecipient ? 'Edit' : 'Tambah' }} Penerima</h3>
            </div>
            <form wire:submit="saveRecipient" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                    <input wire:model="recipientForm.name" type="text" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500">
                    @error('recipientForm.name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input wire:model="recipientForm.email" type="email" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500">
                    @error('recipientForm.email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                    <select wire:model="recipientForm.category" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl">
                        <option value="faculty">Fakultas</option>
                        <option value="bureau">Biro</option>
                        <option value="prodi">Prodi</option>
                        <option value="other">Lainnya</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Telepon (opsional)</label>
                    <input wire:model="recipientForm.phone" type="text" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl">
                </div>
                <div class="flex gap-3 pt-4">
                    <button type="button" @click="show = false" class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-gray-600 font-medium hover:bg-gray-50">Batal</button>
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-violet-600 text-white rounded-xl font-medium hover:bg-violet-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    @endteleport

    {{-- Modal: Create Campaign --}}
    @teleport('body')
    <div x-data="{ show: @entangle('showCampaignModal') }" x-show="show" x-cloak class="fixed inset-0 z-[99999] flex items-center justify-center p-4" style="background: rgba(0,0,0,0.5)">
        <div @click.outside="show = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-lg" x-show="show" x-transition>
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-900">Buat Campaign Baru</h3>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Campaign</label>
                    <input wire:model="campaignName" type="text" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Template Email</label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach($templates as $key => $tpl)
                        @if($key === 'service-promotion')
                        <label class="flex items-center gap-3 p-3 border rounded-xl cursor-pointer transition {{ $selectedTemplate === $key ? 'border-violet-500 bg-violet-50' : 'border-gray-200 hover:border-gray-300' }}">
                            <input type="radio" wire:model="selectedTemplate" value="{{ $key }}" class="text-violet-600">
                            <div>
                                <p class="font-medium text-sm text-gray-900">{{ $tpl['name'] }}</p>
                                <p class="text-xs text-gray-500">{{ $tpl['desc'] }}</p>
                            </div>
                        </label>
                        @endif
                        @endforeach
                    </div>
                    <p class="text-xs text-gray-400 mt-2"><i class="fas fa-info-circle mr-1"></i>Saat ini hanya template Promosi Layanan yang bisa dikirim massal</p>
                </div>
                <div class="bg-violet-50 rounded-xl p-4">
                    <p class="text-sm text-violet-700"><i class="fas fa-users mr-2"></i><strong>{{ count($selectedRecipients) }}</strong> penerima terpilih</p>
                </div>
                <div class="flex gap-3 pt-4">
                    <button @click="show = false" class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-gray-600 font-medium hover:bg-gray-50">Batal</button>
                    <button wire:click="createCampaign" class="flex-1 px-4 py-2.5 bg-violet-600 text-white rounded-xl font-medium hover:bg-violet-700">Buat Campaign</button>
                </div>
            </div>
        </div>
    </div>
    @endteleport

    {{-- Modal: Send Confirmation --}}
    @teleport('body')
    <div x-data="{ show: @entangle('showSendModal') }" x-show="show" x-cloak class="fixed inset-0 z-[99999] flex items-center justify-center p-4" style="background: rgba(0,0,0,0.5)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md" x-show="show" x-transition>
            <div class="p-6 text-center">
                <div class="w-16 h-16 bg-violet-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-paper-plane text-2xl text-violet-600"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Kirim Email?</h3>
                @if($sendingCampaign)
                <p class="text-gray-500 mb-6">Email akan dikirim ke <strong>{{ $sendingCampaign->total_recipients }}</strong> penerima.</p>
                @endif
                <div class="flex gap-3">
                    <button @click="show = false" class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-gray-600 font-medium hover:bg-gray-50">Batal</button>
                    <button wire:click="sendCampaign" wire:loading.attr="disabled" class="flex-1 px-4 py-2.5 bg-gradient-to-r from-violet-600 to-purple-600 text-white rounded-xl font-medium hover:shadow-lg">
                        <span wire:loading.remove wire:target="sendCampaign">Kirim Sekarang</span>
                        <span wire:loading wire:target="sendCampaign"><i class="fas fa-spinner fa-spin mr-1"></i>Mengirim...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endteleport

    {{-- Modal: Template Preview --}}
    @teleport('body')
    <div x-data="{ show: @entangle('showPreviewModal') }" x-show="show" x-cloak class="fixed inset-0 z-[99999] flex items-center justify-center p-4" style="background: rgba(0,0,0,0.5)">
        <div @click.outside="show = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col" x-show="show" x-transition>
            <div class="p-4 border-b border-gray-100 flex items-center justify-between flex-shrink-0">
                <h3 class="text-lg font-bold text-gray-900">Preview: {{ $templates[$previewTemplate]['name'] ?? '' }}</h3>
                <button @click="show = false" class="p-2 text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
            </div>
            <div class="flex-1 overflow-auto bg-gray-100 p-4">
                @if($previewTemplate)
                    @php $previewData = $this->getPreviewData($previewTemplate); @endphp
                    <div class="bg-white rounded-lg shadow">
                        @include("emails.{$previewTemplate}", $previewData)
                    </div>
                @endif
            </div>
        </div>
    </div>
    @endteleport
</div>
