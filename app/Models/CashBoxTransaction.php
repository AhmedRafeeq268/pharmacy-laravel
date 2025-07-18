<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashBoxTransaction extends Model
{
    protected $fillable = [
        'employee_id','received_amount','delivered_amount',
    ];
}
