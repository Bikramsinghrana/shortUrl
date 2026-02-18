<?php

namespace App\Repositories;

use App\Enums\RoleEnum;
use App\Models\ShortUrl;
use App\Models\User;
use App\Repositories\Contracts\ShortUrlRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ShortUrlRepository implements ShortUrlRepositoryInterface
{
    public function getVisibleForUser(User $user, int $perPage): LengthAwarePaginator
    {
        $query = ShortUrl::query()->with(['user', 'company'])->latest();

        if ($user->hasRole(RoleEnum::ADMIN->value)) {
            $query->where('company_id', $user->company_id);
        } elseif ($user->hasRole(RoleEnum::MEMBER->value)) {
            $query->where('user_id', $user->id);
        }

        return $query->paginate($perPage);
    }

    public function createForUser(User $user, array $data): ShortUrl
    {
        return ShortUrl::create([
            'user_id' => $user->id,
            'company_id' => $user->company_id,
            'original_url' => $data['original_url'],
            'title' => $data['title'] ?? null,
            'description' => $data['description'] ?? null,
            'short_code' => $data['short_code'] ?? null,
            'expires_at' => $data['expires_at'] ?? null,
        ]);
    }

    public function findByShortCode(string $shortCode): ?ShortUrl
    {
        return ShortUrl::where('short_code', $shortCode)->first();
    }

    public function incrementClicks(ShortUrl $shortUrl): void
    {
        $shortUrl->increment('clicks');
    }
}
