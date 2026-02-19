<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\DashboardService;

class DashboardController extends Controller
{
    public function __construct(private readonly DashboardService $dashboardService)
    {
    }

    public function index()
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            return view('dashboard.super-admin', $this->dashboardService->getSuperAdminDashboardData());
        }

        if ($user->isAdmin()) {
            return view('dashboard.admin', $this->dashboardService->getAdminDashboardData($user));
        }

        return view('dashboard.member', $this->dashboardService->getMemberDashboardData($user));
    }
}