<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreShortUrlRequest;
use App\Models\ShortUrl;
use App\Models\User;
use App\Services\ShortUrlService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShortUrlController extends Controller
{
    public function __construct(
        private readonly ShortUrlService $shortUrlService
    ) {
        $this->middleware('auth');
    }

    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        $shortUrls = $this->shortUrlService->listVisibleForUser($user);

        return view('short-urls.index', compact('shortUrls'));
    }

    public function create()
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$this->shortUrlService->canCreate($user)) {
            abort(403, 'You do not have permission to create short URLs.');
        }

        return view('short-urls.create');
    }

    public function store(StoreShortUrlRequest $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $shortUrl = $this->shortUrlService->create($user, $request->validated());

        if (!$shortUrl) {
            return redirect()->back()->withInput()->withErrors(['error' => 'Failed! Please try again.']);
        }

        return redirect()->route('dashboard')->with('success', 'Short URL created successfully!');
    }

    public function show(ShortUrl $shortUrl)
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$this->shortUrlService->canView($user, $shortUrl)) {
            abort(403, 'You do not have permission to view this short URL.');
        }

        return view('short-urls.show', compact('shortUrl'));
    }

    public function edit(ShortUrl $shortUrl)
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$this->shortUrlService->canEdit($user, $shortUrl)) {
            abort(403, 'You do not have permission to edit this short URL.');
        }

        return view('short-urls.edit', compact('shortUrl'));
    }

    public function update(Request $request, ShortUrl $shortUrl)
    {
        $validated = $request->validate([
            'original_url' => 'required|url|max:2048',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'expires_at' => 'nullable|date',
        ]);

        $shortUrl->update($validated);

        return redirect()->route('short-urls.index')->with('success', 'Short URL updated successfully!');
    }

    public function destroy(ShortUrl $shortUrl)
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$this->shortUrlService->canDelete($user, $shortUrl)) {
            abort(403, 'You do not have permission to delete this short URL.');
        }

        $shortUrl->delete();

        return redirect()->route('short-urls.index')->with('success', 'Short URL deleted successfully!');
    }
}
