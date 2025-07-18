<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable=[
        'name','manufacture_company','productCategory','image_path','unit_price','category_id','barcode'
    ];
    public function ProductCategory()
{
    return $this->belongsTo(ProductCategory::class, 'category_id');
}
}
