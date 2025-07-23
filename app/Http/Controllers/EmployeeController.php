<?php

namespace App\Http\Controllers;

use App\Models\CodesTb;
use App\Models\customer;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Exports\EmployeesExport;
use App\Models\BankAccount;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     $employees = Employee::orderBy('id', 'desc')->paginate(8);
    //     return view('employee.index',['employees'=>$employees]);
    // }

    public function index(Request $request)
    {
        $search = $request->input('search');

        $employees = Employee::with(['bankAccount.bank','bankAccount.wallet'])->when($search, function ($query, $search) {
            $query->where('name', 'like', "%{$search}%")
            ->orWhere('id_card', 'like', "%{$search}%")
            ->orWhere('phone', 'like', "%{$search}%");
        })->paginate(8); // حدد عدد العناصر في كل صفحة

        // إذا كان الطلب AJAX نعيد جزء الـ Table فقط
        if ($request->ajax()) {
            return view('employee._table', compact('employees'))->render();
        }

        // أما إذا كان تحميل الصفحة عادي
        return view('employee.index', compact('employees'));
    }

    public function export(Request $request)
    {
        $search = $request->input('search');

        $employees = Employee::when($search, function ($query, $search) {
            $query->where('name', 'like', "%{$search}%")
            ->orWhere('id_card', 'like', "%{$search}%")
            ->orWhere('phone', 'like', "%{$search}%");
        })->get();

        if ($employees->isEmpty()) {
            return redirect()->back()->with('error', 'لا توجد بيانات لتصديرها.');
        }

        return Excel::download(new employeesExport($search), 'employees.xlsx');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $banks = CodesTb::where('main_cd',1)->where('sub_cd', '>', 0)->get();
        $wallets = CodesTb::where('main_cd',6)->where('sub_cd', '>', 0)->get();
        return view('employee.create', compact("banks","wallets"));
    }

    /**
     * Store a newly created resource in storage.
     */
        public function store(){
            request()->validate([
                'name' => ['required'],
                'phone' => ['required'],
                'email' => ['required'],
                'id_card' => ['required'],
                'bank_account' => ['nullable', 'numeric'],
                'bank_name' => ['nullable', 'integer'],
                'wallet_phone' => ['nullable', 'numeric'],
                'wallet_type' => ['nullable', 'integer'],
            ]);

            // $data = request()->all();
            $name = request()->name;
            $phone = request()->phone;
            $email = request()->email;
            $id_card = request()->id_card;
            $bank_account = request()->bank_account;
            $bank_name = request()->bank_name;
            $wallet_phone = request()->wallet_phone;
            $wallet_type = request()->wallet_type;


           $employee = Employee::create([
                'name'=>$name,
                'phone'=>$phone,
                'email'=>$email,
                'id_card'=>$id_card,
            ]);

            BankAccount::create([
                'IPAN'=>$bank_account,
                'bank_cd'=>$bank_name,
                'wallet_phone_number'=>$wallet_phone,
                'wallet_cd'=>$wallet_type,
                'accountable_type_cd'=>1,
                'accountable_id'=>$employee->id,
            ]);
            return to_route('employee.create')->with('success', __('messages.added'));
    }


    /**
     * Display the specified resource.
     */
    public function show($id){
        $employee = Employee::with('bankAccount')->findOrFail($id);
        return view('employee.show',compact('employee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
        public function edit($employeeId){
            $employee=Employee::findOrFail($employeeId);
            $banks = CodesTb::where('main_cd',1)->where('sub_cd', '>', 0)->get();
            $wallets = CodesTb::where('main_cd',6)->where('sub_cd', '>', 0)->get();
            return view('employee.edit',compact('employee','banks','wallets'));
        }


    /**
     * Update the specified resource in storage.
     */
    public function update($employeeId)
    {
        request()->validate([
            'name' => ['required'],
            'phone' => ['required'],
            'email' => ['required'],
            'id_card' => ['required'],
            'bank_account' => ['nullable', 'numeric'],
            'bank_name' => ['nullable', 'integer'],
            'wallet_phone' => ['nullable', 'numeric'],
            'wallet_type' => ['nullable', 'integer'],
        ]);

        $employee = Employee::findOrFail($employeeId);

        $employee->update([
            'name' => request()->name,
            'phone' => request()->phone,
            'email' => request()->email,
            'id_card' => request()->id_card,
        ]);

        // تحديث أو إنشاء بيانات الحساب البنكي
        $bankAccount = $employee->bankAccount()->first();

        if (!$bankAccount) {
            $bankAccount = new BankAccount();
            $bankAccount->accountable_id = $employee->id;
            $bankAccount->accountable_type_cd = 1; // 1 معناها موظف
        }

        $bankAccount->IPAN = request()->bank_account;
        $bankAccount->bank_cd = request()->bank_name;
        $bankAccount->wallet_phone_number = request()->wallet_phone;
        $bankAccount->wallet_cd = request()->wallet_type;

        $bankAccount->save();

        $page = request()->get('page', 1);
        return to_route('employee.index', ['page' => $page])
            ->with('success', __('messages.updated'));
    }


    /**
     * Remove the specified resource from storage.
     */
        public function destroy($employeeId){
        $employee = Employee::find($employeeId);
        if (!$employee)
        {
            return redirect()->back()->with('error', __('messages.not_found'));
        }
        $employee->delete();
        $page = request()->get('page', 1);
        return to_route('employee.index',['page' => $page])
        ->with('success', __('messages.deleted'));
    }

}
