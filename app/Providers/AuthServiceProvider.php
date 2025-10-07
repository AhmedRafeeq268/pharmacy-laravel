<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{

    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * تسجيل أي أذونات (Gates) التطبيق.
     */
    public function boot(): void
    {
        $this->registerPolicies();


    }
}
