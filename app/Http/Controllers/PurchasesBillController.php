<?php

namespace App\Http\Controllers;

use App\Models\CodesTb;
use App\Models\Product;
use App\Models\Employee;
use App\Models\Supplier;
// use App\Models\PurchasesBills;
use Illuminate\Http\Request;
use App\Models\LastBillInsert;
use App\Models\PurchasesBills;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\StorePurchaseBillRequest;
use App\Http\Requests\UpdatePurchaseBillRequest;

class PurchasesBillController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        abort_if(Gate::denies('view-purchase-bill'), 403);
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
        abort_if(Gate::denies('create-purchase-bill'), 403);
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
    public function store(StorePurchaseBillRequest $request)
    {
        $maxBillNumber = PurchasesBills::max('bill_number');
        $data = $request->validated();
        // تعيين القيم الافتراضية إن لم يتم إدخالها
        $data['paid'] = $data['paid'] ?? 0;
        $data['remaining'] = $data['remaining'] ?? $data['total_amount'];
        $data['status'] = $data['status'] ?? 'unpaid';
        $data['adopt_bill'] = $data['adopt_bill'] ?? false;
        $data['certified_or_not'] = $data['certified_or_not'] ?? false;
        $data['bill_number'] = $maxBillNumber + 1;

         $bill = PurchasesBills::create($data);
        return redirect()->route('billDetails.create', ['billId' => $bill->id])
            ->with('success', __('messages.added'));
    }

    /**
     * Display the specified resource.
     */
    public function show($billId)
    {
        abort_if(Gate::denies('view-purchase-bill'), 403);
        $bill = PurchasesBills::with('supplier', 'authorizedEmployee')->findOrFail($billId);
        return view('bill.show', compact('bill'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($billId)
    {
        abort_if(Gate::denies('edit-purchase-bill'), 403);
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
    public function update(UpdatePurchaseBillRequest $request, $billId)
    {
        $bill = PurchasesBills::findOrFail($billId);

        $data = $request->validated();

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
