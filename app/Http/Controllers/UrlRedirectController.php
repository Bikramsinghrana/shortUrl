<?php

namespace App\Http\Controllers;

use App\Models\ShortUrl;
use Illuminate\Http\Request;

class UrlRedirectController extends Controller
{
    /**
     * Redirect short URL to original URL
     */
    public function redirect($shortCode)
    {
        $shortUrl = ShortUrl::where('short_code', $shortCode)->first();

        if (!$shortUrl) {
            abort(404, 'Short URL not found.');
        }

        if (!$shortUrl->isAccessible()) {
            abort(410, 'This short URL has expired or is no longer active.');
        }

        // Increment click count
        $shortUrl->incrementClicks();

        // Redirect to original URL
        return redirect($shortUrl->original_url);
    }
}
