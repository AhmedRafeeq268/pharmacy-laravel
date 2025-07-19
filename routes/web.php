<?php

use App\Models\CodesTb;
use App\Exports\UsersExport;
use App\Exports\BosBillsExport;
use App\Exports\ProductsExport;
use App\Models\ProductCategory;
use App\Exports\CustomersExport;

use App\Exports\EmployeesExport;
use App\Exports\SuppliersExport;
use Illuminate\Support\Facades\App;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\CodesTbController;
use App\Http\Controllers\FarmacyController;
use App\Http\Controllers\PosBillController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\customerController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BillCertifiedController;
use App\Http\Controllers\PurchasesBillsController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\PurchasesBillsDetailsController;

Route::get('/toggle-language', function () {
        $locale = Session::get('locale', config('app.locale', 'ar'));
        $newLocale = $locale === 'ar' ? 'en' : 'ar';
        Session::put('locale', $newLocale);
        App::setLocale($newLocale);
        return redirect()->back();
    })->name('toggle.language');

    Route::get('locale/{lang}', [LocaleController::class, 'setLocale'])->name('change.language');
    Route::middleware('auth')->group(function () {

    Route::get('/', function () {
        return view('welcome');
    });



    Route::get('farmacy',[FarmacyController::class,'index'])->name('farmacy.index');

    Route::get('farmacy/add_new_customer',function(){
    return view('farmacy.add_new_customer');
    })->name('farmacy.addcustomer');

    Route::get('/customer',[customerController::class,'index'])->name('customer.index');
    Route::get('/customer/create',[customerController::class,'create'])->name('customer.create');
    Route::post('/customer',[customerController::class,'store'])->name('customer.store');
    Route::get('/customer/{customer}/edit',[customerController::class,'edit'])->name('customer.edit');
    Route::put('/customer/{customer}',[customerController::class,'update'])->name('customer.update');
    Route::delete('/customer/{customer}',[customerController::class,'destroy'])->name('customer.destroy');
    Route::get('/customer/export', [CustomerController::class, 'export'])->name('customer.printCustomersExcel');

    Route::get('/employee',[EmployeeController::class,'index'])->name('employee.index');
    Route::get('/employee/create',[EmployeeController::class,'create'])->name('employee.create');
    Route::post('/employee',[EmployeeController::class,'store'])->name('employee.store');
    Route::get('/employee/{employee}/edit',[EmployeeController::class,'edit'])->name('employee.edit');
    Route::put('/employee/{employee}',[EmployeeController::class,'update'])->name('employee.update');
    Route::delete('/employee/{employee}',[EmployeeController::class,'destroy'])->name('employee.destroy');
    Route::get('/employee/export', [EmployeeController::class, 'export'])->name('employee.printEmployeesExcel');

    Route::get('/codeTb',[CodesTbController::class,'index'])->name('codeTb.index');
    Route::get('/codeTb/create',[CodesTbController::class,'create'])->name('codeTb.create');
    Route::post('/codeTb',[CodesTbController::class,'store'])->name('codeTb.store');
    Route::get('/codeTb/{codeTb}/edit',[CodesTbController::class,'edit'])->name('codeTb.edit');
    Route::put('/codeTb/{codeTb}',[CodesTbController::class,'update'])->name('codeTb.update');
    Route::delete('/codeTb/{codeTb}',[CodesTbController::class,'destroy'])->name('codeTb.destroy');


    Route::get('/supplier',[SupplierController::class,'index'])->name('supplier.index');
    Route::get('/supplier/create',[SupplierController::class,'create'])->name('supplier.create');
    Route::post('/supplier',[SupplierController::class,'store'])->name('supplier.store');
    Route::get('/supplier/{supplier}/edit',[SupplierController::class,'edit'])->name('supplier.edit');
    Route::put('/supplier/{supplier}',[SupplierController::class,'update'])->name('supplier.update');
    Route::delete('/supplier/{supplier}',[SupplierController::class,'destroy'])->name('supplier.destroy');
    Route::get('/supplier/export', [SupplierController::class, 'export'])->name('supplier.printSuppliersExcel');


    Route::get('/product',[ProductController::class,'index'])->name('product.index');
    Route::get('/product/create',[ProductController::class,'create'])->name('product.create');
    Route::post('/product',[ProductController::class,'store'])->name('product.store');
    Route::get('/product/{product}/edit',[ProductController::class,'edit'])->name('product.edit');
    Route::put('/product/{product}',[ProductController::class,'update'])->name('product.update');
    Route::delete('/product/{product}',[ProductController::class,'destroy'])->name('product.destroy');
    Route::get('/product/export', [ProductController::class, 'export'])->name('product.printProductsExcel');


    Route::get('/productCategory',[ProductCategoryController::class,'index'])->name('productCategory.index');
    Route::get('/productCategory/create',[ProductCategoryController::class,'create'])->name('productCategory.create');
    Route::post('/productCategory',[ProductCategoryController::class,'store'])->name('productCategory.store');
    Route::get('/productCategory/{productCategory}/edit',[ProductCategoryController::class,'edit'])->name('productCategory.edit');
    Route::put('/productCategory/{productCategory}',[ProductCategoryController::class,'update'])->name('productCategory.update');
    Route::delete('/productCategory/{productCategory}',[ProductCategoryController::class,'destroy'])->name('productCategory.destroy');

    Route::get('/bill',[PurchasesBillsController::class,'index'])->name('bill.index');
    Route::get('/bill/create',[PurchasesBillsController::class,'create'])->name('bill.create');
    Route::post('/bill',[PurchasesBillsController::class,'store'])->name('bill.store');
    Route::get('/bill/{bill}/edit',[PurchasesBillsController::class,'edit'])->name('bill.edit');
    Route::put('/bill/{bill}',[PurchasesBillsController::class,'update'])->name('bill.update');
    Route::delete('/bill/{bill}',[PurchasesBillsController::class,'destroy'])->name('bill.destroy');

    Route::get('/billDetails',[PurchasesBillsDetailsController::class,'index'])->name('billDetails.index');
    Route::get('/billDetails/{billId}/create',[PurchasesBillsDetailsController::class,'create'])->name('billDetails.create');
    Route::post('/billDetails/{billId}',[PurchasesBillsDetailsController::class,'store'])->name('billDetails.store');
    Route::get('/billDetails/{billDetailsId}/edit',[PurchasesBillsDetailsController::class,'edit'])->name('billDetails.edit');
    Route::put('/billDetails/{billDetailsId}',[PurchasesBillsDetailsController::class,'update'])->name('billDetails.update');
    Route::delete('/billDetails/{billDetailsId}',[PurchasesBillsDetailsController::class,'destroy'])->name('billDetails.destroy');

    Route::get('/billCertified/{billId}',[BillCertifiedController::class,'index'])->name('billCertified.index');
    Route::post('/billCertified/{billId}',[BillCertifiedController::class,'store'])->name('billCertified.store');
    Route::post('/billCertified/reject/{billId}', [BillCertifiedController::class, 'reject'])->name('billCertified.reject');


    Route::get('/pos',[PosBillController::class,'index'])->name('pos.index');
    Route::get ('/pos/create/{pos_bill_id?}', [PosBillController::class, 'create'])->name('pos.create');
    Route::post('/pos/store/{pos_bill_id?}', [PosBillController::class, 'store'])->name('pos.store');
    Route::get('/pos/{pos}/edit',[PosBillController::class,'edit'])->name('pos.edit');
    Route::put('/pos/{pos}',[PosBillController::class,'update'])->name('pos.update');
    Route::delete('/pos/{pos}',[PosBillController::class,'destroy'])->name('pos.destroy');
    Route::get('/pos/fetchProduct/{barcode}', [PosBillController::class, 'fetchProduct'])->name('pos.fetchProduct');
    Route::post('/pos/finish/{pos_bill_id}', [PosBillController::class, 'finish'])->name('pos.finish');
    Route::post('pos/closeCashbox',[PosBillController::class,'closeCashbox'])->name('pos.closeCashbox');
    Route::get('/pos/print/{id}',[PosBillController::class,'print'])->name('pos.print');
    Route::get('/pos/export', [PosBillController::class, 'export'])->name('pos.printPosBillsExcel');


    Route::get('/change-password', [AuthController::class, 'changePasswordForm'])->name('password.change');
    Route::patch('/update-password', [AuthController::class, 'updatePassword'])->name('password.update');

    Route::get('/dashboard',function(){
        return view('dashboard');
    })->name('dashboard');


    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        // Route::get('/admin/users/create', [UserController::class, 'create'])->name('admin.users.create');
        // Route::post('/admin/users', [UserController::class, 'store'])->name('admin.users.store');
        // Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

        // Route::get('/export-users', function () {
        // return Excel::download(new UsersExport, 'users.xlsx');
        // })->name('user.printUsersExcel');

        // Route::get('/export-products', function () {
        // return Excel::download(new ProductsExport, 'product.xlsx');
        // })->name('product.printProductsExcel');

        // Route::get('/export-customers', function () {
        // return Excel::download(new CustomersExport, 'customer.xlsx');
        // })->name('customer.printCustomersExcel');

        // Route::get('/export-employees', function () {
        // return Excel::download(new EmployeesExport, 'employee.xlsx');
        // })->name('employee.printEmployeesExcel');

        // Route::get('/export-posBills', function () {
        // return Excel::download(new BosBillsExport, 'posBill.xlsx');
        // })->name('posBill.printPosBillsExcel');

        // Route::get('/export-suppliers', function () {
        // return Excel::download(new SuppliersExport, 'supplier.xlsx');
        // })->name('supplier.printSuppliersExcel');
    });

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);




    // Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    // Route::post('/login', [LoginController::class, 'login']);
