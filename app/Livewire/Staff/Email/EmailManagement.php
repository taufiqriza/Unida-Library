<?php

namespace App\Livewire\Staff\Email;

use App\Mail\ServicePromotionMail;
use App\Models\EmailCampaign;
use App\Models\EmailLog;
use App\Models\EmailRecipient;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\WithPagination;

class EmailManagement extends Component
{
    use WithPagination;

    public $activeTab = 'recipients';
    public $search = '';
    public $categoryFilter = '';
    
    public $showRecipientModal = false;
    public $editingRecipient = null;
    public $recipientForm = ['name' => '', 'email' => '', 'category' => 'faculty', 'phone' => ''];
    
    public $showCampaignModal = false;
    public $selectedRecipients = [];
    public $campaignName = '';
    public $selectedTemplate = 'service-promotion';
    public $selectAll = false;
    
    public $showSendModal = false;
    public $sendingCampaign = null;
    
    public $showPreviewModal = false;
    public $previewTemplate = '';

    // Daftar template email yang tersedia
    public array $templates = [
        'service-promotion' => ['name' => 'Promosi Layanan', 'desc' => 'Informasi layanan terbaru perpustakaan', 'icon' => 'fa-bullhorn', 'color' => 'violet'],
        'welcome' => ['name' => 'Selamat Datang', 'desc' => 'Email sambutan member baru', 'icon' => 'fa-door-open', 'color' => 'green'],
        'publication-approved' => ['name' => 'Publikasi Disetujui', 'desc' => 'Notifikasi karya ilmiah dipublikasikan', 'icon' => 'fa-check-circle', 'color' => 'emerald'],
        'plagiarism-result' => ['name' => 'Hasil Plagiasi', 'desc' => 'Hasil pengecekan similarity', 'icon' => 'fa-search', 'color' => 'blue'],
        'certificate-updated' => ['name' => 'Update Sertifikat', 'desc' => 'Pembaruan format sertifikat', 'icon' => 'fa-certificate', 'color' => 'amber'],
        'loan-reminder' => ['name' => 'Pengingat Peminjaman', 'desc' => 'Reminder batas waktu pengembalian', 'icon' => 'fa-clock', 'color' => 'orange'],
        'loan-overdue' => ['name' => 'Keterlambatan', 'desc' => 'Notifikasi buku terlambat dikembalikan', 'icon' => 'fa-exclamation-triangle', 'color' => 'red'],
    ];

    protected $rules = [
        'recipientForm.name' => 'required|min:3',
        'recipientForm.email' => 'required|email',
        'recipientForm.category' => 'required',
    ];

    public function mount()
    {
        $this->seedDefaultRecipients();
    }

    private function seedDefaultRecipients()
    {
        if (EmailRecipient::count() === 0) {
            $defaults = [
                // Rektorat & Unit Pusat
                ['name' => 'Rektorat UNIDA Gontor', 'email' => 'rektorat@unida.gontor.ac.id', 'category' => 'bureau', 'phone' => '+62 81 3337 31713'],
                ['name' => 'Operator PDDIKTI', 'email' => 'operatorpt@unida.gontor.ac.id', 'category' => 'bureau'],
                ['name' => 'CIOS (Center for Islamization of Science)', 'email' => 'cios@unida.gontor.ac.id', 'category' => 'bureau', 'phone' => '+62 822 2839 5277'],
                ['name' => 'BAAK UNIDA Gontor', 'email' => 'baak@unida.gontor.ac.id', 'category' => 'bureau'],
                
                // Fakultas
                ['name' => 'Fakultas Ushuluddin', 'email' => 'ushuluddin@unida.gontor.ac.id', 'category' => 'faculty', 'phone' => '+62 81 3337 31713'],
                ['name' => 'Fakultas Tarbiyah', 'email' => 'tarbiyah@unida.gontor.ac.id', 'category' => 'faculty', 'phone' => '+62 813-3568-0607'],
                ['name' => 'Fakultas Syariah', 'email' => 'syariah@unida.gontor.ac.id', 'category' => 'faculty', 'phone' => '+62 838-4567-4075'],
                ['name' => 'Fakultas Ekonomi & Manajemen', 'email' => 'fem@unida.gontor.ac.id', 'category' => 'faculty', 'phone' => '+62 81235797925'],
                ['name' => 'Fakultas Humaniora', 'email' => 'humaniora@unida.gontor.ac.id', 'category' => 'faculty', 'phone' => '+62 813 3415 7523'],
                ['name' => 'Fakultas Sains & Teknologi', 'email' => 'saintek@unida.gontor.ac.id', 'category' => 'faculty', 'phone' => '+62 81 3337 31713'],
                ['name' => 'Fakultas Kedokteran', 'email' => 'fk@unida.gontor.ac.id', 'category' => 'faculty'],
                ['name' => 'Pascasarjana', 'email' => 'pascasarjana@unida.gontor.ac.id', 'category' => 'faculty', 'phone' => '+62 812-3285-7600'],
            ];
            foreach ($defaults as $r) {
                EmailRecipient::create($r);
            }
        }
    }

    public function updatedSelectAll($value)
    {
        $this->selectedRecipients = $value 
            ? EmailRecipient::where('is_active', true)->pluck('id')->toArray() 
            : [];
    }

    public function openRecipientModal($id = null)
    {
        $this->resetValidation();
        if ($id) {
            $this->editingRecipient = EmailRecipient::find($id);
            $this->recipientForm = $this->editingRecipient->only(['name', 'email', 'category', 'phone']);
        } else {
            $this->editingRecipient = null;
            $this->recipientForm = ['name' => '', 'email' => '', 'category' => 'faculty', 'phone' => ''];
        }
        $this->showRecipientModal = true;
    }

    public function saveRecipient()
    {
        $this->validate();
        if ($this->editingRecipient) {
            $this->editingRecipient->update($this->recipientForm);
        } else {
            EmailRecipient::create($this->recipientForm);
        }
        $this->showRecipientModal = false;
        session()->flash('success', 'Penerima berhasil disimpan');
    }

    public function toggleActive($id)
    {
        $r = EmailRecipient::find($id);
        $r->update(['is_active' => !$r->is_active]);
    }

    public function deleteRecipient($id)
    {
        EmailRecipient::destroy($id);
    }

    public function openCampaignModal()
    {
        if (empty($this->selectedRecipients)) {
            session()->flash('error', 'Pilih minimal 1 penerima');
            return;
        }
        $this->campaignName = 'Campaign ' . now()->format('d M Y H:i');
        $this->selectedTemplate = 'service-promotion';
        $this->showCampaignModal = true;
    }

    public function openNewCampaignModal()
    {
        // Auto-select semua penerima aktif
        $this->selectedRecipients = EmailRecipient::where('is_active', true)->pluck('id')->toArray();
        $this->campaignName = 'Campaign ' . now()->format('d M Y H:i');
        $this->selectedTemplate = 'service-promotion';
        $this->showCampaignModal = true;
    }

    public function previewTemplate($template)
    {
        $this->previewTemplate = $template;
        $this->showPreviewModal = true;
    }

    public function getPreviewData($template): array
    {
        return match($template) {
            'service-promotion' => ['recipientName' => 'Bapak/Ibu Pimpinan', 'appUrl' => config('app.url'), 'websiteUrl' => config('app.url')],
            'welcome' => ['user' => (object)['name' => 'Ahmad Fauzi']],
            'publication-approved' => ['publication' => (object)['title' => 'Contoh Judul Karya Ilmiah', 'type' => 'Skripsi'], 'user' => (object)['name' => 'Ahmad Fauzi']],
            'plagiarism-result' => ['submission' => (object)['title' => 'Contoh Judul Dokumen', 'similarity_score' => 15], 'user' => (object)['name' => 'Ahmad Fauzi']],
            'certificate-updated' => ['publication' => (object)['title' => 'Contoh Judul Karya'], 'user' => (object)['name' => 'Ahmad Fauzi']],
            'loan-reminder' => ['loan' => (object)['book' => (object)['title' => 'Contoh Judul Buku'], 'due_date' => now()->addDays(3)], 'user' => (object)['name' => 'Ahmad Fauzi']],
            'loan-overdue' => ['loan' => (object)['book' => (object)['title' => 'Contoh Judul Buku'], 'due_date' => now()->subDays(5), 'fine' => 5000], 'user' => (object)['name' => 'Ahmad Fauzi']],
            default => []
        };
    }

    public function createCampaign()
    {
        $tpl = $this->templates[$this->selectedTemplate] ?? $this->templates['service-promotion'];
        
        $campaign = EmailCampaign::create([
            'name' => $this->campaignName,
            'subject' => $this->getSubjectForTemplate($this->selectedTemplate),
            'template' => $this->selectedTemplate,
            'recipient_ids' => $this->selectedRecipients,
            'status' => 'draft',
            'total_recipients' => count($this->selectedRecipients),
            'created_by' => auth()->id(),
        ]);
        
        $this->showCampaignModal = false;
        $this->selectedRecipients = [];
        $this->selectAll = false;
        $this->activeTab = 'campaigns';
        session()->flash('success', 'Campaign berhasil dibuat');
    }

    private function getSubjectForTemplate($template): string
    {
        return match($template) {
            'service-promotion' => '📚 Informasi Layanan Terbaru - Perpustakaan UNIDA',
            'welcome' => '🎉 Selamat Datang di UNIDA Library',
            'publication-approved' => '✅ Karya Ilmiah Anda Telah Dipublikasikan',
            'plagiarism-result' => '📊 Hasil Pengecekan Plagiasi',
            'certificate-updated' => '📜 Pembaruan Sertifikat Originalitas',
            'loan-reminder' => '⏰ Pengingat Pengembalian Buku',
            'loan-overdue' => '⚠️ Pemberitahuan Keterlambatan Pengembalian',
            default => 'Informasi dari Perpustakaan UNIDA'
        };
    }

    public function confirmSend($id)
    {
        $this->sendingCampaign = EmailCampaign::find($id);
        $this->showSendModal = true;
    }

    public function sendCampaign()
    {
        $campaign = $this->sendingCampaign;
        $campaign->update(['status' => 'sending']);
        
        $recipients = EmailRecipient::whereIn('id', $campaign->recipient_ids)->get();
        $sent = $failed = 0;
        
        foreach ($recipients as $recipient) {
            try {
                // Untuk saat ini hanya service-promotion yang bisa dikirim massal
                if ($campaign->template === 'service-promotion') {
                    Mail::to($recipient->email)->send(
                        new ServicePromotionMail($recipient->name, config('app.url'))
                    );
                }
                
                EmailLog::create([
                    'campaign_id' => $campaign->id,
                    'recipient_id' => $recipient->id,
                    'email' => $recipient->email,
                    'subject' => $campaign->subject,
                    'status' => 'sent',
                    'sent_at' => now(),
                ]);
                $sent++;
            } catch (\Exception $e) {
                EmailLog::create([
                    'campaign_id' => $campaign->id,
                    'recipient_id' => $recipient->id,
                    'email' => $recipient->email,
                    'subject' => $campaign->subject,
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                    'sent_at' => now(),
                ]);
                $failed++;
            }
        }
        
        $campaign->update(['status' => 'sent', 'sent_count' => $sent, 'failed_count' => $failed, 'sent_at' => now()]);
        $this->showSendModal = false;
        $this->sendingCampaign = null;
        session()->flash('success', "Terkirim: {$sent}, Gagal: {$failed}");
    }

    public function deleteCampaign($id)
    {
        EmailCampaign::destroy($id);
    }

    public function render()
    {
        $recipients = EmailRecipient::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")->orWhere('email', 'like', "%{$this->search}%"))
            ->when($this->categoryFilter, fn($q) => $q->where('category', $this->categoryFilter))
            ->orderBy('category')->orderBy('name')
            ->paginate(15);

        $campaigns = EmailCampaign::with('creator')->latest()->paginate(10);
        $logs = EmailLog::with('recipient')->latest()->paginate(20);
        
        $stats = [
            'total_recipients' => EmailRecipient::count(),
            'active_recipients' => EmailRecipient::where('is_active', true)->count(),
            'total_campaigns' => EmailCampaign::count(),
            'total_sent' => EmailLog::where('status', 'sent')->count(),
        ];

        return view('livewire.staff.email.email-management', compact('recipients', 'campaigns', 'logs', 'stats'))
            ->extends('staff.layouts.app')
            ->section('content');
    }
}
