<?php

namespace App\Providers;

use App\Repositories\TodolistRepository;
use App\Repositories\TodolistRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use Laravel\Pail\Console\Commands\PailCommand;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            TodolistRepositoryInterface::class,
            TodolistRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        date_default_timezone_set('Asia/Manila');
        Carbon::setLocale('en');
        if (PHP_OS_FAMILY === 'Windows') {
            $this->app->forgetInstance(PailCommand::class);
        }
    }
}
