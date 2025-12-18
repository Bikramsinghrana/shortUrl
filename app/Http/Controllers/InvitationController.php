<?php

namespace App\Http\Controllers;

use App\Http\Requests\InviteUserRequest;
use App\Mail\InvitationMail;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Hash, Log, Mail};
use Illuminate\Support\Str;

class InvitationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();

        if ($user->isSuperAdmin()) {
            $invitations = User::with(['company', 'invitedBy'])->whereNotNull('invited_by')->latest('invited_at')->paginate(config('app.PAGINATION_NUMBER'));
        } elseif ($user->isAdmin()) {
            $invitations = User::with(['company', 'invitedBy'])->where('invited_by', $user->id)->latest('invited_at')->paginate(config('app.PAGINATION_NUMBER'));
        } else {
            abort(403, 'You do not have permission to view invitations.');
        }

        return view('invitations.index', compact('invitations'));
    }

    public function create()
    {
        $user = Auth::user();

        if (!$user->can('invite-users')) {
            abort(403, 'You do not have permission to invite users.');
        }

        if ($user->isSuperAdmin()) {
            $availableRoles = ['Admin'];
            $companies = Company::where('is_active', true)->get();
        } elseif ($user->isAdmin()) {
            $availableRoles = ['Admin', 'Member'];
            $companies = collect();
        } else {
            abort(403, 'You do not have permission to invite users.');
        }

        return view('invitations.create', compact('availableRoles', 'companies'));
    }

    public function store(InviteUserRequest $request)
    {
        $authUser = Auth::user();
        $validated = $request->validated();

        DB::beginTransaction();

        try {
            // 1️⃣ Determine company
            if ($authUser->isSuperAdmin()) {
                if ($validated['company_option'] === 'new') {
                    $company = Company::create([
                        'name' => $validated['company_name'],
                        'is_active' => true,
                    ]);
                    $companyId = $company->id;
                } else {
                    $companyId = $validated['company_id'];
                }
            } else {
                $companyId = $authUser->company_id;
            }

            // 2️⃣ Create user
            $randomPassword = Str::random(12);

            $newUser = User::create([
                'name'              => $validated['name'],
                'email'             => $validated['email'],
                'password'          => Hash::make($randomPassword),
                'company_id'        => $companyId,
                'is_active'         => true,
                'invited_by'        => $authUser->id,
                'invitation_token'  => Str::random(32),
                'invitation_status' => '0', // pending
                'invited_at'        => now(),
            ]);

            // 3️⃣ Assign role
            $newUser->assignRole($validated['role']);

            // 4️⃣ Send email
            $companyName = Company::find($companyId)?->name ?? 'N/A';

            Mail::to($newUser->email)->send(
                new InvitationMail(
                    $newUser,
                    $randomPassword,
                    $companyName,
                    $authUser->name
                )
            );

            DB::commit();

            return redirect()
                ->route('invitations.index')
                ->with('success', 'User created and invitation email sent');
        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('Invite user failed', [
                'error' => $e->getMessage(),
                'user'  => $authUser->id,
            ]);

            return redirect()->back()->withInput()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function destroy(User $invitation)
    {
        $currentUser = Auth::user();

        if (is_null($invitation->invited_by)) {
            return back()->with('error', 'Cannot delete non-invited users.');
        }


        // Check permissions
        if (!$currentUser->isSuperAdmin() && $invitation->company_id !== $currentUser->company_id) {
            abort(403);
        }

        // Prevent self-deletion
        if ($invitation->id === $currentUser->id) {
            abort(403, 'Cannot delete your own account.');
        }

        $invitation->delete();

        return redirect()->route('invitations.index')->with('success', 'User removed successfully!');
    }
}
