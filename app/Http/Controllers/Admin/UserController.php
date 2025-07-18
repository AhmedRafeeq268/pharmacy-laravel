<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function create()
    {
        $content="";
        return view('admin.users.create',compact('content'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email'=> 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email'=> $request->email,
            'password' => Hash::make($request->password),
        ]);

        return to_route('admin.users.create')->with('success', 'تم إضافة المستخدم بنجاح');
    }
}
