<?php

namespace App\Http\Controllers;

use App\Models\CodesTb;
use App\Models\customer;

use Illuminate\Http\Request;
use App\Exports\CustomersExport;
use function Laravel\Prompts\select;
use Maatwebsite\Excel\Facades\Excel;

class customerController extends Controller
{
    // public function index(){
    //     $customers = customer::orderBy('id', 'desc')->paginate(8);
    //     return view('customer.index',['customers'=>$customers]);
    // }

        public function index(Request $request)
    {
        $search = $request->input('search');

        $customers = Customer::when($search, function ($query, $search) {
            $query->where('name', 'like', "%{$search}%")
            ->orWhere('id_card', 'like', "%{$search}%")
            ->orWhere('phone', 'like', "%{$search}%");
        })->paginate(8); // حدد عدد العناصر في كل صفحة

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

        return Excel::download(new CustomersExport($search), 'customers.xlsx');
    }

    public function show($id){
        $customer = customer::findOrFail($id);
        return view('customer.show',compact('customer'));
    }


    public function create(){
        $status = CodesTb::where('main_cd',9)->where('sub_cd', '>', 0)->get();
        return view('customer.create',compact('status'));
    }


    public function store(){
        // dd(request()->status);
        request()->validate([
            'name' => ['required'],
            'phone' => ['required'],
            'email' => ['required'],
            'address' => ['required'],
            'id_card' => ['required'],
            'address_details' => ['required'],
            'status_cd' => ['required'],
        ]);

        // $data = request()->all();
        $name = request()->name;
        $phone = request()->phone;
        $email = request()->email;
        $address = request()->address;
        $id_card = request()->id_card;
        $address_details = request()->address_details;
        $status_cd = request()->status_cd;


        customer::create([
            'name'=>$name,
            'phone'=>$phone,
            'email'=>$email,
            'address'=>$address,
            'id_card'=>$id_card,
            'status_cd'=>$status_cd,
            'address_details'=>$address_details,
        ]);
        return to_route('customer.create')->with('success', __('messages.added'));
    }

    public function edit($id){
        $customer=customer::findOrFail($id);
        $status = CodesTb::where('main_cd',9)->where('sub_cd', '>', 0)->get();

        return view('customer.edit',compact('customer','status'));
    }

    public function update($id){
        request()->validate([
            'name' => ['required'],
            'phone' => ['required'],
            'email' => ['required'],
            'address' => ['required'],
            'id_card' => ['required'],
            'address_details' => ['required'],
            'status_cd' => ['required'],
        ]);

        $name = request()->name;
        $phone = request()->phone;
        $email = request()->email;
        $address = request()->address;
        $id_card = request()->id_card;
        $address_details = request()->address_details;
        $status_cd = request()->status_cd;
        $customer = customer::findOrFail($id);


        $customer->update([
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
            'address' => $address,
            'id_card' => $id_card,
            'address_details' => $address_details,
            'status_cd' => $status_cd ,
        ]);
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
