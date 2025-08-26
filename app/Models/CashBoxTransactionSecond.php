<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashBoxTransactionSecond extends Model
{
    protected $table = 'cash_box_transactions';
    protected $fillable = [
        'employee_id','received_amount','delivered_amount',
    ];

    public function session()
    {
        return $this->belongsTo(PosSession::class, 'session_id');
    }
}
