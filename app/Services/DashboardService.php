<?php

namespace App\Services;

use App\Models\Company;
use App\Models\ShortUrl;
use App\Models\User;

class DashboardService
{
    /**
     * @return array{stats: array{companies:int,users:int,urls:int,clicks:int}, allUrls: \Illuminate\Contracts\Pagination\LengthAwarePaginator}
     */
    public function getSuperAdminDashboardData(): array
    {
        $stats = [
            'companies' => Company::count(),
            'users' => User::whereNotNull('company_id')->count(),
            'urls' => ShortUrl::count(),
            'clicks' => (int) ShortUrl::sum('clicks'),
        ];

        $allUrls = ShortUrl::with(['user', 'company'])
            ->latest()
            ->paginate((int) config('app.PAGINATION_NUMBER', 10));

        return [
            'stats' => $stats,
            'allUrls' => $allUrls,
        ];
    }

    /**
     * @return array{companyUrls: \Illuminate\Contracts\Pagination\LengthAwarePaginator}
     */
    public function getAdminDashboardData(User $user): array
    {
        $companyUrls = ShortUrl::with('user')
            ->where('company_id', $user->company_id)
            ->whereIn('user_id', $user->descendantIds())
            ->latest()
            ->paginate((int) config('app.PAGINATION_NUMBER', 10));

        return [
            'companyUrls' => $companyUrls,
        ];
    }

    /**
     * @return array{myUrls: \Illuminate\Contracts\Pagination\LengthAwarePaginator}
     */
    public function getMemberDashboardData(User $user): array
    {
        $myUrls = ShortUrl::where('user_id', $user->id)
            ->latest()
            ->paginate((int) config('app.PAGINATION_NUMBER', 10));

        return [
            'myUrls' => $myUrls,
        ];
    }
}
