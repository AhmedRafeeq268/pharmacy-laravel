<?php

namespace App\Http\Controllers;

use id;
use Mpdf\Mpdf;
use App\Models\Expense;
use App\Models\PosSession;
use Illuminate\Http\Request;
use App\Exports\ExpensesExport;
use App\Models\CashBoxTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\StoreExpenseRequest;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('view-expenses'), 403);
        $search = $request->input('search');

        $expenses = Expense::with(['user'])
            ->when($search, function ($query) use ($search) {
                    $query->where('type', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('amount', 'like', "%{$search}%")
                    ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%$search%"));
        })->orderBy('id', 'desc')->paginate(8);


        // إذا كان الطلب AJAX نعيد جزء الـ Table فقط
        if ($request->ajax()) {
            return view('expenses._table', compact('expenses'))->render();
        }

        // أما إذا كان تحميل الصفحة عادي
        return view('expenses.index', compact('expenses'));
    }

    public function export(Request $request)
    {
        $search = $request->input('search');

         $expenses = Expense::with(['user'])
            ->when($search, function ($query) use ($search) {
                    $query->where('type', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('amount', 'like', "%{$search}%")
                    ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%$search%"));
        })->get();


        if ($expenses->isEmpty()) {
            return redirect()->back()->with('error', 'لا توجد بيانات لتصديرها.');
        }

        return Excel::download(new ExpensesExport($expenses), 'expenses.xlsx');
    }

    public function exportPDF(Request $request)
    {
        $search = $request->input('search');

        $expensesItems = Expense::with(['user'])
            ->when($search, function ($query) use ($search) {
                    $query->where('type', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('amount', 'like', "%{$search}%")
                    ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%$search%"));
        })->get();

        if ($expensesItems->isEmpty()) {
            return redirect()->back()->with('error', 'لا توجد بيانات لتصديرها.');
        }

        // تحميل Blade كـ HTML
        $html = view('expenses.expensesItemsPDF', compact('expensesItems'))->render();

        // إعداد mPDF
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => app()->getLocale() == 'ar' ? 'Cairo' : 'dejavusans',
            'directionality' => app()->getLocale() == 'ar' ? 'rtl' : 'ltr',
        ]);

        // كتابة HTML في PDF
        $mpdf->WriteHTML($html);
        $filename = app()->getLocale() == 'ar' ? 'تقرير المصروفات.pdf' : 'Expenses_Report.pdf';

        // تحميل PDF مباشرة
        return $mpdf->Output($filename, 'D');
    }


    public function create(){
        abort_if(Gate::denies('create-expenses'), 403);
        return view('expenses.create');
    }

    public function store(StoreExpenseRequest $request)
    {
        $data = $request->validated();
        $user_id = Auth::id() ?? 1;
        $data['created_by'] = $user_id;
        $expense = Expense::create($data);

        // الجلسة المفتوحة الحالية للصندوق
        $currentSessionId = PosSession::where('user_id', $user_id)
                                    ->where('status', 'open')
                                    ->latest()
                                    ->value('id');

        if (!$currentSessionId) {
            return back()->withErrors(['session' => 'لا توجد جلسة مفتوحة حالياً.']);
        }

        // تسجيل المصروف نقدًا في الصندوق
        CashBoxTransaction::create([
            'session_id' => $currentSessionId,
            'type'       => 'expense',
            'amount'     => -$expense->amount,
            'note'       => $expense->description,
            'expense_id' => $expense->id,
        ]);

        return redirect()->route('expenses.create')
            ->with('success', 'تم حفظ المصروف بنجاح.');
    }


    public function report(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');

        $query = Expense::query();

        if ($from && $to) {
            $query->whereBetween('expense_date', [$from, $to]);
        }

        $expenses = $query->get();
        $total = $expenses->sum('amount');

        return view('expenses.report', compact('expenses', 'total', 'from', 'to'));
    }



}
