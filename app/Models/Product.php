<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable=[
        'name','unit','price_buy','price_sell','quantity','expiry_date','barcode','manufacture_company','category_id','image_path','unit_price','price_sell'
    ];
    public function ProductCategory()
{
    return $this->belongsTo(ProductCategory::class, 'category_id');
}
}
