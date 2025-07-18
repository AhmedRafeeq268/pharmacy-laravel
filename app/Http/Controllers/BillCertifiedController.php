<?php

namespace App\Http\Controllers;

use App\Models\BalanceStore;
use Illuminate\Http\Request;
use App\Models\PurchasesBills;
use App\Models\PurchasesBillsDetails;

class BillCertifiedController extends Controller
{
    public function index($billId){
        $billDetailsItems = PurchasesBillsDetails::where('bill_id',$billId)->get();
        $bills=PurchasesBills::where('id',$billId)->get();
        return view('certified.index',compact('billDetailsItems','billId','bills'));
    }
    public function store($billId){

        $bill = PurchasesBills::findOrFail($billId);
        $bill->update([
            'adopt_bill' => 1 ,
            'authorized_employee' => 'ahmed' ,
            'certified_or_not' => 1 ,
        ]);

        $billDetailsItems = PurchasesBillsDetails::where('bill_id',$billId)->get();

        foreach($billDetailsItems as $billDetailsItem){
            BalanceStore::create([
            'product_name' => $billDetailsItem->product_name ,
            'quantity' => $billDetailsItem->quantity ,
            'unity_price' => $billDetailsItem->cost ,

        ]);
        }
        $page = request()->get('page', 1);
        return to_route('bill.index',['page' => $page])->with('success', __('messages.approved'));
    }
    public function reject(Request $request, $billId)
    {
        $bill = PurchasesBills::findOrFail($billId);
        $bill->certified_or_not = 0;
        $bill->authorized_employee = 'ahmed';
        $bill->adopt_bill = 0;
        $bill->save();
        $page = request()->get('page', 1);

        return to_route('bill.index',['page' => $page])->with('error', __('messages.uncertified'));
    }

}
