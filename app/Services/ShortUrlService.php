<?php

namespace App\Services;

use App\Enums\RoleEnum;
use App\Models\ShortUrl;
use App\Models\User;
use App\Repositories\Contracts\ShortUrlRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ShortUrlService
{
    public function __construct(private readonly ShortUrlRepositoryInterface $shortUrlRepository)
    {
    }

    public function listVisible(User $user, int $perPage): LengthAwarePaginator
    {
        return $this->shortUrlRepository->getVisibleForUser($user, $perPage);
    }

    public function listVisibleForUser(User $user): LengthAwarePaginator
    {
        return $this->listVisible($user, (int) config('app.PAGINATION_NUMBER', 10));
    }

    public function canCreate(User $user): bool
    {
        if ($user->hasRole(RoleEnum::SUPER_ADMIN->value)) {
            return false;
        }

        return $user->can('create-short-urls');
    }

    public function create(User $user, array $validated): ShortUrl
    {
        return $this->shortUrlRepository->createForUser($user, $validated);
    }

    public function canView(User $user, ShortUrl $shortUrl): bool
    {
        if ($user->hasRole(RoleEnum::SUPER_ADMIN->value)) {
            return true;
        }

        if ($user->hasRole(RoleEnum::ADMIN->value)) {
            return $shortUrl->company_id === $user->company_id;
        }

        if ($user->hasRole(RoleEnum::MEMBER->value)) {
            return $shortUrl->user_id === $user->id;
        }

        return true;
    }

    public function canEdit(User $user, ShortUrl $shortUrl): bool
    {
        return $this->canCreate($user) && $shortUrl->user_id === $user->id;
    }

    public function canDelete(User $user, ShortUrl $shortUrl): bool
    {
        return $this->canCreate($user) && $shortUrl->user_id === $user->id;
    }

    public function resolveForAuthenticatedUser(string $shortCode): ?ShortUrl
    {
        $shortUrl = $this->shortUrlRepository->findByShortCode($shortCode);

        if (!$shortUrl || !$shortUrl->isAccessible()) {
            return null;
        }

        return $shortUrl;
    }

    public function incrementClicks(ShortUrl $shortUrl): void
    {
        $this->shortUrlRepository->incrementClicks($shortUrl);
    }
}
