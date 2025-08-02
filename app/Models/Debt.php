<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{
    protected $fillable = [
        'customer_id',
        'pos_bill_id',
        'total_amount',
        'remaining_amount',
        'status',
        'paid_by',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function posBill()
    {
        return $this->belongsTo(PosBill::class);
    }

    public function payments()
    {
        return $this->hasMany(DebtPayment::class);
    }

    // تحديث حالة الدين بناءً على الرصيد المتبقي
    public function updateStatus()
    {
        if ($this->remaining_amount <= 0) {
            $this->status = 'closed';
        } else {
            $this->status = 'open';
        }
        $this->save();
    }

    // هل الدين مكتمل؟
    public function isFullyPaid()
    {
        return $this->remaining_amount <= 0;
    }
}
