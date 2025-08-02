<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use App\Models\customer;
use App\Models\PosSession;
use App\Models\DebtPayment;
use Illuminate\Http\Request;
use App\Models\CashBoxTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DebtController extends Controller
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
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Debt $debt)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Debt $debt)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Debt $debt)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Debt $debt)
    {
        //
    }

    public function searchForm()
    {
        $customers = customer::select('id','name')->get();
        return view('debts.search',compact('customers'));
    }

   public function ajaxDebts($customerId)
    {
        $debts = Debt::where('customer_id', $customerId)
                    ->where('is_paid', false)
                    ->with(['posBill.details.product:id,name']) // جلب المنتجات مع الفاتورة
                    ->get();

        $result = $debts->map(function ($debt) {
            return [
                'id' => $debt->id,
                'total_amount' => $debt->total_amount,
                'remaining_amount' => $debt->remaining_amount,
                'products' => $debt->posBill->details->map(function ($d) {
                    return [
                        'name' => $d->product->name,
                        'quantity' => $d->quantity,
                        'price' => $d->price,
                    ];
                }),
            ];
        });

        return response()->json([
            'success' => true,
            'debts' => $result,
        ]);
    }


    public function payDebt(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'amount'      => 'required|numeric',
        ]);

        $amountToPay = $validated['amount']; // المبلغ الذي سيدفعه الزبون مرة واحدة فقط
        $originalAmount = $amountToPay;
        $customerId  = $validated['customer_id'];

        $sessionId = PosSession::where('user_id', Auth::id())
            ->where('status', 'open')
            ->latest()
            ->value('id');

        if (!$sessionId) {
            return response()->json([
                'success' => false,
                'message' => 'لا توجد جلسة مفتوحة حالياً.'
            ], 400);
        }

        $debts = Debt::where('customer_id', $customerId)
            ->where('is_paid', false)
            ->orderBy('created_at', 'asc')
            ->get();

        if ($debts->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'لا يوجد ديون مفتوحة لهذا الزبون.'
            ], 400);
        }

        DB::beginTransaction();
        try {
            foreach ($debts as $debt) {
                if ($amountToPay <= 0) break;

                $remaining = $debt->remaining_amount;
                $paidNow = min($amountToPay, $remaining);
                $remaining = $debt->remaining_amount - $paidNow;

                if ($remaining <= 0) {
                    $remaining = 0;
                    $debt->is_paid = true;
                }

                $debt->save();

                DebtPayment::create([
                    'debt_id'        => $debt->id,
                    'amount_paid'    => $paidNow,
                    'paid_by'        => Auth::id(),
                    'payment_method' => 'cash',
                    'payment_date'   => now(),
                    'notes'          => 'دفع عبر واجهة البحث',
                ]);

                CashBoxTransaction::create([
                    'amount'      => $paidNow,
                    'type'        => 'in',
                    'description' => 'دفع دين للزبون #' . $customerId . ' - دين #' . $debt->id,
                    'session_id'  => $sessionId,
                ]);

                $amountToPay -= $paidNow;
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'تم تسجيل الدفع بنجاح بمبلغ ' . number_format($originalAmount, 2)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'فشل في تسجيل الدفع: ' . $e->getMessage()
            ], 500);
        }
    }






}
