<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashBoxTransaction extends Model
{
    protected $table = 'cashbox_transactions';
    protected $fillable = [
        'employee_id','received_amount','delivered_amount','session_id','type','amount','note','expense_id'
    ];

    public function session()
    {
        return $this->belongsTo(PosSession::class, 'session_id');
    }
}
