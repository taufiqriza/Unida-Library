<?php

namespace App\Http\Controllers\Opac;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessPlagiarismCheck;
use App\Models\PlagiarismCheck;
use App\Models\Setting;
use App\Services\Plagiarism\CertificateGenerator;
use App\Services\Plagiarism\PlagiarismService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PlagiarismController extends Controller
{
    /**
     * Display list of plagiarism checks
     */
    public function index()
    {
        $member = Auth::guard('member')->user();
        
        $checks = PlagiarismCheck::where('member_id', $member->id)
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('opac.member.plagiarism.index', [
            'checks' => $checks,
            'member' => $member,
        ]);
    }

    /**
     * Quota limit per member
     */
    protected const QUOTA_LIMIT = 3;

    /**
     * Show create form
     */
    public function create()
    {
        if (!PlagiarismService::isEnabled()) {
            return redirect()->route('opac.member.dashboard')
                ->with('error', 'Layanan cek plagiasi sedang tidak aktif.');
        }

        $member = Auth::guard('member')->user();
        
        // Check quota
        $usedQuota = $member->plagiarismChecks()->count();
        $remainingQuota = self::QUOTA_LIMIT - $usedQuota;
        
        if ($remainingQuota <= 0) {
            return redirect()->route('opac.member.plagiarism.index')
                ->with('error', 'Kuota cek plagiasi Anda sudah habis (' . self::QUOTA_LIMIT . ' kali). Hubungi admin jika memerlukan penambahan kuota.');
        }
        
        // Get member's thesis submissions (if any)
        $submissions = $member->thesisSubmissions()
            ->whereIn('status', ['approved', 'published'])
            ->latest()
            ->get();

        return view('opac.member.plagiarism.create', [
            'member' => $member,
            'submissions' => $submissions,
            'usedQuota' => $usedQuota,
            'quotaLimit' => self::QUOTA_LIMIT,
            'remainingQuota' => $remainingQuota,
        ]);
    }

    /**
     * Store new plagiarism check
     */
    public function store(Request $request)
    {
        if (!PlagiarismService::isEnabled()) {
            return redirect()->route('opac.member.dashboard')
                ->with('error', 'Layanan cek plagiasi sedang tidak aktif.');
        }

        $request->validate([
            'document' => 'required|file|mimes:pdf,docx|max:20480', // Max 20MB
            'document_title' => 'required|string|max:500',
            'thesis_submission_id' => 'nullable|exists:thesis_submissions,id',
            'agreement' => 'required|accepted',
        ], [
            'document.required' => 'Dokumen wajib diunggah',
            'document.mimes' => 'Format dokumen harus PDF atau DOCX',
            'document.max' => 'Ukuran dokumen maksimal 20MB',
            'document_title.required' => 'Judul dokumen wajib diisi',
            'agreement.accepted' => 'Anda harus menyetujui pernyataan',
        ]);

        $member = Auth::guard('member')->user();
        
        // Check quota before processing
        $usedQuota = $member->plagiarismChecks()->count();
        if ($usedQuota >= self::QUOTA_LIMIT) {
            return redirect()->route('opac.member.plagiarism.index')
                ->with('error', 'Kuota cek plagiasi Anda sudah habis.');
        }
        
        $file = $request->file('document');

        // Store file
        $path = $file->store('plagiarism-uploads', 'local');

        // Create check record
        $check = PlagiarismCheck::create([
            'member_id' => $member->id,
            'thesis_submission_id' => $request->thesis_submission_id,
            'document_title' => $request->document_title,
            'original_filename' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getClientOriginalExtension(),
            'file_size' => $file->getSize(),
            'status' => PlagiarismCheck::STATUS_PENDING,
            'provider' => Setting::get('plagiarism_provider', 'ithenticate'),
        ]);

        // Dispatch background job
        ProcessPlagiarismCheck::dispatch($check);

        return redirect()->route('opac.member.plagiarism.show', $check)
            ->with('success', 'Dokumen berhasil diunggah. Pengecekan plagiasi sedang diproses.');
    }

    /**
     * Show check result
     */
    public function show(PlagiarismCheck $check)
    {
        $member = Auth::guard('member')->user();

        // Authorization: only owner can view
        if ($check->member_id !== $member->id) {
            abort(403, 'Akses ditolak');
        }

        return view('opac.member.plagiarism.show', [
            'check' => $check,
            'member' => $member,
        ]);
    }

    /**
     * View certificate in browser
     */
    public function certificate(PlagiarismCheck $check)
    {
        $member = Auth::guard('member')->user();

        if ($check->member_id !== $member->id) {
            abort(403, 'Akses ditolak');
        }

        if (!$check->isCompleted() || !$check->hasCertificate()) {
            return redirect()->route('opac.member.plagiarism.show', $check)
                ->with('error', 'Sertifikat belum tersedia.');
        }

        return view('opac.member.plagiarism.certificate', [
            'check' => $check,
            'member' => $member,
        ]);
    }

    /**
     * Download certificate PDF
     */
    public function downloadCertificate(PlagiarismCheck $check)
    {
        $member = Auth::guard('member')->user();

        if ($check->member_id !== $member->id) {
            abort(403, 'Akses ditolak');
        }

        if (!$check->isCompleted()) {
            return redirect()->route('opac.member.plagiarism.show', $check)
                ->with('error', 'Pengecekan belum selesai.');
        }

        // Generate certificate if not exists
        if (!$check->hasCertificate()) {
            $generator = new CertificateGenerator($check);
            $generator->generate();
            $check->refresh();
        }

        $generator = new CertificateGenerator($check);
        
        return response($generator->getPdfContent(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $generator->getDownloadFilename() . '"',
        ]);
    }

    /**
     * Public verification page
     */
    public function verify(string $certificate)
    {
        $check = PlagiarismCheck::where('certificate_number', $certificate)->first();

        if (!$check) {
            return view('opac.plagiarism.verify', [
                'found' => false,
                'certificate' => $certificate,
            ]);
        }

        return view('opac.plagiarism.verify', [
            'found' => true,
            'check' => $check,
            'certificate' => $certificate,
        ]);
    }

    /**
     * Check status via AJAX (for polling)
     */
    public function status(PlagiarismCheck $check)
    {
        $member = Auth::guard('member')->user();

        if ($check->member_id !== $member->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'status' => $check->status,
            'status_label' => $check->status_label,
            'status_info' => $check->status_info,
            'similarity_score' => $check->similarity_score,
            'is_completed' => $check->isCompleted(),
            'is_failed' => $check->isFailed(),
            'is_stuck' => $check->isStuck(),
            'has_certificate' => $check->hasCertificate(),
        ]);
    }

    /**
     * View full report on iThenticate
     */
    public function viewReport(PlagiarismCheck $check)
    {
        $member = Auth::guard('member')->user();

        if ($check->member_id !== $member->id) {
            abort(403);
        }

        if (!$check->isCompleted() || !$check->external_id || $check->provider !== 'ithenticate') {
            return back()->with('error', 'Report tidak tersedia.');
        }

        $provider = new \App\Services\Plagiarism\Providers\IthenticateProvider();
        $url = $provider->getReportUrl($check->external_id);

        if (!$url) {
            return back()->with('error', 'Gagal mengambil link report.');
        }

        return redirect($url);
    }
}
