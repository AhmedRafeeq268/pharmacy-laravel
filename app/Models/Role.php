<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name'];

    // ربط الدور بالصلاحيات
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    // ربط الدور بالمستخدمين
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
