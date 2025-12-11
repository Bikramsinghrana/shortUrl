<?php

namespace App\Http\Controllers;

use App\Mail\InvitationMail;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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
        } elseif( $user->isAdmin()) {
            $invitations = User::with(['company', 'invitedBy'])->where('invited_by', $user->id)->latest('invited_at')->paginate(config('app.PAGINATION_NUMBER'));
        }else {
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

    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->can('invite-users')) {
            abort(403);
        }

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:Admin,Member',
        ];



        // SuperAdmin must provide company information
        if ($user->isSuperAdmin()) {
            $rules['company_option'] = 'required|in:existing,new';
            $rules['company_id'] = 'required_if:company_option,existing|nullable|exists:companies,id';
            $rules['company_name'] = 'required_if:company_option,new|nullable|string|max:255';
        }

        $validated = $request->validate($rules);

        //  dd($request->all());

        // Determine company_id
        if ($user->isSuperAdmin()) {
            if (($validated['company_option'] ?? null) === 'new') {
                // Create new company
                $company = Company::create([
                    'name' => $validated['company_name'],
                    'is_active' => true,
                ]);
                $companyId = $company->id;
            } else {
                // Use existing company
                $companyId = $validated['company_id'];
            }
        } else {
            $companyId = $user->company_id;
        }

        // Generate random password
        $randomPassword = Str::random(12);

        $newUser = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($randomPassword),
            'company_id' => $companyId,
            'is_active' => true,
            'invited_by' => $user->id,
            'invitation_token' => Str::random(32),
            'invitation_status' => '0',
            'invited_at' => now(),
        ]);
        
        if (!$newUser) {
            return redirect()->back()->withInput()->with('error', 'Failed to create user. Please try again.');
        }
        // Assign role to user
        $newUser->assignRole($validated['role']);

        // Get company and inviter info
        $company = Company::find($companyId);
        $companyName = $company->name ?? 'N/A';
        $invitedByName = $user->name ?? 'System Admin';

        // Send welcome email with login credentials
        try {
            Mail::to($newUser->email)->send(new InvitationMail($newUser, $randomPassword, $companyName, $invitedByName));
            $message = 'User account created successfully! Login credentials have been sent to ' . $newUser->email;
        } catch (\Exception $e) {
            $message = 'User account created but email failed to send';
        }

        return redirect()->route('invitations.index')->with('success', $message);
        // return redirect()->route('dashboard')->with('success', $message);
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