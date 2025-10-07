<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class PermissionProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
         // تعريف Gates للتحكم بالصلاحيات
        /*Gate::define('edit-articles', fn(User $user) => $user->hasPermission('edit articles'));
        Gate::define('delete-articles', fn(User $user) => $user->hasPermission('delete articles'));
        Gate::define('view-reports', fn(User $user) => $user->hasPermission('view reports'));*/
        $permissions = Permission::all()->pluck("name");
        // dd(Auth::id());
        foreach($permissions as $p){
            Gate::define($p, function(User $user)use($p){
               return $user->hasPermission($p);
            });
        }

    }
}
