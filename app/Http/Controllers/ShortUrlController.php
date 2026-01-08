<?php

namespace App\Http\Controllers;

use App\Models\ShortUrl;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;

class ShortUrlController extends Controller
{
    public function index()
    {
        $shortUrls = ShortUrl::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.short-urls.index', compact('shortUrls'));
    }

    public function preview(string $code)
    {
        $shortUrl = ShortUrl::where('code', $code)->first();

        if (!$shortUrl || !$shortUrl->isActive()) {
            abort(404, 'Short URL not found or expired');
        }

        return view('short-url.preview', compact('shortUrl'));
    }

    public function redirect(string $code): RedirectResponse
    {
        $shortUrl = ShortUrl::where('code', $code)->first();

        if (!$shortUrl || !$shortUrl->isActive()) {
            abort(404, 'Short URL not found or expired');
        }

        // Track click
        $shortUrl->incrementClicks();

        return redirect($shortUrl->original_url);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'url' => 'required|url|max:2000',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'expires_at' => 'nullable|date|after:now',
            'custom_code' => 'nullable|string|max:10|alpha_num|unique:short_urls,code'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $code = $request->custom_code ?: ShortUrl::generateUniqueCode();

        $shortUrl = ShortUrl::create([
            'code' => $code,
            'original_url' => $request->url,
            'title' => $request->title,
            'description' => $request->description,
            'expires_at' => $request->expires_at,
            'user_id' => auth()->id()
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'short_url' => url('/s/' . $shortUrl->code),
                'code' => $shortUrl->code,
                'original_url' => $shortUrl->original_url,
                'clicks' => 0
            ]
        ]);
    }

    public function stats(string $code)
    {
        $shortUrl = ShortUrl::where('code', $code)->first();

        if (!$shortUrl) {
            abort(404);
        }

        return response()->json([
            'code' => $shortUrl->code,
            'original_url' => $shortUrl->original_url,
            'title' => $shortUrl->title,
            'clicks' => $shortUrl->clicks,
            'created_at' => $shortUrl->created_at,
            'expires_at' => $shortUrl->expires_at,
            'is_active' => $shortUrl->isActive()
        ]);
    }
}
