<?php

namespace App\Providers;

use Illuminate\Auth\Events\Login;
use App\Listeners\ClosePosSession;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\ServiceProvider;
use App\Listeners\OpenPosSessionAfterLogin;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    protected $listen = [
            Login::class => [
                OpenPosSessionAfterLogin::class,
            ],

            Logout::class => [
                ClosePosSession::class,
            ],
        ];


    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
