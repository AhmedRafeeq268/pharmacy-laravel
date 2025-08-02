<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DebtPayment extends Model
{
    protected $fillable = [
        'debt_id',
        'amount_paid',
        'payment_date',
        'payment_method',
        'notes',
    ];

    public function debt()
    {
        return $this->belongsTo(Debt::class);
    }

    // بعد إضافة الدفع، تحديث الدين
    public static function boot()
    {
        parent::boot();

        static::created(function ($payment) {
            $debt = $payment->debt;
            $debt->remaining_amount -= $payment->amount_paid;
            $debt->updateStatus();
        });
    }
}
