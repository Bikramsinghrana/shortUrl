<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\ShortUrl;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            return $this->superAdminDashboard();
        }

        if ($user->isAdmin()) {
            return $this->adminDashboard();
        }

        return $this->memberDashboard();
    }

    private function superAdminDashboard()
    {
        $stats = [
            'companies' => Company::count(),
            'users' => User::whereNotNull('company_id')->count(),
            'urls' => ShortUrl::count(),
            'clicks' => ShortUrl::sum('clicks'),
        ];


        // $allUrls = ShortUrl::with(['user', 'company'])->latest() ->paginate(2);
        $allUrls = ShortUrl::with(['user', 'company'])->latest() ->paginate(config('app.PAGINATION_NUMBER'));

        return view('dashboard.super-admin', compact('stats', 'allUrls'));
    }

    private function adminDashboard()
    {
        $user = auth()->user();
        $companyId = $user->company_id;
        // $companyUrls = ShortUrl::with('user')->where('company_id', $companyId)->latest()->paginate(config('app.PAGINATION_NUMBER'));

        $perPage = config('app.PAGINATION_NUMBER', 15);

        // ADMIN → see own + all nested child admins
        $userIds = $user->descendantIds();

        $companyUrls = ShortUrl::with('user')->where('company_id', $companyId) ->whereIn('user_id', $userIds) ->latest()->paginate($perPage);
       

        return view('dashboard.admin', compact('companyUrls'));
    }

    private function memberDashboard()
    {
        $user = auth()->user();
        $myUrls = ShortUrl::where('user_id', $user->id)->latest()->paginate(config('app.PAGINATION_NUMBER')); // only their own URLs
      
        return view('dashboard.member', compact('myUrls'));
    }
}