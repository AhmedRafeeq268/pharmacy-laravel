<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:settings.edit')->only(['edit', 'update']);
        $this->middleware('permission:settings.activate')->only(['activate']);
        $this->middleware('permission:settings.deactivate')->only(['deactivate']);
    }

    public function edit()
    {
        return view('settings.edit');
    }

    public function update(Request $request)
    {
        // كود التحديث
    }

    public function activate()
    {
        // كود التفعيل
    }

    public function deactivate()
    {
        // كود إلغاء التفعيل
    }
}
