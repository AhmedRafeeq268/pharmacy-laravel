<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesReturnDetail extends Model
{
    protected $fillable = [
        'sales_return_id', 'product_id', 'price', 'quantity', 'subtotal',
    ];

    public function salesReturn()
    {
        return $this->belongsTo(SalesReturn::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
