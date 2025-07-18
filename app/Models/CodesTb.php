<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CodesTb extends Model
{
    protected $fillable=[
        'main_cd','sub_cd','desc_ar','desc_en','details','is_active','is_editables'
    ];
}
