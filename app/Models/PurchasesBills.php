<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchasesBills extends Model
{

    protected $fillable = [
        'supplier_id',
        'paid',
        'total_amount',
        'remaining',
        'status',
        'currancy_type',
        'bill_number',
        'bill_date',
        'employee_receipt',
        'adopt_bill',
        'authorized_employee',
        'manufacturer',
        'certified_or_not',
    ];

    // علاقة المورد
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    // علاقة الموظف المفوض
    public function authorizedEmployee()
    {
        return $this->belongsTo(User::class, 'authorized_employee_id');
    }

    // علاقة تفاصيل الفاتورة
    public function details()
    {
        return $this->hasMany(PurchasesBillsDetails::class, 'bill_id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function authorizer()
    {
        return $this->belongsTo(User::class, 'authorized_employee', 'id');
    }





}
