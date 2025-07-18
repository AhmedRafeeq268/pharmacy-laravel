<?php

namespace App\Http\Controllers;

use App\Models\CodesTb;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\LastBillInsert;
use App\Models\PurchasesBills;
use App\Models\ProductCategory;

class PurchasesBillsController extends Controller
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
        $employees = Employee::get('name');
        $ProductCategories=ProductCategory::get('name');
        $manufacturers = CodesTb::where('main_cd',8)->where('sub_cd','>',0)->get();

        return view('bill.create',compact('currancies','employees','ProductCategories','manufacturers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store()
    {
        request()->validate([
            'total_amount' => ['required'],
            'currancy_type' => ['required'],
            'bill_number' => ['required'],
            'bill_date' => ['required'],
            'employee_receipt' => ['required'],
            'manufacturer' => ['required'],
        ]);
        $total_amount =request() ->total_amount;
        $currancy_type =request() ->currancy_type;
        $bill_number =request() ->bill_number;
        $bill_date =request() ->bill_date;
        $employee_receipt =request() ->employee_receipt;
        $manufacturer =request() ->manufacturer;

        $bill = PurchasesBills::create([
            'total_amount' => $total_amount ,
            'currancy_type' => $currancy_type ,
            'bill_number' => $bill_number ,
            'bill_date' => $bill_date ,
            'employee_receipt' => $employee_receipt ,
            'manufacturer' => $manufacturer ,
        ]);

        $billId = $bill->id;
        return to_route('billDetails.create',["billId"=>$billId ])->with('success', __('messages.added'));
    }

    /**
     * Display the specified resource.
     */
    public function show(PurchasesBills $purchasesBills)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($billId)
    {
        $bill = PurchasesBills::findOrFail($billId);
        $currancies = CodesTb::where('main_cd',7)->where('sub_cd','>',0)->get();
        $employees = Employee::get('name');
        return view('bill.edit',compact('bill','currancies','employees'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update($billId)
    {
        request()->validate([
            'total_amount' => ['required'],
            'currancy_type' => ['required'],
            'bill_number' => ['required'],
            'bill_date' => ['required'],
            'employee_receipt' => ['required'],
        ]);
        $total_amount =request() ->total_amount;
        $currancy_type =request() ->currancy_type;
        $bill_number =request() ->bill_number;
        $bill_date =request() ->bill_date;
        $employee_receipt =request() ->employee_receipt;

        $bill = PurchasesBills::findOrFail($billId);
        $bill->update([
            'total_amount' => $total_amount ,
            'currancy_type' => $currancy_type ,
            'bill_number' => $bill_number ,
            'bill_date' => $bill_date ,
            'employee_receipt' => $employee_receipt ,
            'adopt_bill'=>null,
            'authorized_employee'=>null,
            'certified_or_not'=>null,
        ]);
        $page = request()->get('page', 1);

        return to_route('bill.index',['page' => $page])
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
}
