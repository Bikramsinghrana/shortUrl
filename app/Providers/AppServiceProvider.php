<?php

namespace App\Providers;

use App\Repositories\Contracts\InvitationRepositoryInterface;
use App\Repositories\Contracts\ShortUrlRepositoryInterface;
use App\Repositories\InvitationRepository;
use App\Repositories\ShortUrlRepository;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ShortUrlRepositoryInterface::class, ShortUrlRepository::class);
        $this->app->bind(InvitationRepositoryInterface::class, InvitationRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
    }
}
