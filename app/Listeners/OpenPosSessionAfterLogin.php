<?php

namespace App\Listeners;

use App\Models\PosSession;
use Illuminate\Auth\Events\Login;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class OpenPosSessionAfterLogin
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event)
{
    $user = $event->user;

    // تحقق إن كان يوجد جلسة مفتوحة مسبقاً لهذا المستخدم
    $hasOpenSession = PosSession::where('user_id', $user->id)
        ->where('status', 'open')
        ->exists();

    if (!$hasOpenSession) {
        // جلب آخر رصيد إغلاق لأي جلسة مغلقة (لأي مستخدم)
        $lastClosedSession = PosSession::where('status', 'closed')
            ->orderByDesc('closed_at')
            ->first();

        $openingBalance = $lastClosedSession?->closing_balance ?? 0;

        PosSession::create([
            'user_id' => $user->id,
            'status' => 'open',
            'opened_at' => now(),
            'opening_balance' => $openingBalance,
        ]);
    }
}

}
