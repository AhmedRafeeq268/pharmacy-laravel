<?php

namespace App\Http\Controllers;

use id;
use App\Models\Expense;
use App\Models\PosSession;
use Illuminate\Http\Request;
use App\Models\CashBoxTransaction;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function create(){
        return view('expenses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:salary,rent,bills,other',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
        ]);

        $expense = Expense::create([
            'type' => $validated['type'],
            'description' => $validated['description'],
            'amount' => $validated['amount'],
            'expense_date' => $validated['expense_date'],
            'created_by' => Auth::id(),
        ]);
        $user_id = Auth::id() ?? 1;
        // الجلسة المفتوحة الحالية للصندوق
        $currentSessionId = PosSession::where('user_id', $user_id)
                                    ->where('status', 'open')
                                    ->latest()
                                    ->value('id');

        // تسجيل المصروف نقدًا في الصندوق
        CashBoxTransaction::create([
            'session_id' => $currentSessionId,
            'type' => 'expense',
            'amount' => -$expense->amount,
            'note' => $expense->description,
            'expense_id' => $expense->id,
        ]);

        return redirect()->route('expenses.create')->with('success', 'تم حفظ المصروف بنجاح.');
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
