<?php

namespace App\Listeners;

use App\Models\PosSession;
use App\Models\CashBoxTransaction;
use App\Models\PosBill;
use Illuminate\Auth\Events\Logout;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ClosePosSession
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
    public function handle(Logout $event)
    {
        $user = $event->user;

        // ابحث عن الجلسة المفتوحة
        $session = PosSession::where('user_id', $user->id)
            ->where('status', 'open')
            ->first();

        if ($session) {
            // الرصيد الافتتاحي
            $openingBalance = $session->opening_balance;

            // مجموع amount (موجب وسالب)
            $totalAmount = CashBoxTransaction::where('session_id', $session->id)
                ->sum('amount');

            // مجموع net_amount لنفس الجلسة
            $totalNetAmount = PosBill::where('session_id', $session->id)
                ->sum('net_amount');

            // الرصيد الختامي = رصيد افتتاحي + amount + net_amount
            $closingBalance = $openingBalance + $totalNetAmount;

            // تحديث الجلسة
            $session->update([
                'status' => 'closed',
                'closed_at' =>now(),
                'closing_balance' => $closingBalance,
            ]);
        }
    }
}
