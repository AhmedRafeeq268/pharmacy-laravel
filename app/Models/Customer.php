<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class customer extends Model
{
    protected $fillable=[
        'name','phone','email','address','id_card','address_details','status_cd'
    ];
}
