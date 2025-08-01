<?php

namespace App\Models;

use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Foundation\Auth\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseReturn extends Model
{
    protected $fillable = [
        'purchase_bill_id','supplier_id','product_id','quantity','return_amount','reason','refunded_in_cash','created_by','session_id','edited_by'
    ];

    public function purchaseBill() {
    return $this->belongsTo(PurchasesBills::class,'purchase_bill_id');
    }

    public function supplier() {
        return $this->belongsTo(Supplier::class,'supplier_id');
    }

    public function product() {
        return $this->belongsTo(Product::class,'product_id');
    }

    // public function user() {
    //     return $this->belongsTo(User::class, 'created_by');
    // }

    // المستخدم الذي قام بالإرجاع
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function editor()
    {
        return $this->belongsTo(User::class, 'edited_by');
    }
}
