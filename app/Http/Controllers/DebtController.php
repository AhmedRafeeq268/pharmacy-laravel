<?php

namespace App\Http\Controllers;

use Mpdf\Mpdf;
use App\Models\Debt;
use App\Models\customer;
use App\Models\PosSession;
use App\Models\DebtPayment;
use App\Exports\DebtsExport;
use Illuminate\Http\Request;
use App\Models\CashBoxTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Sum;

class DebtController extends Controller
{

    public function index(Request $request)
    {
        $search = $request->input('search');

        $debtsQuery = Debt::select('customer_id', DB::raw('SUM(remaining_amount) as total_remaining'),DB::raw('SUM(total_amount) as total_debt'))
            ->with('customer') // لتحميل بيانات الزبون
            ->where('status', 'open')
            ->groupBy('customer_id');

        $total_debts = Debt::where('status', 'open')->sum('remaining_amount');

        // بحث حسب اسم الزبون
        if ($search) {
            $debtsQuery->whereHas('customer', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $debts = $debtsQuery->paginate(8); // عدد الزبائن في الصفحة

        // في حالة AJAX
        if ($request->ajax()) {
            return view('debts._table', compact('debts','total_debts'))->render();
        }

        // تحميل عادي
        return view('debts.index', compact('debts','total_debts'));
    }

    public function export(Request $request)
    {
        $search = $request->input('search');

        $debtsQuery = Debt::select(
                'customer_id',
                DB::raw('SUM(total_amount) as total_debt'),
                DB::raw('SUM(remaining_amount) as total_remaining')
            )
            ->where('status', 'open')
            ->groupBy('customer_id')
            ->when($search, function ($query) use ($search) {
                $query->whereHas('customer', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->with('customer')
            ->get();

        if ($debtsQuery->isEmpty()) {
            return redirect()->back()->with('error', 'لا توجد بيانات لتصديرها.');
        }

        // تمرير البيانات مباشرة إلى DebtsExport
        return Excel::download(new DebtsExport($debtsQuery), 'debts.xlsx');
    }

     public function exportPDF(Request $request)
    {
        $search = $request->input('search');

        $debts = Debt::select(
        'customer_id',
        DB::raw('SUM(remaining_amount) as total_remaining'),
        DB::raw('SUM(total_amount) as total_debt'),
        DB::raw('MAX(created_at) as created_at')
    )
    ->with('customer')
    ->where('status', 'open')
    ->when($search, function ($query) use ($search) {
        $query->whereHas('customer', function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%")
              ->orWhere('address', 'like', "%{$search}%");
        });
    })
    ->groupBy('customer_id');


        $total_debts = Debt::where('status', 'open')->sum('remaining_amount');
        $debtsItems = $debts->get();

        if ($debtsItems->isEmpty()) {
            return redirect()->back()->with('error', 'لا توجد بيانات لتصديرها.');
        }

        // تحميل Blade كـ HTML
        $html = view('debts.debtsItemsPDF', compact('debtsItems','total_debts'))->render();

        // إعداد mPDF
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => app()->getLocale() == 'ar' ? 'Cairo' : 'dejavusans',
            'directionality' => app()->getLocale() == 'ar' ? 'rtl' : 'ltr',
        ]);

        // كتابة HTML في PDF
        $mpdf->WriteHTML($html);
        $filename = app()->getLocale() == 'ar' ? 'تقرير الديون.pdf' : 'Debts_Report.pdf';

        // تحميل PDF مباشرة
        return $mpdf->Output($filename, 'D');
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
    // public function show($customer_id)
    // {
    //      $customer = Customer::findOrFail($customer_id);

    //     // جلب كل الديون المفتوحة الخاصة بالزبون
    //     $debts = Debt::with(['posBill', 'payments'])
    //         ->where('customer_id', $customer_id)
    //         ->where('status', 'open')
    //         ->get();

    //     return view('debts.customer_details', compact('customer', 'debts'));
    // }

    public function show( $customer_id)
    {
        $customer = Customer::findOrFail($customer_id);

        // جلب الديون المفتوحة مع الفواتير والمنتجات
        $debts = Debt::with([
            'posBill.details.product', // لجلب المنتجات في كل فاتورة
            'payments'
        ])
        ->where('customer_id', $customer_id)
        ->where('status', 'open')
        ->get();

         $total_remaining = $debts->sum(function ($debt) {
            return $debt->remaining_amount;
        });

        return view('debts.customer_details', compact('customer', 'debts','total_remaining','total_remaining'));
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
    public function destroy($debt){
            $debt = Debt::find($debt);
            if (!$debt)
            {
                return redirect()->back()->with('error', __('messages.not_found'));
            }
            $debt->delete();
            $page = request()->get('page', 1);
            return to_route('debts.index',['page' => $page])
            ->with('success', __('messages.deleted'));
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
