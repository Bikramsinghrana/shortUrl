<?php

namespace App\Repositories;

use App\Enums\RoleEnum;
use App\Models\Company;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class InvitationRepository
{
    public function getInvitationsForUser(User $user, int $perPage): LengthAwarePaginator
    {
        $query = User::with(['company', 'invitedBy'])
            ->whereNotNull('invited_by')
            ->latest('invited_at');

        if ($user->hasRole(RoleEnum::ADMIN->value)) {
            $query->where('invited_by', $user->id);
        }

        return $query->paginate($perPage);
    }

    public function createCompany(string $name): Company
    {
        return Company::create([
            'name' => $name,
            'is_active' => true,
        ]);
    }

    public function createInvitedUser(array $payload): User
    {
        return User::create($payload);
    }
}
