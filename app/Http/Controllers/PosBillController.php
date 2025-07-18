<?php

namespace App\Http\Controllers;

use App\Models\PosBill;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\PosBillDetails;
use App\Models\CashBoxTransaction;
use Illuminate\Support\Facades\DB;

class PosBillController extends Controller
{

    public function index(Request $request)
{
    $search = $request->search;

    $posBills = PosBill::with(['customer', 'employee'])->where('total_amount', '>', 0)
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



public function create(Request $request, $pos_bill_id = null)
{
    // لا تنشئ أي فاتورة هنا
    if (!$pos_bill_id) {
        return view('pos.create', ['pos_bill_id' => null,'posBillsDetails' => collect(),]);
    }

    // جلب تفاصيل الفاتورة إن كانت موجودة
    $posBillsDetails = PosBillDetails::where('pos_bill_id', $pos_bill_id)->get();

    return view('pos.create', ['pos_bill_id' => $pos_bill_id,'posBillsDetails' => $posBillsDetails,]);
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

    // جلب المنتج
    $product = Product::where('barcode', $barcode)->first();
    if (!$product) {
        return back()->with('error', __('messages.pos.product_not_found'));
    }

    // ❗ إنشاء الفاتورة فقط إذا لم تكن موجودة
    if (!$pos_bill_id || !PosBill::find($pos_bill_id)) {
        $posBill = PosBill::create([
            'total_amount' => 0,
            'discount'     => 0,
            'net_amount'   => 0,
            'employee_id'  => 0,
        ]);
        $pos_bill_id = $posBill->id;
    }

    // تفاصيل المنتج
    $unit_price = $product->unit_price;
    $price      = $unit_price * $quantity;

    PosBillDetails::create([
        'pos_bill_id' => $pos_bill_id,
        'product_id'  => $product->id,
        'unit_price'  => $unit_price,
        'quantity'    => $quantity,
        'price'       => $price,
    ]);

    // تحديث المجاميع
    $total_amount = PosBillDetails::where('pos_bill_id', $pos_bill_id)->sum('price');
    $net_amount   = $total_amount - $discount;

    PosBill::where('id', $pos_bill_id)->update([
        'customer_id' => 7,
        'employee_id' => 5,
        'total_amount' => $total_amount,
        'discount'     => $discount,
        'net_amount'   => $net_amount,
    ]);

    return redirect()->route('pos.create', ['pos_bill_id' => $pos_bill_id])
                     ->with('success', __('messages.added'));
}



    /*--------------------------------------------------------------
    |  جلب بيانات منتج بالباركود للأجاكس
    --------------------------------------------------------------*/
    public function fetchProduct($barcode)
    {
        $product = Product::where('barcode', $barcode)->first();

        if ($product) {
            return response()->json([
                'success' => true,
                'product' => [
                    'name'       => $product->name,
                    'unit_price' => $product->unit_price,
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
    $bill = PosBill::with('details')->find($pos_bill_id);

    if (!$bill || $bill->details->count() === 0) {
        return response()->json(['success' => false, 'message' => __('messages.pos.empty_invoice_error')]);
    }

    $discount = $request->input('discount', 0);
    $total = $bill->details->sum('price');
    $net = $total - $discount;

    $bill->update([
        'discount'     => $discount,
        'total_amount' => $total,
        'net_amount'   => max($net, 0),
    ]);

    return response()->json([
        'success' => true,
        'message' => __('messages.pos.finished_entry'),
        'bill_id' => $bill->id
    ]);
}


public function closeCashbox(Request $request)
{
    $employeeId = $request->employee_id;
    // $today = now()->toDateString();

    // احسب مجموع الفواتير غير المغلقة أولاً
    $nets_amount = PosBill::where('employee_id', $employeeId)
                        //   ->whereDate('created_at', $today)
                          ->where('is_closed_with_cashbox', 0)
                          ->sum('net_amount');

    // اجلب آخر مبلغ تم تسليمه (أو صفر إذا لا يوجد)
    $received_amount = CashBoxTransaction::latest()->value('delivered_amount') ?? 0;

    // اجمع المبلغين
    $delivered_amount = $received_amount + $nets_amount;

    // أغلق الفواتير الآن
    PosBill::where('employee_id', $employeeId)
        // ->whereDate('created_at', $today)
        ->where('is_closed_with_cashbox', 0)
        ->update(['is_closed_with_cashbox' => 1]);

    // خزّن السجل في جدول CashBoxTransaction
    CashBoxTransaction::create([
        'employee_id' => $employeeId,
        'received_amount' => $received_amount,
        'delivered_amount' => $delivered_amount,
    ]);

    return redirect()->back()->with('success', __('messages.pos.cashbox_closed_successfully'));
}

public function print($id)
{
    $posBill = PosBill::with('details.product', 'employee')->findOrFail($id);
    return view('pos.print', compact('posBill'));
}

}
