<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DamagedItem extends Model
{
    protected $fillable = ['product_id', 'quantity', 'reason', 'reported_by','session_id'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function session()
    {
        return $this->belongsTo(PosSession::class, 'session_id');
    }
}
