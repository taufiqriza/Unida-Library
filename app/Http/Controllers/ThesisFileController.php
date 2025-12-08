<?php

namespace App\Http\Controllers;

use App\Models\ThesisSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ThesisFileController extends Controller
{
    /**
     * Serve thesis file with access control
     */
    public function show(Request $request, ThesisSubmission $submission, string $type): StreamedResponse
    {
        // Validate file type
        if (!in_array($type, ['cover', 'approval', 'preview', 'fulltext'])) {
            abort(404, 'File type not found');
        }

        // Get file path
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

        // Check access permission
        $member = Auth::guard('member')->user();
        $user = Auth::guard('web')->user();

        if (!$submission->canAccessFile($type, $member, $user)) {
            abort(403, 'Access denied');
        }

        // Check if file exists
        if (!Storage::disk('public')->exists($filePath)) {
            abort(404, 'File not found');
        }

        // Determine content type
        $mimeType = Storage::disk('public')->mimeType($filePath);
        $fileName = basename($filePath);

        // Stream the file
        return Storage::disk('public')->response($filePath, $fileName, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => $type === 'cover' ? 'inline' : 'inline; filename="' . $fileName . '"',
        ]);
    }

    /**
     * Download thesis file
     */
    public function download(Request $request, ThesisSubmission $submission, string $type): StreamedResponse
    {
        // Validate file type
        if (!in_array($type, ['cover', 'approval', 'preview', 'fulltext'])) {
            abort(404, 'File type not found');
        }

        // Get file path
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

        // Check access permission
        $member = Auth::guard('member')->user();
        $user = Auth::guard('web')->user();

        if (!$submission->canAccessFile($type, $member, $user)) {
            abort(403, 'Access denied');
        }

        // Check if file exists
        if (!Storage::disk('public')->exists($filePath)) {
            abort(404, 'File not found');
        }

        // Generate download filename
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $downloadName = str($submission->nim . '_' . $submission->author . '_' . $type)->slug() . '.' . $extension;

        return Storage::disk('public')->download($filePath, $downloadName);
    }
}
