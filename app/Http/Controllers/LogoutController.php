<?php

namespace App\Http\Controllers;

use App\Models\PosBill;
use App\Models\PosSession;
use Illuminate\Http\Request;
use App\Jobs\ClosePosSessionJob;
use Illuminate\Support\Facades\Auth;
use App\Models\CashBoxTransactionSecond;

class LogoutController extends Controller
{
    public function forceLogout()
    {
        if (Auth::check()) {
            $userId = Auth::id();

            // أرسل Job لمعالجة إغلاق الجلسة لاحقًا
            ClosePosSessionJob::dispatch($userId);

            // تسجيل خروج المستخدم فورًا
            Auth::logout();
        }

        return response()->noContent(); // 204
    }
}
