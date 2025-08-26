<?php

namespace App\Jobs;

use App\Models\PosBill;
use App\Models\PosSession;
use App\Models\CashBoxTransactionSecond;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ClosePosSessionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function handle()
    {
        $session = PosSession::where('user_id', $this->userId)
            ->where('status', 'open')
            ->first();

        if (!$session) return;

        $openingBalance = $session->opening_balance;
        $totalNetAmount = PosBill::where('session_id', $session->id)->sum('net_amount');
        $closingBalance = $openingBalance + $totalNetAmount;

        CashBoxTransactionSecond::create([
            'employee_id' => $this->userId,
            'received_amount' => $openingBalance,
            'delivered_amount' => $closingBalance,
        ]);

        $session->update([
            'status' => 'closed',
            'closed_at' => now(),
            'closing_balance' => $closingBalance,
        ]);
    }
}
