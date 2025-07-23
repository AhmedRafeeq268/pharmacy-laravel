<?php

namespace App\Models;

use App\Models\CodesTb;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    protected $fillable =[
        'IPAN','bank_cd','wallet_phone_number','wallet_cd','currency_cd','status_cd','accountable_id','accountable_type_cd',
    ];

    public function bank()
    {
        return $this->belongsTo(CodesTb::class, 'bank_cd', 'sub_cd')
                    ->where('main_cd', 1); // main_cd = 1 للبنوك
    }

    public function wallet()
    {
        return $this->belongsTo(CodesTb::class, 'wallet_cd', 'sub_cd')
                    ->where('main_cd', 6); // main_cd = 6 للمحافظ
    }

}
