<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Debt;
use App\Models\User;
use App\Models\PosBill;
use App\Models\Permission;
use App\Models\BalanceStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class AdminController extends Controller
{
    // public function dashboard()
    // {
    //     abort_if(Gate::denies('view-dashboard'), 403);
    //     // 1. إجمالي المبيعات اليوم
    //     $salesToday = PosBill::whereDate('created_at', now())->sum('net_amount');

    //     // 2. عدد الفواتير اليوم
    //     $billsToday = PosBill::whereDate('created_at', now())->count();

    //     // 3. إجمالي الديون المفتوحة
    //     $totalDebts = Debt::where('status', 'open')->sum('remaining_amount');

    //     // 4. عدد المنتجات منخفضة المخزون (<5)
    //     $lowStock = BalanceStore::select('product_id', DB::raw('SUM(remaining_quantity) as total_quantity'))
    //         ->groupBy('product_id')
    //         ->having('total_quantity', '<', 5)
    //         ->count();


    //     // 5. آخر 10 فواتير
    //     $recentBills = PosBill::with('customer')
    //         ->latest()
    //         ->take(10)
    //         ->get();

    //     // 6. بيانات الرسم البياني للمبيعات آخر 7 أيام
    //     $salesDataRaw = PosBill::select(
    //         DB::raw('DATE(created_at) as date'),
    //         DB::raw('SUM(net_amount) as total')
    //     )
    //     ->where('created_at', '>=', Carbon::now()->subDays(6)->startOfDay())
    //     ->groupBy('date')
    //     ->orderBy('date')
    //     ->get();

    //     $salesLabels = $salesDataRaw->pluck('date')->map(function($d) {
    //         return Carbon::parse($d)->format('d-m');
    //     })->toArray();

    //     $salesData = $salesDataRaw->pluck('total')->map(fn($v) => (float)$v)->toArray();

    //     // 7. بيانات الرسم البياني للديون حسب العملاء
    //     $debtsDataRaw = Debt::with('customer')
    //         ->select('customer_id', DB::raw('SUM(remaining_amount) as total'))
    //         ->where('status', 'open')
    //         ->groupBy('customer_id')
    //         ->get();

    //     $debtsLabels = $debtsDataRaw->map(fn($d) => $d->customer?->name ?? 'زبون عام')->toArray();
    //     $debtsData   = $debtsDataRaw->pluck('total')->map(fn($v) => (float)$v)->toArray();

    //     // 8. عرض البيانات في الصفحة
    //     return view('admin.dashboard', compact(
    //         'salesToday',
    //         'billsToday',
    //         'totalDebts',
    //         'lowStock',
    //         'recentBills',
    //         'salesLabels',
    //         'salesData',
    //         'debtsLabels',
    //         'debtsData'
    //     ));
    // }
    public function dashboard()
    {
        // التحقق من الصلاحيات
        abort_if(Gate::denies('view-dashboard'), 403);

        // 1️⃣ إجمالي المبيعات اليوم
        $salesToday = PosBill::whereDate('created_at', now())->sum('net_amount');

        // 2️⃣ عدد الفواتير اليوم
        $billsToday = PosBill::whereDate('created_at', now())->count();

        // 3️⃣ إجمالي الديون المفتوحة
        $totalDebts = Debt::where('status', 'open')->sum('remaining_amount');

        // 4️⃣ عدد المنتجات منخفضة المخزون (<5)
        $lowStock = BalanceStore::select('product_id', DB::raw('SUM(remaining_quantity) as total_quantity'))
            ->groupBy('product_id')
            ->having('total_quantity', '<', 5)
            ->count();

        // 5️⃣ آخر 10 فواتير مع بيانات الزبون
        $recentBills = PosBill::with('customer')
            ->latest()
            ->take(10)
            ->get();

        // 6️⃣ بيانات المبيعات آخر 7 أيام (للرسم البياني)
        $salesDataRaw = PosBill::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(net_amount) as total')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(6)->startOfDay())
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // تجهيز بيانات المبيعات
        $salesLabels = [];
        $salesData = [];
        $period = Carbon::now()->subDays(6)->daysUntil(Carbon::now());
        foreach ($period as $day) {
            $formatted = $day->toDateString();
            $salesLabels[] = $day->format('d-m');
            $salesData[] = (float) ($salesDataRaw->firstWhere('date', $formatted)->total ?? 0);
        }

        // 7️⃣ بيانات الديون حسب العملاء
        $debtsDataRaw = Debt::with('customer')
            ->select('customer_id', DB::raw('SUM(remaining_amount) as total'))
            ->where('status', 'open')
            ->groupBy('customer_id')
            ->get();

        $debtsLabels = $debtsDataRaw->map(fn($d) => $d->customer?->name ?? __('messages.customer.general_customer'))->toArray();
        $debtsData   = $debtsDataRaw->pluck('total')->map(fn($v) => (float)$v)->toArray();

        // 8️⃣ عرض كل البيانات في صفحة Blade
        return view('admin.dashboard', compact(
            'salesToday',
            'billsToday',
            'totalDebts',
            'lowStock',
            'recentBills',
            'salesLabels',
            'salesData',
            'debtsLabels',
            'debtsData'
        ));
    }


    // public function editPermission($userId)
    // {
    //     // جلب المستخدم مع صلاحياته الإضافية الحالية
    //     $user = User::with('extraPermissions')->findOrFail($userId);

    //     // جلب كل الصلاحيات من جدول permissions
    //     $allPermissions = Permission::pluck('name')->toArray();

    //     // إرسال البيانات إلى صفحة Blade
    //     return view('admin.extraPermissions', compact('user', 'allPermissions'));
    // }

    public function editPermission($userId)
    {
        $user = User::with(['roles.permissions', 'extraPermissions', 'deniedPermissions'])->findOrFail($userId);

        // كل الصلاحيات الممكنة من جدول Permissions
        $allPermissions = Permission::pluck('name')->toArray();

        // الصلاحيات الحالية للمستخدم
        $rolePermissions = $user->roles->flatMap(fn($role) => $role->permissions->pluck('name'))->toArray();
        $extraPermissions = $user->extraPermissions->pluck('permission_key')->toArray();
        $deniedPermissions = $user->deniedPermissions->pluck('permission_key')->toArray();

        // تمرير كل المتغيرات للـ Blade
        return view('admin.extraPermissions', compact(
            'user',
            'allPermissions',
            'rolePermissions',
            'extraPermissions',
            'deniedPermissions'
        ));
    }


    public function updateExtraPermissions(Request $request, User $user)
    {
        $permissionsInput = $request->input('permissions', []);

        $extraPermissions = [];
        $deniedPermissions = [];

        foreach ($permissionsInput as $permKey => $values) {
            if (isset($values['extra'])) {
                $extraPermissions[] = $permKey;
            }
            if (isset($values['denied'])) {
                $deniedPermissions[] = $permKey;
            }
        }

        // صلاحيات الدور الحالية
        $rolePermissions = $user->roles->flatMap(fn($role) => $role->permissions->pluck('name'))->toArray();

        // فقط الصلاحيات المختارة التي ليست ضمن صلاحيات الدور → Extra Permissions
        $extraPermissions = array_diff($extraPermissions, $rolePermissions);

        // حذف القديم وإضافة الجديد للصلاحيات الإضافية
        $user->extraPermissions()->delete();
        foreach ($extraPermissions as $perm) {
            $user->extraPermissions()->create(['permission_key' => $perm]);
        }

        // حذف القديم وإضافة الجديد للصلاحيات الممنوعة
        $user->deniedPermissions()->delete();
        foreach ($deniedPermissions as $perm) {
            $user->deniedPermissions()->create(['permission_key' => $perm]);
        }

        return to_route('admin.users.index')->with('success', 'تم تحديث الصلاحيات بنجاح');
    }




}
