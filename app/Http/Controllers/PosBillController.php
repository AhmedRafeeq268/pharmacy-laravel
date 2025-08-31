<?php

namespace App\Http\Controllers;

use Mpdf\Mpdf;
use Stripe\Charge;
use Stripe\Stripe;
use App\Models\Debt;
use App\Models\PosBill;
use App\Models\Product;
use App\Models\customer;
use App\Models\PosSession;
use Illuminate\Http\Request;
use App\Models\PosBillDetails;
use App\Exports\PosBillsExport;
use App\Models\CashBoxTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Stripe\Exception\ApiErrorException;
use App\Models\CashBoxTransactionSecond;

class PosBillController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $posBills = PosBill::with(['customer', 'employee'])
            ->where('total_amount', '>', 0)
            ->when($search, function ($query) use ($search) {
                $query->where('id', 'like', "%$search%")
                      ->orWhereHas('customer', fn($q) => $q->where('name', 'like', "%$search%"))
                      ->orWhereHas('employee', fn($q) => $q->where('name', 'like', "%$search%"));
            })
            ->orderBy('id')
            ->paginate(8)
            ->appends($request->all());

        if ($request->ajax()) {
            return view('pos._table2', compact('posBills'))->render();
        }

        return view('pos.index', compact('posBills'));
    }

    public function export(Request $request)
    {
        $search = $request->input('search');

        $posBills = PosBill::with(['customer', 'employee'])
            ->where('total_amount', '>', 0)
            ->when($search, function ($query) use ($search) {
                $query->where('id', 'like', "%$search%")
                      ->orWhereHas('customer', fn($q) => $q->where('name', 'like', "%$search%"))
                      ->orWhereHas('employee', fn($q) => $q->where('name', 'like', "%$search%"));
            })->get();

        if ($posBills->isEmpty()) {
            return redirect()->back()->with('error', 'لا توجد بيانات لتصديرها.');
        }

        return Excel::download(new PosBillsExport($posBills), 'posBills.xlsx');
    }

    public function exportPDF(Request $request)
    {
        $search = $request->input('search');

        $posItems= PosBill::with(['customer', 'employee'])
            ->where('total_amount', '>', 0)
            ->when($search, function ($query) use ($search) {
                $query->where('id', 'like', "%$search%")
                      ->orWhereHas('customer', fn($q) => $q->where('name', 'like', "%$search%"))
                      ->orWhereHas('employee', fn($q) => $q->where('name', 'like', "%$search%"));
            })->get();

        if ($posItems->isEmpty()) {
            return redirect()->back()->with('error', 'لا توجد بيانات لتصديرها.');
        }

        // تحميل Blade كـ HTML
        $html = view('pos.posItemsPDF', compact('posItems'))->render();

        // إعداد mPDF
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => app()->getLocale() == 'ar' ? 'Cairo' : 'dejavusans',
            'directionality' => app()->getLocale() == 'ar' ? 'rtl' : 'ltr',
        ]);

        // كتابة HTML في PDF
        $mpdf->WriteHTML($html);
        $filename = app()->getLocale() == 'ar' ? 'تقرير المبيعات.pdf' : 'Pos_Report.pdf';

        // تحميل PDF مباشرة
        return $mpdf->Output($filename, 'D');
    }

    public function create(Request $request, $pos_bill_id = null)
    {
        $customers = customer::all(); // <-- اجلب العملاء دائماً

        $currentSession = PosSession::where('user_id', Auth::id())
        ->where('status', 'open')
        ->latest()
        ->first();

        $posBillsDetails = PosBillDetails::where('pos_bill_id', $pos_bill_id)->get();

        if (!$pos_bill_id) {
            return view('pos.create', [
                'pos_bill_id'       => null,
                'posBillsDetails'   => collect(),
                'customers'         => $customers, // <-- تمرير العملاء هنا
                'currentSession'    => $currentSession,
            ]);
        }

        return view('pos.create',compact('customers','pos_bill_id','posBillsDetails','currentSession') );
    }


    public function store(Request $request, $pos_bill_id = null)
    {
        $request->validate([
            'barcode'  => ['required'],
            'quantity' => ['required', 'numeric', 'min:1'],
        ]);

        $barcode   = $request->barcode;
        $quantity  = $request->quantity;
        $discount  = $request->discount ?? 0;

        $user_id = Auth::id() ?? 1;

        // الجلسة المفتوحة الحالية للصندوق
        $currentSessionId = PosSession::where('user_id', $user_id)
                                    ->where('status', 'open')
                                    ->latest()
                                    ->value('id');

        $product = Product::where('barcode', $barcode)->first();

        if (!$product) {
            return back()->with('error', __('messages.pos.product_not_found'));
        }

        if (!$pos_bill_id || !PosBill::find($pos_bill_id)) {
            $posBill = PosBill::create([
                'total_amount' => 0,
                'discount'     => 0,
                'net_amount'   => 0,
                'employee_id'  => 0,
                'session_id'   => $currentSessionId,
            ]);
            $pos_bill_id = $posBill->id;
        }

        $unit_price = $product->price_sell;
        $price      = $unit_price * $quantity;

        $costPrice = $product->unit_price;
        $profit = ($unit_price - $costPrice) * $quantity;
        PosBillDetails::create([
            'pos_bill_id' => $pos_bill_id,
            'product_id'  => $product->id,
            'unit_price'  => $unit_price,
            'quantity'    => $quantity,
            'price'       => $price,
            'cost_price'   => $costPrice,
            'profit'       => $profit,
        ]);

        $total_amount = PosBillDetails::where('pos_bill_id', $pos_bill_id)->sum('price');
        $net_amount   = $total_amount - $discount;

        PosBill::where('id', $pos_bill_id)->update([
            'customer_id' => 0,
            'employee_id' => Auth::id(),
            'total_amount' => $total_amount,
            'discount'     => $discount,
            'net_amount'   => $net_amount,
            'session_id'   => $currentSessionId,
        ]);

        return redirect()->route('pos.create', ['pos_bill_id' => $pos_bill_id])
                         ->with('success', __('messages.added'));
    }

    public function fetchProduct($barcode)
    {
        $product = Product::where('barcode', $barcode)->first();

        if ($product) {
            return response()->json([
                'success' => true,
                'product' => [
                    'name'       => $product->name,
                    'price_sell' => $product->price_sell,
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => __('messages.pos.product_not_found'),
        ]);
    }


    public function finish(Request $request, $pos_bill_id)
{
    $validated = $request->validate([
        'discount'       => 'nullable|numeric|min:0',
        'payment_status' => 'required|in:cash,visa,debt',
        'customer_id'    => 'required_if:payment_status,debt|nullable|exists:customers,id',
    ]);

    $discount      = $validated['discount'] ?? 0;
    $paymentStatus = $validated['payment_status'];

    $posBill = PosBill::findOrFail($pos_bill_id);

    $total     = $posBill->details()->sum('price');
    $netAmount = max($total - $discount, 0);

    if ($paymentStatus === 'debt' && empty($validated['customer_id'])) {
        return response()->json([
            'success' => false,
            'message' => 'يجب اختيار الزبون عند الدفع بالدين.'
        ], 422);
    }

    // تحديث بيانات الفاتورة
    $posBill->update([
        'total_amount'   => $total,
        'discount'       => $discount,
        'net_amount'     => $netAmount,
        'payment_status' => $paymentStatus,
        'customer_id'    => $paymentStatus === 'debt' ? $validated['customer_id'] : $posBill->customer_id,
        'finished_by'    => Auth::id(),
        'status'         => 'finished',
    ]);

    if ($paymentStatus === 'debt') {
        // إنشاء سجل دين جديد خاص بهذه الفاتورة والزبون
        Debt::create([
            'customer_id'      => $validated['customer_id'],
            'pos_bill_id'      => $posBill->id,
            'total_amount'     => $netAmount,
            'remaining_amount' => $netAmount,
            'status'           => 'open', // فاتورة مفتوحة الدين
        ]);
    } else {
        // تسجيل حركة مالية عند الكاش أو الفيزا
        CashBoxTransaction::create([
            'amount'      => $netAmount,
            'type'        => 'in',
            'description' => __('messages.pos.payment_' . $paymentStatus) . ' - فاتورة #' . $posBill->id,
            'pos_bill_id' => $posBill->id,
            'session_id'  => $posBill->session_id,
        ]);
    }

    return response()->json([
        'success' => true,
        'bill_id' => $posBill->id,
        'message' => __('messages.pos.finished_entry'),
    ]);
}




    public function closeCashbox(Request $request)
    {
        $session = PosSession::where('user_id', Auth::id())
            ->where('status', 'open')
            ->first();
        if ($session) {
            // الرصيد الافتتاحي
            $openingBalance = $session->opening_balance;
            // مجموع net_amount لنفس الجلسة
            $totalNetAmount = PosBill::where('session_id', $session->id)
                ->sum('net_amount');

            // الرصيد الختامي = رصيد افتتاحي + amount + net_amount
            $closingBalance = $openingBalance + $totalNetAmount;

            $employeeId = $request->employee_id;

            // $nets_amount = PosBill::where('employee_id', $employeeId)
            // ->where('is_closed_with_cashbox', 0)
            // ->sum('net_amount');

            $received_amount = $openingBalance;

            $delivered_amount = $closingBalance;

            // PosBill::where('employee_id', $employeeId)
            // ->where('is_closed_with_cashbox', 0)
            // ->update(['is_closed_with_cashbox' => 1]);

            $cashbox = CashBoxTransactionSecond::create([
                'employee_id'      => Auth::id(),
                'received_amount'  => $received_amount,
                'delivered_amount' => $delivered_amount,
            ]);
        }


        return response()->json([
        'success' => true,
        'cashbox_id' => $cashbox->id,  // <--- هنا ID من الداتا بيز
        'message' => __('messages.pos.cashbox_closed_successfully')
        ]);

        // return redirect()->back()->with('success', __('messages.pos.cashbox_closed_successfully'));
    }

    public function print($id)
    {
        $posBill = PosBill::with('details.product', 'employee')->findOrFail($id);
        return view('pos.printPay', compact('posBill'));
    }

        public function cashboxReport($sessionId)
    {
        $session = PosSession::findOrFail($sessionId);

        $opening_balance = $session->opening_balance;
        $closing_balance = $session->closing_balance;
        $casherName = $session->user->name;
        $opened_at = $session->opened_at;
        $closed_at = $session->closed_at;
        $closingId = $sessionId;

        $totalPayments = CashBoxTransaction::where('session_id', $sessionId)
                            ->where('type', 'in')
                            ->sum('amount');
        $totalReturns = CashBoxTransaction::where('session_id', $sessionId)
                            ->where('type', 'refund')
                            ->sum('amount');
        $totalDescounts = PosBill::where('session_id', $sessionId)->sum('discount');
        $totalAmounts = PosBill::where('session_id', $sessionId)->sum('total_amount');
        $netAmounts = PosBill::where('session_id', $sessionId)->sum('net_amount');
        $payDebt = PosBill::where('session_id', $sessionId)->where('payment_status','debt')->sum('net_amount');
        $payVisa = PosBill::where('session_id', $sessionId)->where('payment_status','visa')->sum('net_amount');
        $payCash = PosBill::where('session_id', $sessionId)->where('payment_status','cash')->sum('net_amount');

        return view('pos.printCashBox',compact('opening_balance','closing_balance','casherName','opened_at','closed_at','closingId','totalPayments','totalReturns','totalDescounts','totalAmounts','netAmounts','payDebt','payVisa','payCash',));


    }




    public function showPaymentPage($pos_bill_id)
    {
        $bill = PosBill::findOrFail($pos_bill_id);
        return view('pos.payment', compact('bill'));  // أنشئ هذه الصفحة
    }


    public function processPayment(Request $request, $pos_bill_id)
    {
        $bill = PosBill::findOrFail($pos_bill_id);

        $request->validate([
            'stripeToken' => 'required|string',
        ]);

        try {
            Stripe::setApiKey(env('STRIPE_SECRET'));

            // تنفيذ الدفع عبر Stripe
            $charge = Charge::create([
                'amount' => intval($bill->net_amount * 100), // Stripe يستخدم السنتات
                'currency' => 'usd',
                'description' => 'POS Payment for Bill #' . $bill->id,
                'source' => $request->stripeToken,
            ]);

            // تحديث حالة الفاتورة
            $bill->update([
                'paid'            => true,
                'payment_status'  => 'visa',
                'status'          => 'finished',
                'finished_by'     => Auth::id(),
            ]);

            // تسجيل الحركة المالية في الصندوق
            CashBoxTransaction::create([
                'amount'      => $bill->net_amount,
                'type'        => 'in',
                'description' => __('messages.pos.payment_visa') . ' - فاتورة #' . $bill->id,
                'pos_bill_id' => $bill->id,
                'session_id'  => $bill->session_id,
            ]);

            return redirect()->route('pos.index')->with('success', 'تم الدفع بالفيزا بنجاح');

        } catch (ApiErrorException $e) {
            return back()->with('error', 'فشل الدفع: ' . $e->getMessage());
        }
    }



}
