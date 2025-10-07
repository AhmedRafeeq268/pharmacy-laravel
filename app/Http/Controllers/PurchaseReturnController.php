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
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PurchaseReturnsExport;
use App\Http\Requests\StoreParchaseReturnRequest;
use App\Http\Requests\UpdateParchaseReturnRequest;

class PurchaseReturnController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        abort_if(Gate::denies('view-purchase-return'), 403);
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
        abort_if(Gate::denies('create-purchase-return'), 403);
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
    public function store(StoreParchaseReturnRequest $request)
    {
        // التحقق من صحة البيانات
        $data = $request->validated();

        // استخراج القيم من الطلب
        $purchase_bill_id = $data['purchase_bill_id'];
        $supplier_id = $data['supplier_id'];
        $items = $data['items'];

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
                $refunded_in_cash = $item['refunded_in_cash'] ?? false;

                // إنشاء السجل في purchase_returns
                PurchaseReturn::create([
                    'purchase_bill_id' => $purchase_bill_id,
                    'supplier_id'      => $supplier_id,
                    'product_id'       => $product_id,
                    'quantity'         => $quantity,
                    'return_amount'    => $return_amount,
                    'reason'           => $reason,
                    'refunded_in_cash' => $refunded_in_cash,
                    'created_by'       => $user_id,
                    'session_id'       => $currentSessionId,
                ]);

                // خصم الكمية من المخزون
                Product::where('id', $product_id)->decrement('quantity', $quantity);

                // إذا تم رد نقداً → سجل حركة في الصندوق
                if ($refunded_in_cash) {
                    if (!$currentSessionId) {
                        throw new \Exception('لا توجد جلسة صندوق مفتوحة لإرجاع المبلغ نقداً.');
                    }

                    CashBoxTransaction::create([
                        'session_id' => $currentSessionId,
                        'type'       => 'expense',
                        'amount'     => $return_amount,
                        'note'       => 'إرجاع للمورد (منتج ID: ' . $product_id . ')',
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
        abort_if(Gate::denies('view-purchase-return'), 403);
        $purchaseReturn = PurchaseReturn::with('purchaseBill','supplier','product','creator')->findOrFail($id);
        return view('purchaseReturns.show',compact('purchaseReturn'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($purchaseReturnId)
    {
        abort_if(Gate::denies('edit-purchase-return'), 403);
        $purchaseReturn = PurchaseReturn::findOrFail($purchaseReturnId);
        return view('purchaseReturns.edit',compact('purchaseReturn'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateParchaseReturnRequest $request, $purchaseReturnId)
    {
        $data = $request->validated();

        $purchaseReturn = PurchaseReturn::findOrFail($purchaseReturnId);

        // القيم القديمة
        $oldQuantity = $purchaseReturn->quantity;
        $oldAmount = $purchaseReturn->return_amount;
        $oldRefunded = $purchaseReturn->refunded_in_cash;
        $productId = $purchaseReturn->product_id;

        // القيم الجديدة
        $newQuantity = $data['quantity'];
        $newAmount = $data['return_amount'];
        $newRefunded = $data['refunded_in_cash'] ?? false;
        $reason = $data['reason'] ?? null;

        // تحديث المرتجع
        $purchaseReturn->update([
            'quantity' => $newQuantity,
            'return_amount' => $newAmount,
            'reason' => $reason,
            'refunded_in_cash' => $newRefunded,
            'edited_by' => Auth::id() ?? 1,
        ]);

        // تعديل المخزون
        $quantityDiff = $newQuantity - $oldQuantity;
        if ($quantityDiff != 0) {
            $product = Product::findOrFail($productId);
            $product->quantity -= $quantityDiff;
            if ($product->quantity < 0) {
                return back()->withErrors(['quantity' => 'الكمية في المخزون لا تكفي للتعديل.']);
            }
            $product->save();
        }

        // تعديل حركة الصندوق
        if ($newRefunded) {
            $sessionId = PosSession::where('user_id', Auth::id())
                        ->where('status', 'open')
                        ->latest()
                        ->value('id');
            if (!$sessionId) {
                return back()->withErrors(['session' => 'لا توجد جلسة صندوق مفتوحة لتعديل المرتجع النقدي.']);
            }

            $amountDiff = $newAmount - $oldAmount;
            if ($amountDiff != 0) {
                $type = $amountDiff > 0 ? 'expense' : 'income';
                CashBoxTransaction::create([
                    'session_id' => $sessionId,
                    'type' => $type,
                    'amount' => abs($amountDiff),
                    'note' => 'تعديل مبلغ مرتجع المورد (ID: ' . $productId . ')',
                ]);
            }
        }

        $page = $request->get('page', 1);
        return to_route('purchaseReturns.index', ['page' => $page])
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
