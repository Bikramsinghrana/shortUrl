<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreShortUrlRequest;
use App\Models\ShortUrl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShortUrlController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {   

        $user = Auth::user();

        // SuperAdmin can see all URLs from all companies
        if ($user->isSuperAdmin()) {
            $shortUrls = ShortUrl::with(['user', 'company'])->latest()->paginate(config('app.PAGINATION_NUMBER'));
        }
        // Admin can see all URLs from their company
        elseif ($user->isAdmin()) {
            $shortUrls = ShortUrl::with('user')->where('company_id', $user->company_id) ->latest()->paginate(config('app.PAGINATION_NUMBER'));
        }
        // Members can only see their themselves URLs
        else {
            $shortUrls = ShortUrl::where('user_id', $user->id)->latest()->paginate(config('app.PAGINATION_NUMBER'));
        }

        return view('short-urls.index', compact('shortUrls'));
        return redirect()->route('dashboard')->with('success', 'Short URL created successfully!');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {   
        if (Auth::user()->isSuperAdmin()) {
            abort(403, 'SuperAdmin cannot create short URLs.');
        }
        return view('short-urls.create');
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(StoreShortUrlRequest $request)
    {   

        $user = Auth::user();

        $validated = $request->validated();

        $shortUrl = ShortUrl::create([
            'user_id'      => $user->id,
            'company_id'   => $user->company_id,
            'original_url' => $validated['original_url'],
            'title'        => $validated['title'] ?? null,
            'description'  => $validated['description'] ?? null,
            'short_code'   => $validated['short_code'] ?? null,
            'expires_at'   => $validated['expires_at'] ?? null,
        ]);

        if (!$shortUrl) {
            return redirect()->back()->withInput()->withErrors(['error' => 'Failed! Please try again.']);
        }

        return redirect()->route('dashboard')->with('success', 'Short URL created successfully!');
    }


    /**
     * Display the specified resource.
     */
    public function show(ShortUrl $shortUrl)
    {
        $user = Auth::user();

        if (!$this->canViewUrl($user, $shortUrl)) {
            abort(403, 'You do not have permission to view this short URL.');
        }

        return view('short-urls.show', compact('shortUrl'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ShortUrl $shortUrl)
    {
        $user = Auth::user();

        // SuperAdmin cannot edit URLs
        if ($user->isSuperAdmin()) {
            abort(403, 'SuperAdmin cannot edit short URLs.');
        }

        // Authorization check
        if (!$this->canEditUrl($user, $shortUrl)) {
            abort(403, 'You do not have permission to edit this short URL.');
        }

        return view('short-urls.edit', compact('shortUrl'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ShortUrl $shortUrl)
    {
        $user = Auth::user();

        // Validate
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ShortUrl $shortUrl)
    {
        $user = Auth::user();

        // SuperAdmin cannot delete URLs
        if ($user->isSuperAdmin()) {
            abort(403, 'SuperAdmin cannot delete short URLs.');
        }

        // Authorization check
        if (!$this->canDeleteUrl($user, $shortUrl)) {
            abort(403, 'You do not have permission to delete this short URL.');
        }

        $shortUrl->delete();

        return redirect()->route('short-urls.index')->with('success', 'Short URL deleted successfully!');
    }

    /**
     * Check if user can view the URL
     */
    private function canViewUrl($user, $shortUrl)
    {

        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isAdmin() && $shortUrl->company_id === $user->company_id) {
            return true;
        }

        if ($shortUrl->user_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Check if user can edit the URL
     */
    private function canEditUrl($user, $shortUrl)
    {
        if (!$user->can('edit-short-urls')) {
            return false;
        }

        if ($user->isAdmin() && $shortUrl->company_id === $user->company_id) {
            return true;
        }

        if ($shortUrl->user_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Check if user can delete the URL
     */
    private function canDeleteUrl($user, $shortUrl)
    {
        if (!$user->can('delete-short-urls')) {
            return false;
        }

        if ($user->isAdmin() && $shortUrl->company_id === $user->company_id) {
            return true;
        }

        if ($shortUrl->user_id === $user->id) {
            return true;
        }

        return false;
    }
}
