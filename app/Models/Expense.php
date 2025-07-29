<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Expense extends Model
{
    protected $fillable =[
        'type','description','amount','expense_date','created_by'
    ];
}
