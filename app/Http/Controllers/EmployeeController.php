<?php

namespace App\Http\Controllers;

use Mpdf\Mpdf;
use App\Models\CodesTb;
use App\Models\customer;
use App\Models\Employee;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use App\Exports\EmployeesExport;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('view-employee'), 403);
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

        return Excel::download(new employeesExport($employees), 'employees.xlsx');
    }

    public function exportPDF(Request $request)
    {
        $search = $request->input('search');

         $employeesItems = Employee::when($search, function ($query, $search) {
            $query->where('name', 'like', "%{$search}%")
            ->orWhere('id_card', 'like', "%{$search}%")
            ->orWhere('phone', 'like', "%{$search}%");
        })->get();

        if ($employeesItems->isEmpty()) {
            return redirect()->back()->with('error', 'لا توجد بيانات لتصديرها.');
        }

        // تحميل Blade كـ HTML
        $html = view('employee.employeeItemsPDF', compact('employeesItems'))->render();

        // إعداد mPDF
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => app()->getLocale() == 'ar' ? 'Cairo' : 'dejavusans',
            'directionality' => app()->getLocale() == 'ar' ? 'rtl' : 'ltr',
        ]);

        // كتابة HTML في PDF
        $mpdf->WriteHTML($html);
        $filename = app()->getLocale() == 'ar' ? 'تقرير الموظفين.pdf' : 'Employee_Report.pdf';

        // تحميل PDF مباشرة
        return $mpdf->Output($filename, 'D');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort_if(Gate::denies('create-employee'), 403);
        $banks = CodesTb::where('main_cd',1)->where('sub_cd', '>', 0)->get();
        $wallets = CodesTb::where('main_cd',6)->where('sub_cd', '>', 0)->get();
        return view('employee.create', compact("banks","wallets"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request)
    {
        // إنشاء الموظف
        $employee = Employee::create($request->only([
            'name',
            'phone',
            'email',
            'id_card',
        ]));

        // إنشاء الحساب البنكي / المحفظة
        BankAccount::create([
            'IPAN'               => $request->bank_account,
            'bank_cd'            => $request->bank_name,
            'wallet_phone_number'=> $request->wallet_phone,
            'wallet_cd'          => $request->wallet_type,
            'accountable_type_cd'=> 1, // ممكن تتحول لعلاقة Polymorphic لاحقًا
            'accountable_id'     => $employee->id,
        ]);

        return to_route('employee.create')->with('success', __('messages.added'));
    }


    /**
     * Display the specified resource.
     */
    public function show($id){
        abort_if(Gate::denies('view-employee'), 403);
        $employee = Employee::with('bankAccount')->findOrFail($id);
        return view('employee.show',compact('employee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($employeeId)
    {
        abort_if(Gate::denies('edit-employee'), 403);
        $employee=Employee::findOrFail($employeeId);
        $banks = CodesTb::where('main_cd',1)->where('sub_cd', '>', 0)->get();
        $wallets = CodesTb::where('main_cd',6)->where('sub_cd', '>', 0)->get();
        return view('employee.edit',compact('employee','banks','wallets'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeRequest $request, $employeeId)
    {
        $employee = Employee::findOrFail($employeeId);

        // تحديث بيانات الموظف
        $employee->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'id_card' => $request->id_card,
        ]);

        // تحديث أو إنشاء بيانات الحساب البنكي
        $bankAccount = $employee->bankAccount()->first();

        if (!$bankAccount) {
            $bankAccount = new BankAccount();
            $bankAccount->accountable_id = $employee->id;
            $bankAccount->accountable_type_cd = 1; // 1 معناها موظف
        }

        $bankAccount->IPAN = $request->bank_account;
        $bankAccount->bank_cd = $request->bank_name;
        $bankAccount->wallet_phone_number = $request->wallet_phone;
        $bankAccount->wallet_cd = $request->wallet_type;
        $bankAccount->save();

        $page = $request->get('page', 1);

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
