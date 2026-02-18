<?php

namespace App\Http\Controllers;

use App\Http\Requests\InviteUserRequest;
use App\Models\User;
use App\Services\InvitationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class InvitationController extends Controller
{
    public function __construct(
        private readonly InvitationService $invitationService
    ) {
        $this->middleware('auth');
    }

    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user->isSuperAdmin() && !$user->isAdmin()) {
            abort(403, 'You do not have permission to view invitations.');
        }

        $invitations = $this->invitationService->listVisibleForUser($user);

        return view('invitations.index', compact('invitations'));
    }

    public function create()
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user->can('invite-users')) {
            abort(403, 'You do not have permission to invite users.');
        }

        $availableRoles = $this->invitationService->availableRolesFor($user);

        if (empty($availableRoles)) {
            abort(403, 'You do not have permission to invite users.');
        }

        $companies = $this->invitationService->companiesFor($user);

        return view('invitations.create', compact('availableRoles', 'companies'));
    }

    public function store(InviteUserRequest $request)
    {
        /** @var User $authUser */
        $authUser = Auth::user();

        try {
            $this->invitationService->inviteUser($authUser, $request->validated());

            return redirect()
                ->route('invitations.index')
                ->with('success', 'User created and invitation email sent');
        } catch (\Throwable $e) {
            Log::error('Invite user failed', [
                'error' => $e->getMessage(),
                'user' => $authUser->id,
            ]);

            return redirect()->back()->withInput()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function destroy(User $invitation)
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        if (!$this->invitationService->canDeleteInvitation($currentUser, $invitation)) {
            abort(403, 'You do not have permission to delete this invitation.');
        }

        $this->invitationService->deleteInvitation($invitation);

        return redirect()->route('invitations.index')->with('success', 'User removed successfully!');
    }
}
