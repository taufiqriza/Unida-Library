<?php

namespace App\Http\Controllers;

use App\Models\ThesisSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ThesisFileController extends Controller
{
    protected string $disk = 'thesis';

    public function show(Request $request, ThesisSubmission $submission, string $type): StreamedResponse
    {
        $filePath = $this->getValidatedFilePath($submission, $type);
        $this->checkAccess($submission, $type);

        if (!Storage::disk($this->disk)->exists($filePath)) {
            abort(404, 'File not found');
        }

        $mimeType = Storage::disk($this->disk)->mimeType($filePath);

        return Storage::disk($this->disk)->response($filePath, basename($filePath), [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"',
            'Content-Security-Policy' => "default-src 'none'; style-src 'unsafe-inline';",
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function download(Request $request, ThesisSubmission $submission, string $type): StreamedResponse
    {
        $filePath = $this->getValidatedFilePath($submission, $type);
        $this->checkAccess($submission, $type);

        if (!Storage::disk($this->disk)->exists($filePath)) {
            abort(404, 'File not found');
        }

        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $downloadName = str($submission->nim . '_' . $submission->author . '_' . $type)->slug() . '.' . $extension;

        return Storage::disk($this->disk)->download($filePath, $downloadName);
    }

    protected function getValidatedFilePath(ThesisSubmission $submission, string $type): string
    {
        if (!in_array($type, ['cover', 'approval', 'preview', 'fulltext'])) {
            abort(404, 'File type not found');
        }

        $filePath = match($type) {
            'cover' => $submission->cover_file,
            'approval' => $submission->approval_file,
            'preview' => $submission->preview_file,
            'fulltext' => $submission->fulltext_file,
            default => null,
        };

        if (!$filePath) {
            abort(404, 'File not found');
        }

        return $filePath;
    }

    protected function checkAccess(ThesisSubmission $submission, string $type): void
    {
        $member = Auth::guard('member')->user();
        $user = Auth::guard('web')->user();

        if (!$submission->canAccessFile($type, $member, $user)) {
            abort(403, 'Access denied');
        }
    }
}
