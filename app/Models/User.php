<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable ;
    protected $with=["roles"];

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // علاقات Roles وPermissions
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    // التحقق من وجود دور
    public function hasRole($roleName)
    {
        return $this->roles->pluck('name')->contains($roleName);
    }

    public function extraPermissions()
    {
        return $this->hasMany(UserExtraPermission::class);
    }

    public function deniedPermissions()
    {
        return $this->hasMany(UserDeniedPermission::class);
    }


    // التحقق من وجود صلاحية
     /**
     * تحقق من وجود صلاحية للمستخدم
     *
     * @param string $permissionName
     * @return bool
     */
    public function hasPermission($permissionName)
    {
        // ✅ لو المستخدم عنده رول اسمه admin → اعطيه كل الصلاحيات
        if ($this->roles->pluck('name')->contains('admin')) {
            return true;
        }

        foreach ($this->roles as $role) {
            if ($role->permissions->pluck('name')->contains($permissionName)) {
                if ($this->deniedPermissions->pluck('permission_key')->contains($permissionName)) {
                    return false;
                }

                return true;
            }
        }

        // الصلاحيات الإضافية
        if ($this->extraPermissions->pluck('permission_key')->contains($permissionName)) {
            return true;
        }
        return false;
    }
}
