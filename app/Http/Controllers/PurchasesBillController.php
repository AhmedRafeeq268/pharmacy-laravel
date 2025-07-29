<?php

namespace App\Http\Controllers;

use App\Models\CodesTb;
use App\Models\Employee;
// use App\Models\PurchasesBills;
use Illuminate\Http\Request;
use App\Models\LastBillInsert;
use App\Models\PurchasesBills;
use App\Models\ProductCategory;
use App\Models\Supplier;

class PurchasesBillController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $bills = PurchasesBills::query();

        if ($request->filled('search')) {
            $bills->where('bill_number', 'like', '%' . $request->search . '%');
        }

        $bills = $bills->orderBy('id', 'desc')->paginate(8)->appends($request->all());

        if ($request->ajax()) {
            return view('bill._table', compact('bills'))->render();
        }
        return view('bill.index', compact('bills'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $currancies = CodesTb::where('main_cd',7)->where('sub_cd', '>', 0)->get();
        $employees = Employee::select('id', 'name')->get();
        $productCategories = ProductCategory::select('id', 'name')->get();
        $manufacturers = CodesTb::where('main_cd',8)->where('sub_cd','>',0)->get();
        $suppliers = Supplier::select('id','name')->get();

        return view('bill.create',compact('currancies','employees','productCategories','manufacturers','suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'total_amount' => 'required|numeric|min:0',
            'currancy_type' => 'required|string|max:10',
            'bill_number' => 'required|integer|unique:purchases_bills,bill_number',
            'bill_date' => 'required|date',
            'employee_receipt' => 'required|string|max:191',
            'manufacturer' => 'required|string|max:191',
            'paid' => 'nullable|numeric|min:0',
            'remaining' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:paid,partial,unpaid',

        ]);
        // تعيين القيم الافتراضية إن لم يتم إدخالها
        $data['paid'] = $data['paid'] ?? 0;
        $data['remaining'] = $data['remaining'] ?? $data['total_amount'];
        $data['status'] = $data['status'] ?? 'unpaid';
        $data['adopt_bill'] = $data['adopt_bill'] ?? false;
        $data['certified_or_not'] = $data['certified_or_not'] ?? false;

        // $total_amount =request() ->total_amount;
        // $currancy_type =request() ->currancy_type;
        // $bill_number =request() ->bill_number;
        // $bill_date =request() ->bill_date;
        // $employee_receipt =request() ->employee_receipt;
        // $manufacturer =request() ->manufacturer;

        // $bill = PurchasesBills::create([
        //     'total_amount' => $total_amount ,
        //     'currancy_type' => $currancy_type ,
        //     'bill_number' => $bill_number ,
        //     'bill_date' => $bill_date ,
        //     'employee_receipt' => $employee_receipt ,
        //     'manufacturer' => $manufacturer ,
        // ]);

        // $billId = $bill->id;
        // return to_route('billDetails.create',["billId"=>$billId ])->with('success', __('messages.added'));

         $bill = PurchasesBills::create($data);

        return redirect()->route('billDetails.create', ['billId' => $bill->id])
            ->with('success', __('messages.added'));
    }

    /**
     * Display the specified resource.
     */
    public function show($billId)
    {
        $bill = PurchasesBills::with('supplier', 'authorizedEmployee')->findOrFail($billId);
        return view('bill.show', compact('bill'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($billId)
    {
        $bill = PurchasesBills::findOrFail($billId);
        $currencies = CodesTb::where('main_cd', 7)->where('sub_cd', '>', 0)->get();
        $employees = Employee::select('id', 'name')->get();
        $productCategories = ProductCategory::select('id', 'name')->get();
        $manufacturers = CodesTb::where('main_cd', 8)->where('sub_cd', '>', 0)->get();
        $suppliers = Supplier::select('id','name')->get();


        return view('bill.edit', compact('bill', 'currencies', 'employees', 'productCategories', 'manufacturers','suppliers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $billId)
    {
        $bill = PurchasesBills::findOrFail($billId);

        $data = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'total_amount' => 'required|numeric|min:0',
            'currancy_type' => 'required|string|max:10',
            'bill_number' => 'required|integer|unique:purchases_bills,bill_number,' . $bill->id,
            'bill_date' => 'required|date',
            'employee_receipt' => 'required|string|max:191',
            'manufacturer' => 'required|string|max:191',
            'paid' => 'nullable|numeric|min:0',
            'remaining' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:paid,partial,unpaid',

        ]);

        $data['paid'] = $data['paid'] ?? $bill->paid;
        $data['remaining'] = $data['remaining'] ?? $bill->remaining;
        $data['status'] = $data['status'] ?? $bill->status;
        $data['adopt_bill'] = $data['adopt_bill'] ?? $bill->adopt_bill;
        $data['certified'] = $data['certified'] ?? $bill->certified;

        $bill->update($data);

        $page = $request->get('page', 1);

        return redirect()->route('bill.index', ['page' => $page])
            ->with('success', __('messages.updated'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($billId)
    {
        $bill = PurchasesBills::find($billId);
        if (!$bill)
        {
            return redirect()->back()->with('error', __('messages.not_found'));
        }
        $bill->delete();
        $page = request()->get('page', 1);
        return to_route('bill.index',['page' => $page])
        ->with('success', __('messages.deleted'));
    }

    public function print($billId)
    {
        $bill = PurchasesBills::with('details.product')->findOrFail($billId);
        return view('bill.print', compact('bill'));
    }

}
