<?php

namespace App\Http\Controllers;

use App\Models\CodesTb;
use App\Models\Supplier;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use App\Exports\SuppliersExport;
use Maatwebsite\Excel\Facades\Excel;

class SupplierController extends Controller
{

    public function index(Request $request)
    {
        $search = $request->input('search');

        $suppliers = supplier::when($search, function ($query, $search) {
            $query->where('name', 'like', "%{$search}%")
            ->orWhere('phone', 'like', "%{$search}%");
        })->paginate(8); // حدد عدد العناصر في كل صفحة

        // إذا كان الطلب AJAX نعيد جزء الـ Table فقط
        if ($request->ajax()) {
            return view('suppliers._table', compact('suppliers'))->render();
        }

        // أما إذا كان تحميل الصفحة عادي
        return view('suppliers.index', compact('suppliers'));
    }


    public function export(Request $request)
    {
        $search = $request->input('search');

        $suppliers = Supplier::when($search, function ($query, $search) {
            $query->where('name', 'like', "%{$search}%")
            ->orWhere('phone', 'like', "%{$search}%");
        })->get();

        if ($suppliers->isEmpty()) {
            return redirect()->back()->with('error', 'لا توجد بيانات لتصديرها.');
        }

        return Excel::download(new SuppliersExport($search), 'suppliers.xlsx');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $banks = CodesTb::where('main_cd',1)->where('sub_cd', '>', 0)->get();
        $wallets = CodesTb::where('main_cd',6)->where('sub_cd', '>', 0)->get();
        return view('suppliers.create',compact("banks","wallets"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(){
        request()->validate([
            'name' => ['required'],
            'phone' => ['required'],
            'email' => ['required'],
            'bank_account' => ['nullable', 'numeric'],
            'bank_name' => ['nullable', 'integer'],
            'wallet_phone' => ['nullable', 'numeric'],
            'wallet_type' => ['nullable', 'integer'],
        ]);

        // $data = request()->all();
        $name = request()->name;
        $phone = request()->phone;
        $email = request()->email;
        $bank_account = request()->bank_account;
        $bank_name = request()->bank_name;
        $wallet_phone = request()->wallet_phone;
        $wallet_type = request()->wallet_type;

        $supplier = Supplier::create([
            'name'=>$name,
            'phone'=>$phone,
            'email'=>$email,

        ]);

        BankAccount::create([
            'IPAN'=>$bank_account,
            'bank_cd'=>$bank_name,
            'wallet_phone_number'=>$wallet_phone,
            'wallet_cd'=>$wallet_type,
            'accountable_type_cd'=>2,
            'accountable_id'=>$supplier->id,
        ]);
        return to_route('supplier.create')->with('success', __('messages.added'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id){
        $supplier = Supplier::findOrFail($id);
        return view('suppliers.show',compact('supplier'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($supplierId)
    {
        $supplier=Supplier::findOrFail($supplierId);
        $wallets = CodesTb::where('main_cd',6)->where('sub_cd', '>', 0)->get();
        $banks = CodesTb::where('main_cd',1)->where('sub_cd', '>', 0)->get();

        return view('suppliers.edit',compact('supplier','wallets','banks'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($supplierId)
    {
        request()->validate([
                'name' => ['required'],
                'phone' => ['required'],
                'email' => ['required'],
                'bank_account' => ['nullable', 'numeric'],
                'bank_name' => ['nullable', 'integer'],
                'wallet_phone' => ['nullable', 'numeric'],
                'wallet_type' => ['nullable', 'integer'],
            ]);

            // $data = request()->all();
            $name = request()->name;
            $phone = request()->phone;
            $email = request()->email;

            $supplier = Supplier::findOrFail($supplierId);
            $supplier->update([
                'name'=>$name,
                'phone'=>$phone,
                'email'=>$email,
            ]);
            // تحديث أو إنشاء بيانات الحساب البنكي
            $bankAccount = $supplier->bankAccount()->first();

            if (!$bankAccount) {
                $bankAccount = new BankAccount();
                $bankAccount->accountable_id = $supplier->id;
                $bankAccount->accountable_type_cd = 2; // 2 معناها موزع
            }

            $bankAccount->IPAN = request()->bank_account;
            $bankAccount->bank_cd = request()->bank_name;
            $bankAccount->wallet_phone_number = request()->wallet_phone;
            $bankAccount->wallet_cd = request()->wallet_type;

            $bankAccount->save();


            $page = request()->get('page', 1);
            return to_route('supplier.index',['page' => $page])
            ->with('success', __('messages.updated'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($supplierId){
        $supplier = Supplier::find($supplierId);
        if (!$supplier)
        {
            return redirect()->back()->with('error', __('messages.not_found'));
        }
        $supplier->delete();
        $page = request()->get('page', 1);
        return to_route('supplier.index',['page' => $page])
        ->with('success', __('messages.deleted'));
    }
}
