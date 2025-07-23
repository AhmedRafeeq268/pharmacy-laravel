<?php

namespace App\Models;

use App\Models\BankAccount;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable=[
        'name','phone','email','id_card'
    ];

    public function bankAccount()
    {
        return $this->hasOne(BankAccount::class, 'accountable_id')
            ->where('accountable_type_cd', 1); // 1 معناها موظف
    }
}
