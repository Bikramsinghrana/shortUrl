<?php

namespace App\Services;

use App\Enums\InvitationStatusEnum;
use App\Enums\RoleEnum;
use App\Jobs\SendInvitationEmailJob;
use App\Models\User;
use App\Repositories\Contracts\InvitationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class InvitationService
{
    public function __construct(private readonly InvitationRepositoryInterface $invitationRepository)
    {
    }

    public function listByUser(User $user, int $perPage): LengthAwarePaginator
    {
        return $this->invitationRepository->getInvitationsForUser($user, $perPage);
    }

    public function listVisibleForUser(User $user): LengthAwarePaginator
    {
        return $this->listByUser($user, (int) config('app.PAGINATION_NUMBER', 10));
    }

    /**
     * @return array<int, string>
     */
    public function availableRolesFor(User $user): array
    {
        return $this->buildCreateFormOptions($user)['availableRoles'];
    }

    public function companiesFor(User $user): Collection
    {
        if ($user->hasRole(RoleEnum::SUPER_ADMIN->value)) {
            return collect();
        }

        return collect();
    }

    /**
     * @return array{availableRoles: array<int,string>, requiresNewCompany: bool}
     */
    public function buildCreateFormOptions(User $user): array
    {
        if ($user->hasRole(RoleEnum::SUPER_ADMIN->value)) {
            return [
                'availableRoles' => [RoleEnum::ADMIN->value],
                'requiresNewCompany' => true,
            ];
        }

        if ($user->hasRole(RoleEnum::ADMIN->value)) {
            return [
                'availableRoles' => [RoleEnum::ADMIN->value, RoleEnum::MEMBER->value],
                'requiresNewCompany' => false,
            ];
        }

        abort(403, 'You do not have permission to invite users.');
    }

    public function invite(User $authUser, array $validated): void
    {
        DB::transaction(function () use ($authUser, $validated): void {
            if ($authUser->hasRole(RoleEnum::SUPER_ADMIN->value)) {
                $company = $this->invitationRepository->createCompany($validated['company_name']);
                $companyId = $company->id;
            } else {
                $companyId = $authUser->company_id;
            }

            $randomPassword = Str::random(12);

            $newUser = $this->invitationRepository->createInvitedUser([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($randomPassword),
                'company_id' => $companyId,
                'is_active' => true,
                'invited_by' => $authUser->id,
                'invitation_token' => Str::random(32),
                'invitation_status' => InvitationStatusEnum::PENDING->value,
                'invited_at' => now(),
            ]);

            $newUser->assignRole($validated['role']);

            // Mail::to($newUser->email)->queue(
            //     (new InvitationMail(
            //         $newUser,
            //         $randomPassword,
            //         $newUser->company?->name ?? 'N/A',
            //         $authUser->name
            //     ))->afterCommit()
            // );

            SendInvitationEmailJob::dispatch(
                $newUser,
                $randomPassword,
                $newUser->company?->name ?? 'N/A',
                $authUser->name
            )->afterCommit();
        });
    }

    public function inviteUser(User $authUser, array $validated): void
    {
        $this->invite($authUser, $validated);
    }

    public function canDeleteInvitation(User $currentUser, User $invitation): bool
    {
        if (is_null($invitation->invited_by)) {
            return false;
        }

        if ($invitation->id === $currentUser->id) {
            return false;
        }

        if ($currentUser->hasRole(RoleEnum::SUPER_ADMIN->value)) {
            return true;
        }

        return $currentUser->company_id === $invitation->company_id;
    }

    public function deleteInvitation(User $invitation): void
    {
        $invitation->delete();
    }
}
