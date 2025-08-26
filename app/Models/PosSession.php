<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PosSession extends Model
{
    protected $fillable =[
        'user_id','opened_at','closed_at','status','opening_balance','closing_balance'
    ];

    public function transactions()
    {
        return $this->hasMany(CashBoxTransaction::class, 'session_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
