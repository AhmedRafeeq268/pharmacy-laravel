<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // محاولة تسجيل الدخول
        if (Auth::attempt($credentials)) {
            $user = Auth::user(); // جلب بيانات المستخدم بعد التحقق

            // فحص حالة المستخدم
            if ($user->status == 0) {
                Auth::logout(); // تسجيل الخروج مباشرة
                return back()->withErrors([
                    'email' => 'حسابك غير مفعل.', // رسالة الخطأ للمستخدم
                ]);
            }

            // إذا كان مفعل، تسجيل الدخول عادي
            $request->session()->regenerate();
            return redirect()->intended('farmacy');
        }

        // في حال كانت البيانات خاطئة
        return back()->withErrors([
            'email' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة.',
        ]);
    }


    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'min:6', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email'=> $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        return redirect()->route('login'); // Change as needed
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function changePasswordForm()
    {
        return view('auth.reset-password');
    }

    public function updatePassword(Request $request)
    {
    $request->validate([
        'current_password' => ['required', 'current_password'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
    ]);

    $request->user()->update([
        'password' => Hash::make($request->password),
    ]);

    return to_route('farmacy.index')->with('status', 'password-updated');
    }
}
