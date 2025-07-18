<?php

namespace App\Http\Controllers;

use App\Models\customer;
use Illuminate\Http\Request;

use function Laravel\Prompts\select;

class customerController extends Controller
{
    // public function index(){
    //     $customers = customer::orderBy('id', 'desc')->paginate(8);
    //     return view('customer.index',['customers'=>$customers]);
    // }

    public function index(Request $request)
    {
        $query = customer::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('id_card', 'like', '%' . $request->search . '%');
        }

        $customers = $query->orderBy('id')->paginate(8)->appends($request->all());

        if ($request->ajax()) {
            return view('customer._table', compact('customers'))->render();
        }

        return view('customer.index', compact('customers'));
    }


    public function create(){
        return view('customer.create');
    }

    public function store(){
        request()->validate([
            'name' => ['required'],
            'phone' => ['required'],
            'email' => ['required'],
            'address' => ['required'],
            'id_card' => ['required'],
            'address_details' => ['required'],
        ]);

        // $data = request()->all();
        $name = request()->name;
        $phone = request()->phone;
        $email = request()->email;
        $address = request()->address;
        $id_card = request()->id_card;
        $address_details = request()->address_details;


        customer::create([
            'name'=>$name,
            'phone'=>$phone,
            'email'=>$email,
            'address'=>$address,
            'id_card'=>$id_card,
            'address_details'=>$address_details,
        ]);
        return to_route('customer.create')->with('success', __('messages.added'));
    }

    public function edit($customerId){
        $singlecustomerFRomDB=customer::findOrFail($customerId);
        return view('customer.edit',['customer'=>$singlecustomerFRomDB]);
    }

    public function update($customerId){
        request()->validate([
            'name' => ['required'],
            'phone' => ['required'],
            'email' => ['required'],
            'address' => ['required'],
            'id_card' => ['required'],
            'address_details' => ['required'],
        ]);

        $name = request()->name;
        $phone = request()->phone;
        $email = request()->email;
        $address = request()->address;
        $id_card = request()->id_card;
        $address_details = request()->address_details;

        $customer = customer::findOrFail($customerId);


        $customer->update([
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
            'address' => $address,
            'id_card' => $id_card,
            'address_details' => $address_details,
        ]);
        $page = request()->get('page', 1);
        return to_route('customer.index',['page' => $page])
        ->with('success', __('messages.updated'));
    }

    public function destroy($customerId){
        $customer = customer::find($customerId);
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
