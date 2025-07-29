<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Employee;
// use App\Models\PurchaseBill;
use Illuminate\Http\Request;
use App\Models\LastBillInsert;
use App\Models\PurchasesBills;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\DB;
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
        $validated = $request->validate([
            "product_id" => 'required|exists:products,id',
            "product_data" => 'required|string|max:255',
            "quantity" => 'required|integer|min:1',
            "cost" => 'required|numeric|min:0',
            "total" => 'required|numeric|min:0',
            "discount" => 'nullable|numeric|min:0',
            "product_category" => 'required|string|max:255',
            "billId" => 'required|exists:purchases_bills,id',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $billId = $validated['billId'];

        DB::beginTransaction();
        try {
            // إنشاء تفاصيل الفاتورة
            PurchasesBillsDetails::create([
                'bill_id' => $billId,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_category' => $validated['product_category'],
                'product_data' => $validated['product_data'],
                'quantity' => $validated['quantity'],
                'cost' => $validated['cost'],
                'total' => $validated['total'],
                'discount' => $validated['discount'] ?? 0,
                'employee_id' => 3,  // تأكد من المستخدم المسجل
            ]);

            // تحديث كمية المنتج في المخزون
            $product->quantity = ($product->quantity ?? 0) + $validated['quantity'];
            $product->save();

            // تحديث إجمالي الفاتورة
            $totalAmount = PurchasesBillsDetails::where('bill_id', $billId)->sum('total');
            $bill = PurchasesBills::findOrFail($billId);

            $bill->total_amount = $totalAmount;

            // التحقق من المدفوع والمستحق
            if ($bill->paid > $totalAmount) {
                $bill->paid = $totalAmount;
            }
            $bill->remaining = $totalAmount - $bill->paid;

            // تحديث الحالة
            if ($bill->remaining <= 0) {
                $bill->status = 'paid';
                $bill->remaining = 0;
            } elseif ($bill->paid > 0) {
                $bill->status = 'partial';
            } else {
                $bill->status = 'unpaid';
            }

            $bill->save();

            DB::commit();
            return redirect()->route('billDetails.create', ['billId' => $billId])
                            ->with('success', __('messages.added'));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
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
        public function update(Request $request, $billDetailsId)
    {
        $validated = $request->validate([
            "product_id" => 'required|exists:products,id',
            "product_data" => 'required|string|max:255',
            "quantity" => 'required|integer|min:1',
            "cost" => 'required|numeric|min:0',
            "total" => 'required|numeric|min:0',
            "discount" => 'nullable|numeric|min:0',
            "product_category" => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $billDetails = PurchasesBillsDetails::findOrFail($billDetailsId);
            $oldQuantity = $billDetails->quantity;
            $billId = $billDetails->bill_id;

            $product = Product::findOrFail($validated['product_id']);

            // تعديل كمية المنتج في المخزون: طرح القديمة ثم إضافة الجديدة
            $product->quantity = ($product->quantity - $oldQuantity) + $validated['quantity'];
            if ($product->quantity < 0) {
                // لا يمكن أن تكون الكمية بالسالب
                throw new \Exception('الكمية في المخزون لا تكفي للتعديل.');
            }
            $product->save();

            // تحديث التفاصيل
            $billDetails->update([
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_category' => $validated['product_category'],
                'product_data' => $validated['product_data'],
                'quantity' => $validated['quantity'],
                'cost' => $validated['cost'],
                'total' => $validated['total'],
                'discount' => $validated['discount'] ?? 0,
            ]);

            // تحديث إجمالي الفاتورة بعد التعديل
            $totalAmount = PurchasesBillsDetails::where('bill_id', $billId)->sum('total');
            $bill = PurchasesBills::findOrFail($billId);

            $bill->total_amount = $totalAmount;

            // تأكيد أن المدفوع لا يتجاوز المجموع
            if ($bill->paid > $totalAmount) {
                $bill->paid = $totalAmount;
            }
            $bill->remaining = $totalAmount - $bill->paid;

            // تحديث الحالة
            if ($bill->remaining <= 0) {
                $bill->status = 'paid';
                $bill->remaining = 0;
            } elseif ($bill->paid > 0) {
                $bill->status = 'partial';
            } else {
                $bill->status = 'unpaid';
            }

            $bill->save();

            DB::commit();
            return redirect()->route('billDetails.create', ['billId' => $billId])
                            ->with('success', __('messages.updated'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
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

     $totalAmount = PurchasesBillsDetails::where('bill_id', $billId)
                    ->sum('total');

    $bill = PurchasesBills::find($billId);
    if ($bill) {
        $bill->total_amount = $totalAmount;
        if ($bill->paid > $totalAmount) {
            $bill->paid = $totalAmount;
        }
        $bill->remaining = $totalAmount - $bill->paid;
        if ($bill->remaining <= 0) {
            $bill->status = 'paid';
            $bill->remaining = 0;
        } elseif ($bill->paid > 0) {
            $bill->status = 'partial';
        } else {
            $bill->status = 'unpaid';
        }
        $bill->save();
    }

    return to_route('billDetails.create', ['billId' => $billId])
    ->with('success', __('messages.deleted'));
}
public function closeBill($billId)
{
    $bill = PurchasesBills::findOrFail($billId);
    $bill->adopt_bill = 1;
    $bill->save();

    return redirect()->route('bill.create')->with('success', __('messages.added'));;
}



}
