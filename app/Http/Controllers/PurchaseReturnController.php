<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PosSession;
use Illuminate\Http\Request;
use App\Models\PurchaseReturn;
use App\Models\PurchasesBills;
use App\Models\CashBoxTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PurchaseReturnController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // فقط الفواتير التي لها مورد معرف
        $bills = PurchasesBills::with('supplier', 'details.product')
            ->whereHas('supplier')  // ضمان وجود المورد
            ->orderBy('id', 'desc')
            ->get();

        $bill = null;
        if ($request->has('bill_id')) {
            $bill = PurchasesBills::with('supplier', 'details.product')->findOrFail($request->bill_id);

            // فلترة التفاصيل للتأكد من وجود المنتج مرتبط بالتفصيل
            $bill->details = $bill->details->filter(function($detail) {
                return $detail->product !== null;
            });
        }

        return view('purchase_returns.create', compact('bills', 'bill'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // التحقق من صحة البيانات
        $request->validate([
            'purchase_bill_id' => 'required|exists:purchases_bills,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:0',
            'items.*.return_amount' => 'required|numeric|min:0',
            'items.*.reason' => 'nullable|string|max:255',
            'items.*.refunded_in_cash' => 'required|boolean',
        ]);

        // استخراج القيم من الطلب
        $purchase_bill_id = $request->purchase_bill_id;
        $supplier_id = $request->supplier_id;
        $items = $request->items;

        // المستخدم الحالي
        $user_id = Auth::id() ?? 1;

        // الجلسة المفتوحة الحالية للصندوق
        $currentSessionId = PosSession::where('user_id', $user_id)
                                    ->where('status', 'open')
                                    ->latest()
                                    ->value('id');

        // تنفيذ كل العمليات داخل معاملة قاعدة بيانات
        DB::beginTransaction();

        try {
            foreach ($items as $item) {
                $product_id = $item['product_id'];
                $quantity = $item['quantity'];
                $return_amount = $item['return_amount'];
                $reason = $item['reason'] ?? null;
                $refunded_in_cash = $item['refunded_in_cash'];

                // إنشاء السجل في purchase_returns
                PurchaseReturn::create([
                    'purchase_bill_id' => $purchase_bill_id,
                    'supplier_id' => $supplier_id,
                    'product_id' => $product_id,
                    'quantity' => $quantity,
                    'return_amount' => $return_amount,
                    'reason' => $reason,
                    'refunded_in_cash' => $refunded_in_cash,
                    'created_by' => $user_id,
                    'session_id' => $currentSessionId,

                ]);

                // خصم الكمية من المخزون
                Product::where('id', $product_id)->decrement('quantity', $quantity);

                // إذا تم رد نقداً → سجل حركة في الصندوق
                if ($refunded_in_cash && $currentSessionId) {
                    CashBoxTransaction::create([
                        'session_id' => $currentSessionId,
                        'type' => 'expense',
                        'amount' => $return_amount,
                        'note' => 'إرجاع للمورد (منتج ID: ' . $product_id . ')',
                        'created_at' => now(),
                    ]);
                }
            }

            DB::commit();
            return to_route('purchaseReturns.create')->with('success', 'تم حفظ المرتجعات بنجاح.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء الحفظ: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PurchaseReturn $purchaseReturn)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PurchaseReturn $purchaseReturn)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PurchaseReturn $purchaseReturn)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PurchaseReturn $purchaseReturn)
    {
        //
    }
}
