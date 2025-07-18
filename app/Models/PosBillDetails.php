<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosBillDetails extends Model
{
    protected $fillable =
    [
        'pos_bill_id','product_id','unit_price','quantity','price'
    ];

    public function product()
{
    return $this->belongsTo(Product::class, 'product_id');
}

}
