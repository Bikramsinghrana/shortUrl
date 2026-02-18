<?php

namespace App\Repositories\Contracts;

use App\Models\ShortUrl;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ShortUrlRepositoryInterface
{
    public function getVisibleForUser(User $user, int $perPage): LengthAwarePaginator;

    public function createForUser(User $user, array $data): ShortUrl;

    public function findByShortCode(string $shortCode): ?ShortUrl;

    public function incrementClicks(ShortUrl $shortUrl): void;
}
