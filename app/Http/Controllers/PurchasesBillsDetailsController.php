<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Employee;
use App\Models\BalanceStore;
use Illuminate\Http\Request;
use App\Models\LastBillInsert;
use App\Models\PurchasesBills;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\PurchasesBillsDetails;
use App\Http\Requests\StorePurchaseBillDetailsRequest;
use App\Http\Requests\UpdatePurchaseBillDetailsRequest;

class PurchasesBillsDetailsController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('view-purchase-bill-details'), 403);
        return view('bill.index');
    }

    public function create(Request $request)
    {
        abort_if(Gate::denies('create-purchase-bill-details'), 403);
        $ProductCategories = ProductCategory::get('name');
        $billId = $request->route('billId');

        $billDetails = PurchasesBillsDetails::where('bill_id', $billId)->get();
        $bills = PurchasesBills::where('id', $billId)->get();
        $products = Product::get(['id', 'name']);

        return view('billDetails.create', compact('ProductCategories', 'billDetails', 'billId', 'bills', 'products'));
    }

    public function store(StorePurchaseBillDetailsRequest $request)
    {
        $validated = $request->validated();
        $billId  = $validated['billId'];

        DB::beginTransaction();
        try {
            // نفترض أن جميع الحقول مصفوفات بنفس الطول
            foreach ($validated['product_id'] as $index => $productId) {
                $product = Product::findOrFail($productId);
                $quantity = $validated['quantity'][$index];
                $cost = $validated['cost'][$index];
                $discount = $validated['discount'][$index] ?? 0;
                $prodDate = $validated['production_date'][$index];
                $expDate = $validated['exp_date'][$index];
                $manufacture = $validated['manufacture'][$index];
                // $productCategory = $validated['product_category'][$index];

                $total = ($quantity * $cost) - $discount;

                // إنشاء تفاصيل الفاتورة لكل منتج
                PurchasesBillsDetails::create([
                    'bill_id'          => $billId,
                    'product_id'       => $product->id,
                    'product_name'     => $product->name,
                    'product_category' => $product->category_id,
                    'product_data'     => $prodDate,
                    'exp_date'         => $expDate,
                    'manufacture'      => $manufacture,
                    'quantity'         => $quantity,
                    'cost'             => $cost,
                    'total'            => $total,
                    'discount'         => $discount,
                    'employee_id'      => Auth::id(),
                ]);

                // تحديث كمية المنتج الإجمالية
                $product->increment('quantity', $quantity);

                // تحديث رصيد المخزن
                $balance = BalanceStore::where('product_id', $product->id)
                    ->where('production_date', $prodDate)
                    ->where('exp_date', $expDate)
                    ->where('manufacture', $manufacture)
                    ->where('unity_price', $cost)
                    ->first();

                if ($balance) {
                    $balance->increment('quantity', $quantity);
                    $balance->increment('remaining_quantity', $quantity);
                } else {
                    BalanceStore::create([
                        'product_id'         => $product->id,
                        'product_name'       => $product->name,
                        'production_date'    => $prodDate,
                        'exp_date'           => $expDate,
                        'manufacture'        => $manufacture,
                        'unity_price'        => $cost,
                        'quantity'           => $quantity,
                        'remaining_quantity' => $quantity,
                    ]);
                }
            }

            // تحديث إجمالي الفاتورة بعد إضافة جميع المنتجات
            $totalAmount = PurchasesBillsDetails::where('bill_id', $billId)->sum('total');
            $bill = PurchasesBills::findOrFail($billId);
            $bill->total_amount = $totalAmount;
            $bill->remaining = max(0, $totalAmount - $bill->paid);
            $bill->status = $bill->remaining == 0 ? 'paid' : ($bill->paid > 0 ? 'partial' : 'unpaid');
            $bill->save();

            DB::commit();
            return redirect()->route('billDetails.create', ['billId' => $billId])
                ->with('success', __('messages.added'));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }


    public function edit($billDetailsId)
    {
        abort_if(Gate::denies('edit-purchase-bill-details'), 403);
        $ProductCategories = ProductCategory::get('name');
        $billId = PurchasesBillsDetails::where('id', $billDetailsId)->value('bill_id');
        $billDetails = PurchasesBillsDetails::findOrFail($billDetailsId);
        $products = Product::get(['id', 'name']);
        return view('billDetails.edit', compact('billDetails', 'ProductCategories', 'billId', 'products'));
    }

    public function update(UpdatePurchaseBillDetailsRequest $request, $billDetailsId)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $billDetails = PurchasesBillsDetails::findOrFail($billDetailsId);
            $oldQuantity = $billDetails->quantity;
            $billId = $billDetails->bill_id;

            $product = Product::findOrFail($validated['product_id']);

            $newQuantity = $product->quantity - $oldQuantity + $validated['quantity'];
            if ($newQuantity < 0) {
                throw new \Exception('الكمية في المخزون لا تكفي للتعديل.');
            }
            $product->update(['quantity' => $newQuantity]);

            $total = ($validated['quantity'] * $validated['cost']) - ($validated['discount'] ?? 0);
            $billDetails->update([
                'product_id'       => $product->id,
                'product_name'     => $product->name,
                // 'product_category' => $validated['product_category'],
                'product_data'     => $validated['prod_date'],
                'exp_date'         => $validated['exp_date'],     // ✅ إضافة
                'manufacture'      => $validated['manufacture'],  // ✅ إضافة
                'quantity'         => $validated['quantity'],
                'cost'             => $validated['cost'],
                'total'            => $total,
                'discount'         => $validated['discount'] ?? 0,
                'employee_id'      => Auth::id(),
            ]);

            $totalAmount = PurchasesBillsDetails::where('bill_id', $billId)->sum('total');
            $bill = PurchasesBills::findOrFail($billId);
            $bill->total_amount = $totalAmount;
            $bill->remaining = max(0, $totalAmount - $bill->paid);
            $bill->status = $bill->remaining == 0 ? 'paid' : ($bill->paid > 0 ? 'partial' : 'unpaid');
            $bill->save();

            $oldBalance = BalanceStore::where('product_id', $billDetails->product_id)
                ->where('prod_date', $billDetails->product_data)
                ->where('exp_date', $billDetails->exp_date)
                ->where('manufacture', $billDetails->manufacture)
                ->where('unity_price', $billDetails->cost)
                ->first();

            if ($oldBalance) {
                $oldBalance->decrement('quantity', $oldQuantity);
                $oldBalance->decrement('remaining_quantity', $oldQuantity);
            }

            $newBalance = BalanceStore::where('product_id', $product->id)
                ->where('prod_date', $validated['prod_date'])
                ->where('exp_date', $validated['exp_date'])
                ->where('manufacture', $validated['manufacture'])
                ->where('unity_price', $validated['cost'])
                ->first();

            if ($newBalance) {
                $newBalance->increment('quantity', $validated['quantity']);
                $newBalance->increment('remaining_quantity', $validated['quantity']);
            } else {
                BalanceStore::create([
                    'product_id'         => $product->id,
                    'product_name'       => $product->name,
                    'prod_date'          => $validated['prod_date'],
                    'exp_date'           => $validated['exp_date'],
                    'manufacture'        => $validated['manufacture'],
                    'unity_price'        => $validated['cost'],
                    'quantity'           => $validated['quantity'],
                    'remaining_quantity' => $validated['quantity'],
                ]);
            }

            DB::commit();
            return redirect()->route('billDetails.create', ['billId' => $billId])
                ->with('success', __('messages.updated'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    public function destroy($billDetailsId)
    {
        $billDetails = PurchasesBillsDetails::find($billDetailsId);
        if (!$billDetails) {
            return redirect()->back()->with('error', __('messages.not_found'));
        }

        $billId = $billDetails->bill_id;
        $product = Product::find($billDetails->product_id);

        DB::beginTransaction();
        try {
            if ($product) {
                $product->decrement('quantity', $billDetails->quantity);
            }

            $balance = BalanceStore::where('product_id', $billDetails->product_id)
                ->where('prod_date', $billDetails->product_data)
                ->where('exp_date', $billDetails->exp_date)
                ->where('manufacture', $billDetails->manufacture)
                ->where('unity_price', $billDetails->cost)
                ->first();

            if ($balance) {
                $balance->decrement('quantity', $billDetails->quantity);
                $balance->decrement('remaining_quantity', $billDetails->quantity);
            }

            $billDetails->delete();

            $totalAmount = PurchasesBillsDetails::where('bill_id', $billId)->sum('total');
            $bill = PurchasesBills::find($billId);
            if ($bill) {
                $bill->total_amount = $totalAmount;
                $bill->paid = min($bill->paid, $totalAmount);
                $bill->remaining = max(0, $totalAmount - $bill->paid);
                $bill->status = $bill->remaining == 0 ? 'paid' : ($bill->paid > 0 ? 'partial' : 'unpaid');
                $bill->save();
            }

            DB::commit();
            return to_route('billDetails.create', ['billId' => $billId])
                ->with('success', __('messages.deleted'));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    public function closeBill($billId)
    {
        $bill = PurchasesBills::findOrFail($billId);
        $bill->adopt_bill = 1;
        $bill->save();

        // ✅ رجع لعرض الفاتورة بدلاً من إنشاء جديدة
        return redirect()->route('bill.create', ['billId' => $billId])
            ->with('success', __('messages.added'));
    }
}
