<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BalanceStore extends Model
{
    protected $fillable=[
        'product_id' ,
        'prod_date' ,
        'exp_date' ,
        'manufacture' ,
        'unity_price' ,
         'unity_price' ,
          'remaining_quantity' ,
          'quantity',
          'product_name'
    ];
}
