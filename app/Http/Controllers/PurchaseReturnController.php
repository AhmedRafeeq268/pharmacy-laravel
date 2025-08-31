<?php

namespace App\Http\Controllers;

use Mpdf\Mpdf;
use App\Models\Product;
use App\Models\PosSession;
use Illuminate\Http\Request;
use App\Models\PurchaseReturn;
use App\Models\PurchasesBills;
use App\Models\CashBoxTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PurchaseReturnsExport;

class PurchaseReturnController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $purchaseReturns = PurchaseReturn::with(['purchaseBill','supplier','product','creator'])
            ->when($search, function ($query) use ($search) {
                    $query->where('purchase_bill_id', 'like', "%{$search}%")
                    ->orWhereHas('supplier', fn($q) => $q->where('name', 'like', "%$search%"))
                    ->orWhereHas('product', fn($q) => $q->where('name', 'like', "%$search%"));
        })->orderBy('id', 'desc')->paginate(8); // حدد عدد العناصر في كل صفحة

        // إذا كان الطلب AJAX نعيد جزء الـ Table فقط
        if ($request->ajax()) {
            return view('purchaseReturns._table', compact('purchaseReturns'))->render();
        }

        return view('purchaseReturns.index',compact('purchaseReturns'));
    }

    public function export(Request $request)
    {
        $search = $request->input('search');

        $purchaseReturns = PurchaseReturn::with(['purchaseBill','supplier','product','creator'])
            ->when($search, function ($query) use ($search) {
                    $query->where('purchase_bill_id', 'like', "%{$search}%")
                    ->orWhereHas('supplier', fn($q) => $q->where('name', 'like', "%$search%"))
                    ->orWhereHas('product', fn($q) => $q->where('name', 'like', "%$search%"));
        })->get();

        if ($purchaseReturns->isEmpty()) {
            return redirect()->back()->with('error', 'لا توجد بيانات لتصديرها.');
        }

        return Excel::download(new PurchaseReturnsExport($purchaseReturns), 'purchaseReturn.xlsx');
    }

    public function exportPDF(Request $request)
    {
        $search = $request->input('search');

        $purchaseReturnItems = PurchaseReturn::with(['purchaseBill','supplier','product','creator'])
            ->when($search, function ($query) use ($search) {
                    $query->where('purchase_bill_id', 'like', "%{$search}%")
                    ->orWhereHas('supplier', fn($q) => $q->where('name', 'like', "%$search%"))
                    ->orWhereHas('product', fn($q) => $q->where('name', 'like', "%$search%"));
        })->get();

        if ($purchaseReturnItems->isEmpty()) {
            return redirect()->back()->with('error', 'لا توجد بيانات لتصديرها.');
        }

        // تحميل Blade كـ HTML
        $html = view('purchaseReturns.purchaseReturnItemsPDF', compact('purchaseReturnItems'))->render();

        // إعداد mPDF
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => app()->getLocale() == 'ar' ? 'Cairo' : 'dejavusans',
            'directionality' => app()->getLocale() == 'ar' ? 'rtl' : 'ltr',
        ]);

        // كتابة HTML في PDF
        $mpdf->WriteHTML($html);
        $filename = app()->getLocale() == 'ar' ? 'تقرير مرجعات المنتجات.pdf' : 'PurchaseReturn_Report.pdf';

        // تحميل PDF مباشرة
        return $mpdf->Output($filename, 'D');
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

        return view('purchaseReturns.create', compact('bills', 'bill'));
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
     public function show($id){
        $purchaseReturn = PurchaseReturn::with('purchaseBill','supplier','product','creator')->findOrFail($id);
        return view('purchaseReturns.show',compact('purchaseReturn'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($purchaseReturnId)
    {
        $purchaseReturn = PurchaseReturn::findOrFail($purchaseReturnId);
        return view('purchaseReturns.edit',compact('purchaseReturn'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request ,$purchaseReturnId){
        $request->validate([
        'quantity' => 'required|integer|min:1',
        'return_amount' => 'required|numeric|min:0',
        'reason' => 'nullable|string|max:255',
        'refunded_in_cash' => 'required|boolean',
        ]);

        $purchaseReturn = PurchaseReturn::findOrFail($purchaseReturnId);

        // القيم القديمة
        $oldQuantity = $purchaseReturn->quantity;
        $oldAmount = $purchaseReturn->return_amount;
        $oldRefunded = $purchaseReturn->refunded_in_cash;
        $productId = $purchaseReturn->product_id;

        // القيم الجديدة من الطلب
        $newQuantity = $request->quantity;
        $newAmount = $request->return_amount;
        $newRefunded = $request->refunded_in_cash;

        // تحديث المرتجع
        $purchaseReturn->update([
            'quantity' => $newQuantity,
            'return_amount' => $newAmount,
            'reason' => $request->reason,
            'refunded_in_cash' => $newRefunded,
            'edited_by' => Auth::id() ?? 1,
        ]);

        // تعديل المخزون (فرق الكمية)
        $quantityDiff = $newQuantity - $oldQuantity;
        if ($quantityDiff != 0) {
            // إذا الفرق موجب ⇒ نخصم من المخزون
            // إذا الفرق سالب ⇒ نعيد للمخزون
            Product::where('id', $productId)->increment('quantity', -$quantityDiff);
        }

        // تعديل حركة الصندوق إذا كان تم رد نقداً
        $sessionId = PosSession::where('user_id', Auth::id())
                       ->where('status', 'open')
                       ->latest()
                       ->value('id');
        if ($newRefunded) {
            $amountDiff = $newAmount - $oldAmount;
            if ($amountDiff != 0 && $sessionId) {
                $type = $amountDiff > 0 ? 'expense' : 'income'; // زيادة = expense، نقصان = income
                CashBoxTransaction::create([
                    'session_id' => $sessionId,
                    'type' => $type,
                    'amount' => abs($amountDiff),
                    'note' => 'تعديل مبلغ مرتجع المورد (ID: ' . $productId . ')',
                    'created_at' => now(),
                ]);
            }
        }
        $page = $request->get('page', 1);
        return to_route('purchaseReturns.index',['page' => $page])
        ->with('success', __('messages.updated'));
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request ,$purchaseReturnId)
    {
        $purchaseReturn = PurchaseReturn::find($purchaseReturnId);
        if (!$purchaseReturn)
        {
            return redirect()->back()->with('error', __('messages.not_found'));
        }
        $purchaseReturn->delete();
        $page = $request->get('page', 1);
        return to_route('purchaseReturns.index',['page' => $page])
        ->with('success', __('messages.deleted'));
    }
}
