<?php

namespace App\Http\Controllers;

use Mpdf\Mpdf;
use App\Models\Debt;
use App\Models\PosBill;
use App\Models\Product;
use App\Models\Customer;
use App\Models\PosSession;
use App\Models\DebtPayment;
use App\Models\SalesReturn;
use Illuminate\Http\Request;
use App\Models\PosBillDetails;
use App\Exports\PosBillsExport;
use App\Models\SalesReturnDetail;
use App\Exports\SalesReturnExport;
use App\Models\CashBoxTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\StoreSalesReturnRequest;

class SalesReturnController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('edit-customer-return'), 403);
        $search = $request->input('search');

        $salesReturns = SalesReturn::with(['customer','details','bill'])
            ->when($search, function ($query) use ($search) {
                    $query->where('pos_bill_id', 'like', "%{$search}%")
                    ->orWhere('refund_method', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn($q) => $q->where('name', 'like', "%$search%"));
        })->orderBy('id', 'desc')->paginate(8); // حدد عدد العناصر في كل صفحة

        // إذا كان الطلب AJAX نعيد جزء الـ Table فقط
        if ($request->ajax()) {
            return view('salesReturn._table', compact('salesReturns'))->render();
        }

        return view('salesReturn.index',compact('salesReturns'));
    }

    public function export(Request $request)
    {
        $search = $request->input('search');

        $salesReturns = SalesReturn::with(['customer','details','bill'])
            ->when($search, function ($query) use ($search) {
                    $query->where('pos_bill_id', 'like', "%{$search}%")
                    ->orWhere('refund_method', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn($q) => $q->where('name', 'like', "%$search%"));
        })->get();


        if ($salesReturns->isEmpty()) {
            return redirect()->back()->with('error', 'لا توجد بيانات لتصديرها.');
        }

        return Excel::download(new SalesReturnExport($salesReturns), 'salesReturns.xlsx');
    }

    public function exportPDF(Request $request)
    {
        $search = $request->input('search');

        $salesReturnsItems = SalesReturn::with(['customer','details','bill'])
            ->when($search, function ($query) use ($search) {
                    $query->where('pos_bill_id', 'like', "%{$search}%")
                    ->orWhere('refund_method', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn($q) => $q->where('name', 'like', "%$search%"));
        })->get();

        if ($salesReturnsItems->isEmpty()) {
            return redirect()->back()->with('error', 'لا توجد بيانات لتصديرها.');
        }

        // تحميل Blade كـ HTML
        $html = view('salesReturn.salesReturnItemsPDF', compact('salesReturnsItems'))->render();

        // إعداد mPDF
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => app()->getLocale() == 'ar' ? 'Cairo' : 'dejavusans',
            'directionality' => app()->getLocale() == 'ar' ? 'rtl' : 'ltr',
        ]);

        // كتابة HTML في PDF
        $mpdf->WriteHTML($html);
        $filename = app()->getLocale() == 'ar' ? 'تقرير مرجعات الزبائن.pdf' : 'SalesReturn_Report.pdf';

        // تحميل PDF مباشرة
        return $mpdf->Output($filename, 'D');
    }


    public function create(Request $request)
    {
        abort_if(Gate::denies('create-customer-return'), 403);
        $bill_id = $request->bill_id;
        $customers = Customer::all();
        $products = Product::all();
        $bill = null;
        $billProducts = [];

        if ($bill_id) {
            $bill = PosBill::with('customer')->find($bill_id);

            if ($bill) {
                $customers = Customer::where('id', $bill->customer_id)->get();
                $billProducts = PosBillDetails::where('pos_bill_id', $bill_id)->with('product')->get();
            } else {
                return redirect()->back()->with('error', 'رقم الفاتورة غير موجود');
            }
        }

        return view('salesReturn.create', compact('customers', 'products', 'bill', 'billProducts', 'bill_id'));
    }

    public function store(StoreSalesReturnRequest $request)
    {
        // ✅ التحقق من صحة البيانات
        $data = $request->validated();

        // ✅ استبعاد الأصناف ذات الكمية = 0
        $items = array_filter($data['items'], fn($i) => $i['quantity'] > 0);
        if (empty($items)) {
            return back()->with('error', 'لم يتم اختيار أي منتجات صالحة للإرجاع.');
        }

        // ✅ التحقق من الفاتورة الأصلية إن وُجدت
        if (!empty($data['bill_id'])) {
            $billDetails = PosBillDetails::where('pos_bill_id', $data['bill_id'])
                ->get()
                ->keyBy(fn($i) => (string)$i->product_id);

            foreach ($items as $item) {
                $productId = $item['product_id'];
                $product = Product::find($productId);

                if (!isset($billDetails[(string)$productId])) {
                    return back()->with('error', "المنتج {$product->name} غير موجود بالفاتورة الأصلية");
                }

                $purchased = $billDetails[(string)$productId]->quantity;

                // ✅ مجموع الكمية الراجعة سابقًا
                $returned  = SalesReturnDetail::whereHas('salesReturn', fn($q) => $q->where('pos_bill_id', $data['bill_id']))
                                ->where('product_id', $productId)
                                ->sum('quantity');

                $remaining = $purchased - $returned;

                if ($remaining <= 0) {
                    return back()->with('error', "المنتج {$product->name} تم إرجاعه بالكامل مسبقًا.");
                }

                if ($item['quantity'] > $remaining) {
                    return back()->with('error',
                        "لا يمكن إرجاع المنتج {$product->name} بهذه الكمية.
                        الكمية المسموح بها: {$remaining}.
                        لقد قمت بإرجاع {$returned} من أصل {$purchased}."
                    );
                }
            }
        }

        // ✅ حساب المجموع الكلي
        $total = collect($items)->sum(fn($i) => $i['price'] * $i['quantity']);

        // ✅ تحديد customer_id إن وُجد
        $customerId = $data['customer_id'] ?? null;

        // ✅ تنفيذ العملية داخل Transaction
        try {
            $return = DB::transaction(function () use ($data, $items, $total, $customerId) {
                // إنشاء سجل المرتجع
                $return = SalesReturn::create([
                    'customer_id'   => $customerId,
                    'refund_method' => $data['refund_method'],
                    'total'         => $total,
                    'pos_bill_id'   => $data['bill_id'] ?? null,
                    'user_id'       => Auth::id(),
                ]);

                // جلب المنتجات لتحديث المخزون
                $products = Product::whereIn('id', array_column($items, 'product_id'))->get()->keyBy('id');

                // حفظ تفاصيل المرتجع + تحديث المخزون
                foreach ($items as $i) {
                    SalesReturnDetail::create([
                        'sales_return_id' => $return->id,
                        'product_id'      => $i['product_id'],
                        'price'           => $i['price'],
                        'quantity'        => $i['quantity'],
                        'subtotal'        => $i['price'] * $i['quantity'],
                    ]);

                    $products[$i['product_id']]->increment('quantity', $i['quantity']);
                }

                // ✅ تسوية طريقة الإرجاع
                if ($data['refund_method'] === 'debt') {
                    if (!$customerId) {
                        throw new \Exception('لا يمكن اختيار طريقة الدين بدون زبون.');
                    }
                    $this->handleDebtRefund($customerId, $total);
                } else {
                    $this->handleCashRefund($return->id, $total);
                }

                return $return;
            });
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        // ✅ رسالة نجاح
        $msg = $data['refund_method'] === 'cash'
            ? "تم إرجاع المنتجات بنجاح. المبلغ الكلي المرجع نقدًا هو: {$total}"
            : "تم حفظ المرتجع بنجاح";

        return redirect()->route('salesReturn.create')->with('success', $msg);
    }


    /**
     * ✅ تسوية الدين
     */
    private function handleDebtRefund($customerId, &$total)
    {
        $debts = Debt::where('customer_id', $customerId)->where('is_paid', false)->orderBy('created_at')->get();
        if ($debts->isEmpty()) throw new \Exception('لا يوجد ديون مفتوحة لهذا الزبون');

        $sessionId = PosSession::where('user_id', Auth::id())->where('status', 'open')->latest()->value('id');

        foreach ($debts as $debt) {
            if ($total <= 0) break;

            $pay = min($total, $debt->remaining_amount);
            $debt->remaining_amount -= $pay;
            if ($debt->remaining_amount <= 0) $debt->is_paid = true;
            $debt->save();

            DebtPayment::create([
                'debt_id'        => $debt->id,
                'amount_paid'    => $pay,
                'paid_by'        => Auth::id(),
                'payment_method' => 'cash',
                'payment_date'   => now(),
                'notes'          => 'دفع عبر المرتجع',
            ]);

            CashBoxTransaction::create([
                'amount'      => $pay,
                'type'        => 'in',
                'description' => "تسوية دين للزبون #$customerId - دين #{$debt->id}",
                'session_id'  => $sessionId,
            ]);

            $total -= $pay;
        }
    }

    /**
     * ✅ تسجيل عملية نقدية
     */
    private function handleCashRefund($returnId, $total)
    {
        $sessionId = PosSession::where('user_id', Auth::id())->where('status', 'open')->latest()->value('id');
        if (!$sessionId) throw new \Exception('لا توجد جلسة بيع مفتوحة.');

        CashBoxTransaction::create([
            'session_id' => $sessionId,
            'type'       => 'refund',
            'amount'     => -$total, // بالسالب لأنه خصم من الصندوق
            'note'       => "مرتجع زبون #$returnId",
        ]);
    }



    public function getBillDetails($billNumber)
    {
        $bill = PosBill::with('customer')->where('id', $billNumber)->first();

        if (!$bill) {
            return response()->json(['message' => 'الفاتورة غير موجودة'], 404);
        }

        $items = PosBillDetails::where('pos_bill_id', $bill->id)
            ->with('product')
            ->get()
            ->map(function ($detail) use ($bill) {
                $returnedQty = SalesReturnDetail::whereHas('salesReturn', function($q) use ($bill) {
                                        $q->where('pos_bill_id', $bill->id);
                                    })
                                    ->where('product_id', $detail->product_id)
                                    ->sum('quantity');

                return [
                    'product_id'   => $detail->product_id,
                    'product_name' => $detail->product->name ?? 'منتج غير معروف',
                    'price'        => $detail->price,
                    'quantity'     => $detail->quantity,
                    'returned'     => $returnedQty,
                ];
            });

        return response()->json([
            'bill'     => $bill,
            'customer' => $bill->customer ?? null, // إذا لا يوجد زبون يصبح null
            'items'    => $items,
        ]);
    }

    public function show($id){
        abort_if(Gate::denies('view-customer-return'), 403);
        $salesReturn = SalesReturn::with('customer','details','bill')->findOrFail($id);
        return view('salesReturn.show',compact('salesReturn'));
    }


}
