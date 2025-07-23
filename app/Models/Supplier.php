<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable=[
        'name','phone','email',
    ];

    public function bankAccount()
    {
        return $this->hasOne(BankAccount::class, 'accountable_id')
            ->where('accountable_type_cd', 2); // 1 معناها موظف
    }
}
