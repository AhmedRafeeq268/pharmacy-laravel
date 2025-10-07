<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $permission)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        // اسم الراوت الحالي
        $routeName = $request->route()->getName();

        // تحقق من الصلاحية باستخدام دالة User::hasPermission
        if (!$user || !$user->hasPermission($routeName)) {
            abort(403, 'Unauthorized'); // منع الوصول
        }

        return $next($request);
    }
}

