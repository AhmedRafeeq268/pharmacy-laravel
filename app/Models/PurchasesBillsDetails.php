<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchasesBillsDetails extends Model
{
    protected $fillable=[
        'bill_id','product_name','product_id','product_category','product_data','quantity','cost','total','discount',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function purchaseBill()
    {
        return $this->belongsTo(PurchasesBills::class);
    }

}
