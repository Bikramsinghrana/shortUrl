<?php

namespace App\Http\Controllers;

use App\Services\ShortUrlService;

class UrlRedirectController extends Controller
{
    public function __construct(private readonly ShortUrlService $shortUrlService)
    {
    }

    /**
     * Redirect short URL to original URL
     */
    public function redirect($shortCode)
    {
        $shortUrl = $this->shortUrlService->resolveForAuthenticatedUser($shortCode);

        if (!$shortUrl) {
            abort(404, 'Short URL not found or unavailable.');
        }

        $this->shortUrlService->incrementClicks($shortUrl);

        return redirect($shortUrl->original_url);
    }
}
