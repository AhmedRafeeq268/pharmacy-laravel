<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\StoreUsersRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(){
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    public function create(){
        $roles = Role::select('id','name')->get();
        return view('admin.users.create',compact('roles'));
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        $role = Role::find($data['role_id']);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $role->name,
        ]);

        // ربط المستخدم بالرول المختار
        $user->roles()->attach($data['role_id']);

        return redirect()->route('admin.users.index')->with('success', 'تم إضافة المستخدم بنجاح');
    }

    public function edit($id){
        $roles = Role::select('id','name')->get();
        $user = User::findOrFail($id);
        return view('admin.users.edit',compact('user','roles'));
    }

    public function update(UpdateUserRequest $request, $id)
    {
        $data = $request->validated();
        $user = User::findOrFail($id);
        $role = Role::findOrFail($data['role_id']);

        // تحديث البيانات الأساسية
        $updateData = [
            'name'  => $data['name'],
            'email' => $data['email'],
            'role'  => $role->name,
        ];

        // إذا المستخدم كتب باسورد جديد فقط، نحدثه
        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $user->update($updateData);

        // تحديث الرول في جدول rol  e_user
        $user->roles()->sync([$data['role_id']]);

        return redirect()->route('admin.users.index')->with('success', 'تم تحديث بيانات المستخدم بنجاح');
    }


    public function destroy(User $user){
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'تم حذف المستخدم');
    }

public function updateStatus(Request $request)
{
    $user = User::find($request->id);
    if($user) {
        $user->status = $request->status;
        $user->save();
        return response()->json(['success'=>'تم تحديث الحالة بنجاح.']);
    }
    return response()->json(['error'=>'المستخدم غير موجود.'], 404);
}


}
