<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchasesBills extends Model
{
    protected $fillable=[
        'total_amount','currancy_type','bill_number','bill_date','employee_receipt','adopt_bill','authorized_employee','manufacturer','certified_or_not',
    ];

}
