<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchasesBillsDetails extends Model
{
    protected $fillable=[
        'bill_id','product_name','product_id','product_category','product_data','quantity','cost','total','discount',
    ];

}
