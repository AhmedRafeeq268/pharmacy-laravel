<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    protected $fillable=[
        'name','description','parent_id','image_path'
    ];
}
