<?php
namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login'); // أنشئ ملف resources/views/auth/login.blade.php
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            return redirect()->intended('/farmacy');
        }

        return back()->withErrors(['email' => 'بيانات الدخول غير صحيحة']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
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

    return back()->with('status', 'password-updated');
    }

}
