<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // تنظيف الجداول لتجنب التكرار
        DB::table('permission_role')->delete();
        DB::table('role_user')->delete();
        DB::table('permissions')->delete();
        DB::table('roles')->delete();

        // إنشاء الأدوار
        $roles = [
            'admin'             => Role::create(['name' => 'admin']),
            'Purchasing'        => Role::create(['name' => 'Purchasing']),//موظف المشتريات
            'cashier'           => Role::create(['name' => 'cashier']),//موظف الكاشير
            'Inventory_Manager' => Role::create(['name' => 'Inventory_Manager']),//مدير المخازن
            'auditor'           => Role::create(['name' => 'auditor']),//المدقق
        ];

        // إنشاء الصلاحيات
        $permissionsArray = [
            'system-settings',
            'store-users','view-users','edit-users',
            'create-codesTb','store-codesTb','edit-codesTb','view-codesTb','delete-codesTb',
            'create-employee','store-employee','edit-employee','view-employee','delete-employee',
            'create-customer','store-customer','edit-customer','view-customer','delete-customer',
            'create-supplier','store-supplier','edit-supplier','view-supplier','delete-supplier',
            'create-product','store-product','edit-product','view-product','delete-product',
            'create-productCategory','store-productCategory','edit-productCategory','view-productCategory','delete-productCategory',
            'create-purchase-bill','store-purchase-bill','edit-purchase-bill','view-purchase-bill','delete-purchase-bill',
            'create-purchase-bill-details','store-purchase-bill-details','view-purchase-bill-details','edit-purchase-bill-details',
            'certified-purchase-bill',
            'create-pos-bill','store-pos-bill','edit-pos-bill','view-pos-bill','delete-pos-bill',
            'create-purchase-return','store-purchase-return','edit-purchase-return','view-purchase-return','delete-purchase-return',
            'create-customer-return','store-customer-return','edit-customer-return','view-customer-return','delete-customer-return',
            'create-damaged-item','store-damaged-item','edit-damaged-item','view-damaged-item','delete-damaged-item',
            'view-debts',
            'create-expense','store-expense','view-expenses',
            'view-dashboard',
            'view-balanceStore',
        ];

        $permissions = [];
        foreach ($permissionsArray as $perm) {
            $permissions[$perm] = Permission::create(['name' => $perm]);
        }

        // ربط الصلاحيات بالأدوار
        $roles['admin']->permissions()->sync(Permission::all()->pluck('id')->toArray());

        $roles['Purchasing']->permissions()->sync([
            $permissions['create-customer']->id,
            $permissions['store-customer']->id,
            $permissions['edit-customer']->id,
            $permissions['view-customer']->id,
            $permissions['delete-customer']->id,
            $permissions['create-purchase-bill']->id,
            $permissions['store-purchase-bill']->id,
            $permissions['edit-purchase-bill']->id,
            $permissions['view-purchase-bill']->id,
            $permissions['delete-purchase-bill']->id,
            $permissions['create-purchase-bill-details']->id,
            $permissions['store-purchase-bill-details']->id,
            $permissions['edit-purchase-bill-details']->id,
            $permissions['view-purchase-bill-details']->id,
            $permissions['create-supplier']->id,
            $permissions['store-supplier']->id,
            $permissions['edit-supplier']->id,
            $permissions['view-supplier']->id,
            $permissions['delete-supplier']->id,
            $permissions['create-purchase-return']->id,
            $permissions['store-purchase-return']->id,
            $permissions['edit-purchase-return']->id,
            $permissions['view-purchase-return']->id,
            $permissions['delete-purchase-return']->id,
        ]);

        $roles['cashier']->permissions()->sync([
            $permissions['create-pos-bill']->id,
            $permissions['store-pos-bill']->id,
            $permissions['edit-pos-bill']->id,
            $permissions['view-pos-bill']->id,
            $permissions['delete-pos-bill']->id,
            $permissions['view-debts']->id,
            $permissions['create-customer-return']->id,
            $permissions['store-customer-return']->id,
            $permissions['edit-customer-return']->id,
            $permissions['view-customer-return']->id,
            $permissions['delete-customer-return']->id,
            $permissions['create-expense']->id,
            $permissions['store-expense']->id,
            $permissions['view-expenses']->id,
        ]);

        $roles['Inventory_Manager']->permissions()->sync([
            $permissions['create-product']->id,
            $permissions['store-product']->id,
            $permissions['edit-product']->id,
            $permissions['view-product']->id,
            $permissions['delete-product']->id,
            $permissions['create-productCategory']->id,
            $permissions['store-productCategory']->id,
            $permissions['edit-productCategory']->id,
            $permissions['view-productCategory']->id,
            $permissions['delete-productCategory']->id,
            $permissions['create-damaged-item']->id,
            $permissions['store-damaged-item']->id,
            $permissions['edit-damaged-item']->id,
            $permissions['view-damaged-item']->id,
            $permissions['delete-damaged-item']->id,
            $permissions['view-balanceStore']->id,
        ]);

        $roles['auditor']->permissions()->sync([
            $permissions['certified-purchase-bill']->id,
            $permissions['view-purchase-bill']->id,

        ]);

        // ربط الأدوار بالمستخدمين إذا وجدوا
        $users = User::all();
        foreach ($users as $user) {
            if (isset($roles[$user->role])) {
                $user->roles()->syncWithoutDetaching([$roles[$user->role]->id]);
            }
        }
    }
}
