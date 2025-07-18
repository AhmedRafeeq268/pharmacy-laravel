<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\LastBillInsert;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\PurchasesBills;
use App\Models\ProductCategory;
use App\Models\PurchasesBillsDetails;

class PurchasesBillsDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $bills=PurchasesBills::where('id',$billId)->get();

        return view('bill.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // $employees=Employee::get('name','id');
        $ProductCategories=ProductCategory::get('name');
        // $billId = LastBillInsert::max('id_last_bill');
        // if($billId == 0){
        //     $billId++;
        // }


         $billId = $request->route('billId');

        $billDetails = PurchasesBillsDetails::where('bill_id', $billId)->get();
        $bills=PurchasesBills::where('id',$billId)->get();
        $products = Product::get(['id', 'name']);

        return view('billDetails.create',compact('ProductCategories','billDetails','billId','bills','products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($_GET['bill_id']);
        $request->validate([
            "product_id"=>['required'],
            "product_data"=>['required'],
            "quantity"=>['required'],
            "cost"=>['required'],
            "total"=>['required'],
            "discount"=>['required'],
            "product_category"=>['required'],
        ]);

        $product_id = request()->product_id;
        $product_name = Product::find($product_id)->name ?? null;
        $product_data = request()->product_data;
        $quantity = request()->quantity;
        $cost = request()->cost;
        $total = request()->total;
        $discount = request()->discount;
        $product_category = request()->product_category;
        $billId = request()->billId;

        PurchasesBillsDetails::create([
            'bill_id' => $billId ,
            'product_id' => $product_id ,
            'product_name' => $product_name ,
            'product_category' => $product_category ,
            'product_data' => $product_data ,
            'quantity' => $quantity ,
            'cost' => $cost ,
            'total' => $total ,
            'discount' => $discount ,
        ]);
        //  dd($request->all());
        return to_route('billDetails.create',['billId'=>$billId])->with('success', __('messages.added'));
    }



    /**
     * Display the specified resource.
     */
    public function show(PurchasesBillsDetails $purchasesBillsDetails)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($billDetailsId)
    {
       // dd($_SERVER["HTTP_REFERER"]);
        $ProductCategories=ProductCategory::get('name');
        // $billId = PurchasesBillsDetails::where('id', $billDetailsId)->get('bill_id');
        $billId = PurchasesBillsDetails::where('id', $billDetailsId)->value('bill_id');
        $billDetails = PurchasesBillsDetails::findOrFail($billDetailsId);
        $products = Product::get(['id', 'name']);
        return view('billDetails.edit',compact('billDetails','ProductCategories','billId','products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($billDetailsId)
    {
        request()->validate([
            "product_id"=>['required'],
            "product_data"=>['required'],
            "quantity"=>['required'],
            "cost"=>['required'],
            "total"=>['required'],
            "discount"=>['required'],
            "product_category"=>['required'],
        ]);

        $product_id = request()->product_id;
        $product_name = Product::find($product_id)->name ?? null;
        $product_data = request()->product_data;
        $quantity = request()->quantity;
        $cost = request()->cost;
        $total = request()->total;
        $discount = request()->discount;
        $product_category = request()->product_category;
        $billId = request()->billId;

        $billDetails = PurchasesBillsDetails::findOrFail($billDetailsId);
        $billDetails->update([
            'bill_id' => $billId ,
            'product_id' => $product_id ,
            'product_name' => $product_name ,
            'product_category' => $product_category ,
            'product_data' => $product_data ,
            'quantity' => $quantity ,
            'cost' => $cost ,
            'total' => $total ,
            'discount' => $discount ,
        ]);
        $billId = $billDetails->bill_id;
        return to_route('billDetails.create',['billId'=>$billId])
        ->with('success', __('messages.updated'));

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($billDetailsId)
{
    $billDetails = PurchasesBillsDetails::find($billDetailsId);

    $billId = $billDetails->bill_id;
    if (!$billDetails)
        {
            return redirect()->back()->with('error', __('messages.not_found'));
        }
    $billDetails->delete();

    return to_route('billDetails.create', ['billId' => $billId])
    ->with('success', __('messages.deleted'));
}

}
