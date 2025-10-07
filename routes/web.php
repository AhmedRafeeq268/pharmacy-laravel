<?php

use App\Models\Permission;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Session;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DebtController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CodesTbController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FarmacyController;
use App\Http\Controllers\PosBillController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DamagedItemController;
use App\Http\Controllers\SalesReturnController;
use App\Http\Controllers\BalanceStoreController;
use App\Http\Controllers\BillCertifiedController;
use App\Http\Controllers\PurchasesBillController;
use App\Http\Controllers\PurchaseReturnController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\PurchasesBillsDetailsController;

// -------------------- Language Switch --------------------
Route::get('/toggle-language', function () {
    $locale = Session::get('locale', config('app.locale', 'ar'));
    $newLocale = $locale === 'ar' ? 'en' : 'ar';
    Session::put('locale', $newLocale);
    App::setLocale($newLocale);
    return redirect()->back();
})->name('toggle.language');
Route::get('locale/{lang}', [LocaleController::class, 'setLocale'])->name('change.language');

// -------------------- Authentication --------------------
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// -------------------- Authenticated Routes --------------------
Route::middleware('auth')->group(function () {

    Route::get('/', fn() => view('welcome'));
    Route::get('/dashboard', function() {
        return view('dashboard');
    })->middleware('permission:view reports');


    // إعدادات الأدمن
    Route::middleware('admin')->prefix('admin/')->name('admin.')->group(function () {

        Route::prefix('settings')->name('settings.')->group(function() {
            Route::get('edit', [SettingController::class,'edit'])
                ->name('edit')
                ->middleware('permission:settings.edit');

            Route::post('update', [SettingController::class,'update'])
                ->name('update')
                ->middleware('permission:settings.edit');

        });

        Route::get('dashboard', [AdminController::class, 'dashboard'])->name('dashboard');


        Route::prefix('users')->name('users.')->group(function(){
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::post('/store', [UserController::class, 'store'])->name('store');
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [UserController::class, 'update'])->name('update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
            Route::post('status', [UserController::class, 'updateStatus'])->name('updateStatus');

        });

        // ✅ راوتس الصلاحيات الإضافية
        Route::get('/{user}/extra-permissions', [AdminController::class, 'editPermission'])->name('extra_permissions.edit');
        Route::put('/{user}/extra-permissions', [AdminController::class, 'updateExtraPermissions'])->name('extra_permissions.update');

    });

    // -------------------- Pharmacy --------------------
    Route::prefix('farmacy')->name('farmacy.')->group(function () {
        Route::get('/', [FarmacyController::class, 'index'])->name('index');
        Route::get('/add_new_customer', fn() => view('farmacy.add_new_customer'))->name('addcustomer');
    });

    // -------------------- Customers --------------------
    Route::prefix('customer')->name('customer.')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::get('/create', [CustomerController::class, 'create'])->name('create');
        Route::post('/store', [CustomerController::class, 'store'])->name('store');
        Route::get('/exportExcel', [CustomerController::class, 'export'])->name('printCustomersExcel');
        Route::get('/exportPDF', [CustomerController::class, 'exportPDF'])->name('printCustomerPdf');
        Route::get('/{customer}/edit', [CustomerController::class, 'edit'])->name('edit');
        Route::put('/{customer}', [CustomerController::class, 'update'])->name('update');
        Route::delete('/{customer}', [CustomerController::class, 'destroy'])->name('destroy');
        Route::get('/{customer}', [CustomerController::class, 'show'])->name('show');
    });

    // -------------------- Employees --------------------
    Route::prefix('employee')->name('employee.')->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])->name('index');
        Route::get('/create', [EmployeeController::class, 'create'])->name('create');
        Route::post('/store', [EmployeeController::class, 'store'])->name('store');
        Route::get('/exportExcel', [EmployeeController::class, 'export'])->name('printEmployeesExcel');
        Route::get('/exportPDF', [EmployeeController::class, 'exportPDF'])->name('printEmployeePdf');
        Route::get('/{employee}/edit', [EmployeeController::class, 'edit'])->name('edit');
        Route::put('/{employee}', [EmployeeController::class, 'update'])->name('update');
        Route::delete('/{employee}', [EmployeeController::class, 'destroy'])->name('destroy');
        Route::get('/{employee}', [EmployeeController::class, 'show'])->name('show');
    });

    // -------------------- Suppliers --------------------
    Route::prefix('supplier')->name('supplier.')->group(function () {
        Route::get('/', [SupplierController::class, 'index'])->name('index');
        Route::get('/create', [SupplierController::class, 'create'])->name('create');
        Route::post('/store', [SupplierController::class, 'store'])->name('store');
        Route::get('/exportExcel', [SupplierController::class, 'export'])->name('printSuppliersExcel');
        Route::get('/exportPDF', [SupplierController::class, 'exportPDF'])->name('printSupplierPdf');
        Route::get('/{supplier}/edit', [SupplierController::class, 'edit'])->name('edit');
        Route::put('/{supplier}', [SupplierController::class, 'update'])->name('update');
        Route::delete('/{supplier}', [SupplierController::class, 'destroy'])->name('destroy');
        Route::get('/{supplier}', [SupplierController::class, 'show'])->name('show');
    });

    // -------------------- Products --------------------
    Route::prefix('product')->name('product.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::post('/store', [ProductController::class, 'store'])->name('store');
        Route::get('/exportExcel', [ProductController::class, 'export'])->name('exportExcel');
        Route::get('/exportPDF', [ProductController::class, 'exportPDF'])->name('exportPDF');
        Route::post('/import', [ProductController::class, 'import'])->name('import');
        Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
        Route::put('/{product}', [ProductController::class, 'update'])->name('update');
        Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
        Route::get('/{product}', [ProductController::class, 'show'])->name('show');
    });

    // -------------------- Product Categories --------------------
    Route::prefix('productCategory')->name('productCategory.')->group(function () {
        Route::get('/', [ProductCategoryController::class, 'index'])->name('index');
        Route::get('/create', [ProductCategoryController::class, 'create'])->name('create');
        Route::post('/store', [ProductCategoryController::class, 'store'])->name('store');
        Route::get('/{productCategory}/edit', [ProductCategoryController::class, 'edit'])->name('edit');
        Route::put('/{productCategory}', [ProductCategoryController::class, 'update'])->name('update');
        Route::delete('/{productCategory}', [ProductCategoryController::class, 'destroy'])->name('destroy');
        Route::get('/{productCategory}', [ProductCategoryController::class, 'show'])->name('show');
    });

    // -------------------- Purchases Bills --------------------
    Route::prefix('bill')->name('bill.')->group(function () {
        Route::get('/', [PurchasesBillController::class, 'index'])->name('index');
        Route::get('/create', [PurchasesBillController::class, 'create'])->name('create');
        Route::post('/store', [PurchasesBillController::class, 'store'])->name('store');
        Route::get('/{bill}/edit', [PurchasesBillController::class, 'edit'])->name('edit');
        Route::put('/{bill}', [PurchasesBillController::class, 'update'])->name('update');
        Route::delete('/{bill}', [PurchasesBillController::class, 'destroy'])->name('destroy');
        Route::get('/{billId}/print', [PurchasesBillController::class, 'print'])->name('print');
        Route::get('/{bill}', [PurchasesBillController::class, 'show'])->name('show');
    });
    // -------------------- Purchases Bills Details --------------------
    Route::prefix('billDetails')->name('billDetails.')->group(function () {
        Route::get('/', [PurchasesBillsDetailsController::class, 'index'])->name('index');
        Route::get('/create/{billId}', [PurchasesBillsDetailsController::class, 'create'])->name('create');
        Route::post('/store', [PurchasesBillsDetailsController::class, 'store'])->name('store');
        Route::get('/{billDetailsId}/edit', [PurchasesBillsDetailsController::class, 'edit'])->name('edit');
        Route::put('/{billDetailsId}', [PurchasesBillsDetailsController::class, 'update'])->name('update');
        Route::delete('/{billDetailsId}', [PurchasesBillsDetailsController::class, 'destroy'])->name('destroy');
        Route::get('/{billId}/print', [PurchasesBillController::class, 'print'])->name('print');
        Route::get('/{billId}/close', [PurchasesBillsDetailsController::class, 'closeBill'])->name('close');
    });



    // -------------------- POS --------------------
    Route::prefix('pos')->name('pos.')->group(function () {
        Route::get('/', [PosBillController::class, 'index'])->name('index');
        Route::get('/create/{pos_bill_id?}', [PosBillController::class, 'create'])->name('create');
        Route::post('/store/{pos_bill_id?}', [PosBillController::class, 'store'])->name('store');
        Route::post('/finish/{pos_bill_id}', [PosBillController::class, 'finish'])->name('finish');
        Route::get('/{pos}/edit', [PosBillController::class, 'edit'])->name('edit');
        Route::put('/{pos}', [PosBillController::class, 'update'])->name('update');
        Route::delete('/{pos}', [PosBillController::class, 'destroy'])->name('destroy');
        Route::get('/fetchProduct/{barcode}', [PosBillController::class, 'fetchProduct'])->name('fetchProduct');
        Route::post('/closeCashbox', [PosBillController::class, 'closeCashbox'])->name('closeCashbox');
        Route::get('/print/{id}', [PosBillController::class, 'print'])->name('print');
        Route::get('/cashboxReport/{session}', [PosBillController::class, 'cashboxReport'])->name('cashboxReport');
        Route::get('/exportExcel', [PosBillController::class, 'export'])->name('printPosBillsExcel');
        Route::get('/exportPDF', [PosBillController::class, 'exportPDF'])->name('printPosPdf');
        // الدفع
        Route::get('/pay/{pos_bill}', [PosBillController::class, 'showPaymentPage'])->name('paymentPage');
        Route::post('/pay/{pos_bill}', [PosBillController::class, 'processPayment'])->name('paymentProcess');
    });

    // -------------------- Debts --------------------
    Route::prefix('debts')->name('debts.')->group(function () {
        Route::get('/', [DebtController::class, 'index'])->name('index');
        Route::get('/search', [DebtController::class, 'searchForm'])->name('searchForm');
        Route::get('/ajax/{customer}', [DebtController::class, 'ajaxDebts'])->name('ajax');
        Route::post('/pay', [DebtController::class, 'payDebt'])->name('pay');
        Route::get('/customer/{customer_id}/{total_remaining?}', [DebtController::class, 'show'])->name('show');
        Route::delete('/{debt}', [DebtController::class, 'destroy'])->name('destroy');
        Route::get('/exportExcel', [DebtController::class, 'export'])->name('printDebtsExcel');
        Route::get('/exportPDF', [DebtController::class, 'exportPDF'])->name('printDebtsPdf');
    });

    // -------------------- Expenses --------------------
    Route::prefix('expenses')->name('expenses.')->group(function () {
        Route::get('/', [ExpenseController::class, 'index'])->name('index');
        Route::get('/create', [ExpenseController::class, 'create'])->name('create');
        Route::post('/store', [ExpenseController::class, 'store'])->name('store');
        Route::get('/report', [ExpenseController::class, 'report'])->name('report');
        Route::get('/exportExcel', [ExpenseController::class, 'export'])->name('printExpensesExcel');
        Route::get('/exportPDF', [ExpenseController::class, 'exportPDF'])->name('printExpensesPdf');
        Route::get('/{expense}', [ExpenseController::class, 'show'])->name('show');
    });

    // -------------------- Damaged Items --------------------
    Route::prefix('damaged')->name('damaged.')->group(function () {
        Route::get('/', [DamagedItemController::class, 'index'])->name('index');
        Route::get('/create', [DamagedItemController::class, 'create'])->name('create');
        Route::post('/store', [DamagedItemController::class, 'store'])->name('store');
        Route::get('/exportExcel', [DamagedItemController::class, 'export'])->name('printDamagedExcel');
        Route::get('/exportPDF', [DamagedItemController::class, 'exportPDF'])->name('printDamagedPdf');
        Route::get('/{damagedItem}/edit', [DamagedItemController::class, 'edit'])->name('edit');
        Route::put('/{damagedItem}', [DamagedItemController::class, 'update'])->name('update');
        Route::delete('/{damagedItem}', [DamagedItemController::class, 'destroy'])->name('destroy');
        Route::get('/{damagedItemId}', [DamagedItemController::class, 'show'])->name('show');
    });

    // -------------------- Sales Returns --------------------
    Route::prefix('salesReturn')->name('salesReturn.')->group(function () {
        Route::get('/', [SalesReturnController::class, 'index'])->name('index');
        Route::get('/create', [SalesReturnController::class, 'create'])->name('create');
        Route::post('/store', [SalesReturnController::class, 'store'])->name('store');
        Route::get('/{billNumber}/details', [SalesReturnController::class, 'getBillDetails'])->name('details');
        Route::get('/exportExcel', [SalesReturnController::class, 'export'])->name('printSalesReturnsExcel');
        Route::get('/exportPDF', [SalesReturnController::class, 'exportPDF'])->name('printSalesReturnsPdf');
        Route::get('/{id}', [SalesReturnController::class, 'show'])->name('show');
    });

    // -------------------- Purchases Returns --------------------
    Route::prefix('purchaseReturns')->name('purchaseReturns.')->group(function () {
        Route::get('/', [PurchaseReturnController::class, 'index'])->name('index');
        Route::get('/create', [PurchaseReturnController::class, 'create'])->name('create');
        Route::post('/store', [PurchaseReturnController::class, 'store'])->name('store');
        Route::get('/exportExcel', [PurchaseReturnController::class, 'export'])->name('printPurchaseReturnsExcel');
        Route::get('/exportPDF', [PurchaseReturnController::class, 'exportPDF'])->name('printPurchaseReturnsPdf');
        Route::get('/{purchaseReturn}', [PurchaseReturnController::class, 'show'])->name('show');
        Route::get('/{purchaseReturn}/edit', [PurchaseReturnController::class, 'edit'])->name('edit');
        Route::put('/{purchaseReturn}', [PurchaseReturnController::class, 'update'])->name('update');
        Route::delete('/{purchaseReturn}', [PurchaseReturnController::class, 'destroy'])->name('destroy');
    });

    // -------------------- Certified Bills --------------------
    Route::prefix('billCertified')->name('billCertified.')->group(function () {
        Route::get('/{billId}',[BillCertifiedController::class,'index'])->name('index');
        Route::post('/{billId}',[BillCertifiedController::class,'store'])->name('store');
        Route::post('/reject/{billId}', [BillCertifiedController::class, 'reject'])->name('reject');

    });


    // -------------------- Codes Table --------------------
    Route::prefix('codeTb')->name('codeTb.')->group(function () {
        Route::get('/', [CodesTbController::class, 'index'])->name('index');
        Route::get('/create', [CodesTbController::class, 'create'])->name('create');
        Route::post('/store', [CodesTbController::class, 'store'])->name('store');
        Route::get('/{codeTb}/edit', [CodesTbController::class, 'edit'])->name('edit');
        Route::put('/{codeTb}', [CodesTbController::class, 'update'])->name('update');
        Route::delete('/{codeTb}', [CodesTbController::class, 'destroy'])->name('destroy');
        Route::get('/{codeTb}', [CodesTbController::class, 'show'])->name('show');
    });

    // -------------------- Reports --------------------
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/profitLoss/filter', [ReportController::class, 'profitLossFilter'])->name('profitLossFilter');
        Route::get('/profitLoss', [ReportController::class, 'profitLoss'])->name('profitLoss');
    });

    // routes/web.php
    Route::get('/balanceStore', [BalanceStoreController::class, 'index'])->name('balanceStore.index');


    // -------------------- Auth Tools --------------------
    Route::get('/change-password', [AuthController::class, 'changePasswordForm'])->name('password.change');
    Route::patch('/update-password', [AuthController::class, 'updatePassword'])->name('password.update');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/force/logout', [LogoutController::class, 'forceLogout'])->name('force.logout');
});
