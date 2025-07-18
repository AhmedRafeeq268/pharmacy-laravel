<?php

namespace App\Http\Controllers;

use App\Models\CodesTb;
use App\Models\customer;
use App\Models\Employee;
use Illuminate\Http\Request;

class FarmacyController extends Controller
{
    public function index(){
        $main_codes = CodesTb::where("sub_cd" ,0)->get();
        // dd($main_codes->toArray());
        $main_cd =1;
        $next_code_sub_cd= CodesTb::where("main_cd" ,$main_cd)->max('sub_cd') +1;


        $customers=customer::all()->count();
        $employees=Employee::all()->count();

        return view('farmacy.index',['customers'=>$customers,'employees'=>$employees]);
    }
}
