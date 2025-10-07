<?php

namespace App\Http\Controllers;

use Mpdf\Mpdf;
use App\Models\CodesTb;

use App\Models\customer;
use Illuminate\Http\Request;
use App\Exports\CustomersExport;
use function Laravel\Prompts\select;
use Illuminate\Support\Facades\Gate;

use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;

class customerController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('view-customer'), 403);
        $search = $request->input('search');

        $customers = Customer::when($search, function ($query, $search) {
            $query->where('name', 'like', "%{$search}%")
            ->orWhere('id_card', 'like', "%{$search}%")
            ->orWhere('phone', 'like', "%{$search}%");
        })->orderBy('id', 'desc')->paginate(8); // حدد عدد العناصر في كل صفحة

        // إذا كان الطلب AJAX نعيد جزء الـ Table فقط
        if ($request->ajax()) {
            return view('customer._table', compact('customers'))->render();
        }

        // أما إذا كان تحميل الصفحة عادي
        return view('customer.index', compact('customers'));
    }


    public function export(Request $request)
    {
        $search = $request->input('search');

        $customers = Customer::when($search, function ($query, $search) {
            $query->where('name', 'like', "%{$search}%")
            ->orWhere('id_card', 'like', "%{$search}%")
            ->orWhere('phone', 'like', "%{$search}%");
        })->get();

        if ($customers->isEmpty()) {
            return redirect()->back()->with('error', 'لا توجد بيانات لتصديرها.');
        }

        return Excel::download(new CustomersExport($customers), 'customers.xlsx');
    }

    public function exportPDF(Request $request)
    {
        $search = $request->input('search');

        $customersItems = Customer::when($search, function ($query, $search) {
            $query->where('name', 'like', "%{$search}%")
            ->orWhere('id_card', 'like', "%{$search}%")
            ->orWhere('phone', 'like', "%{$search}%");
        })->get();

        if ($customersItems->isEmpty()) {
            return redirect()->back()->with('error', 'لا توجد بيانات لتصديرها.');
        }

        // تحميل Blade كـ HTML
        $html = view('customer.customerItemsPDF', compact('customersItems'))->render();

        // إعداد mPDF
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => app()->getLocale() == 'ar' ? 'Cairo' : 'dejavusans',
            'directionality' => app()->getLocale() == 'ar' ? 'rtl' : 'ltr',
        ]);

        // كتابة HTML في PDF
        $mpdf->WriteHTML($html);
        $filename = app()->getLocale() == 'ar' ? 'تقرير الزبائن.pdf' : 'Customer_Report.pdf';

        // تحميل PDF مباشرة
        return $mpdf->Output($filename, 'D');
    }

    public function show($id){
        abort_if(Gate::denies('view-customer'), 403);
        $customer = customer::findOrFail($id);
        return view('customer.show',compact('customer'));
    }


    public function create(){
        abort_if(Gate::denies('create-customer'), 403);
        $status = CodesTb::where('main_cd',9)->where('sub_cd', '>', 0)->get();
        return view('customer.create',compact('status'));
    }


    public function store(StoreCustomerRequest $request){
        Customer::create($request->validated());
        return to_route('customer.index')->with('success', __('messages.added'));
    }

    public function edit($id){
        abort_if(Gate::denies('edit-customer'), 403);
        $customer=Customer::findOrFail($id);
        $status = CodesTb::where('main_cd',9)->where('sub_cd', '>', 0)->get();

        return view('customer.edit',compact('customer','status'));
    }

    public function update(UpdateCustomerRequest $request, $id){

        $customer = Customer::findOrFail($id);
        $customer->update($request->validated());

        $page = request()->get('page', 1);
        return to_route('customer.index',['page' => $page])
        ->with('success', __('messages.updated'));
    }

    public function destroy($id){
        $customer = customer::find($id);
        if (!$customer)
        {
            return redirect()->back()->with('error', __('messages.not_found'));
        }
        $customer->delete();
        $page = request()->get('page', 1);
        return to_route('customer.index',['page' => $page])
        ->with('success', __('messages.deleted'));
    }
}
