<?php

namespace App\Repositories\Contracts;

use App\Models\Company;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface InvitationRepositoryInterface
{
    public function getInvitationsForUser(User $user, int $perPage): LengthAwarePaginator;

    public function createCompany(string $name): Company;

    public function createInvitedUser(array $payload): User;
}
