<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosBill extends Model
{
    protected $fillable = [
        'customer_id','employee_id','total_amount','discount','net_amount','payment_status','is_closed_with_cashbox'

    ];

    public function details()
{
    return $this->hasMany(PosBillDetails::class, 'pos_bill_id');
}

    public function customer()
{
    return $this->belongsTo(Customer::class);
}

public function employee(){
    return $this->belongsTo(Employee::class);
}

public function products()
{
    return $this->hasManyThrough(Product::class, PosBillDetails::class, 'pos_bill_id', 'id', 'id', 'product_id');
}
}
