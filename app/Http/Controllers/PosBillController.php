<?php

namespace App\Http\Controllers;

use App\Models\PosBill;
use App\Models\Product;
use App\Models\PosSession;
use Illuminate\Http\Request;
use App\Models\PosBillDetails;
use App\Exports\PosBillsExport;
use App\Models\CashBoxTransaction;
use App\Models\customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

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
            })
            ->orderBy('id')
            ->paginate(8)
            ->appends($request->all());

        if ($posBills->isEmpty()) {
            return redirect()->back()->with('error', 'لا توجد بيانات لتصديرها.');
        }

        return Excel::download(new PosBillsExport($search), 'posBills.xlsx');
    }

    // public function create(Request $request, $pos_bill_id = null)
    // {
    //     $customers  = customer::all();
    //     if (!$pos_bill_id) {
    //         return view('pos.create', [
    //             'pos_bill_id' => null,
    //             'posBillsDetails' => collect(),
    //         ]);
    //     }

    //     $posBillsDetails = PosBillDetails::where('pos_bill_id', $pos_bill_id)->get();
    //     return view('pos.create',compact('customers','pos_bill_id','posBillsDetails'));
    // }

    public function create(Request $request, $pos_bill_id = null)
{
    $customers = customer::all(); // <-- اجلب العملاء دائماً

    if (!$pos_bill_id) {
        return view('pos.create', [
            'pos_bill_id'       => null,
            'posBillsDetails'   => collect(),
            'customers'         => $customers, // <-- تمرير العملاء هنا
        ]);
    }

    $posBillsDetails = PosBillDetails::where('pos_bill_id', $pos_bill_id)->get();

    return view('pos.create', [
        'customers'       => $customers,
        'pos_bill_id'     => $pos_bill_id,
        'posBillsDetails' => $posBillsDetails,
    ]);
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

        $unit_price = $product->unit_price;
        $price      = $unit_price * $quantity;

        PosBillDetails::create([
            'pos_bill_id' => $pos_bill_id,
            'product_id'  => $product->id,
            'unit_price'  => $unit_price,
            'quantity'    => $quantity,
            'price'       => $price,
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
                    'unit_price' => $product->unit_price,
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => __('messages.pos.product_not_found'),
        ]);
    }

    // public function finish(Request $request, $pos_bill_id)
    // {
    //     $validated = $request->validate([
    //         'discount'       => 'nullable|numeric|min:0',
    //         'payment_status' => 'required|in:cash,visa,debt',  // تعديل هنا ليطابق الحقل الجديد
    //     ]);

    //     $discount      = $validated['discount'] ?? 0;
    //     $paymentStatus = $validated['payment_status'];

    //     $posBill = PosBill::findOrFail($pos_bill_id);

    //     $total    = $posBill->details()->sum('price');
    //     $netAmount = max($total - $discount, 0);

    //     $posBill->update([
    //         'total_amount'   => $total,
    //         'discount'       => $discount,
    //         'net_amount'     => $netAmount,
    //         'payment_status' => $paymentStatus,  // تحديث الحقل هنا
    //         'finished_by'    => Auth::id(),
    //         'status'         => 'finished',
    //     ]);

    //     // تسجيل حركة مالية إذا كانت طريقة الدفع نقداً أو فيزا
    //     if (in_array($paymentStatus, ['cash', 'visa'])) {
    //         CashBoxTransaction::create([
    //             'amount'      => $netAmount,
    //             'type'        => 'in',
    //             'description' => __('messages.pos.payment_' . $paymentStatus) . ' - فاتورة #' . $posBill->id,
    //             'pos_bill_id' => $posBill->id,
    //             'session_id'  => $posBill->session_id,
    //         ]);
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'bill_id' => $posBill->id,
    //         'message' => __('messages.pos.finished_entry'),
    //     ]);
    // }

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

        // في حالة الدفع بالدين يجب أن يكون هناك customer_id
        if ($paymentStatus === 'debt' && empty($validated['customer_id'])) {
            return response()->json([
                'success' => false,
                'message' => 'يجب اختيار الزبون عند الدفع بالدين.'
            ], 422);
        }

        $posBill->update([
            'total_amount'   => $total,
            'discount'       => $discount,
            'net_amount'     => $netAmount,
            'payment_status' => $paymentStatus,
            'customer_id'    => $paymentStatus === 'debt' ? $validated['customer_id'] : $posBill->customer_id,
            'finished_by'    => Auth::id(),
            'status'         => 'finished',
        ]);

        // تسجيل حركة مالية فقط عند الكاش أو الفيزا
        if (in_array($paymentStatus, ['cash', 'visa'])) {
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
        $employeeId = $request->employee_id;

        $nets_amount = PosBill::where('employee_id', $employeeId)
                            ->where('is_closed_with_cashbox', 0)
                            ->sum('net_amount');

        $received_amount = CashBoxTransaction::latest()->value('delivered_amount') ?? 0;

        $delivered_amount = $received_amount + $nets_amount;

        PosBill::where('employee_id', $employeeId)
            ->where('is_closed_with_cashbox', 0)
            ->update(['is_closed_with_cashbox' => 1]);

        CashBoxTransaction::create([
            'employee_id'      => $employeeId,
            'received_amount'  => $received_amount,
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
