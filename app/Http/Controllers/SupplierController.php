<?php

namespace App\Http\Controllers;

use App\Models\CodesTb;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     $suppliers = Supplier::orderBy('id', 'desc')->paginate(8);
    //     return view('suppliers.index',['suppliers'=>$suppliers]);
    // }

    public function index(Request $request)
    {
        $query = Supplier::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('phone', 'like', '%' . $request->search . '%');
        }

        $suppliers = $query->orderBy('id')->paginate(8)->appends($request->all());

        if ($request->ajax()) {
            return view('suppliers._table', compact('suppliers'))->render();
        }

        return view('suppliers.index', compact('suppliers'));
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
            // 'bank_account' => ['required'],
            // 'bank_name' => ['required'],
            // 'wallet_phone' => ['required'],
            // 'wallet_type' => ['required'],
        ]);

        // $data = request()->all();
        $name = request()->name;
        $phone = request()->phone;
        $email = request()->email;


        Supplier::create([
            'name'=>$name,
            'phone'=>$phone,
            'email'=>$email,

        ]);
        return to_route('supplier.create')->with('success', __('messages.added'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        //
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
                // 'bank_account' => ['required'],
                // 'bank_name' => ['required'],
                // 'wallet_phone' => ['required'],
                // 'wallet_type' => ['required'],
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
