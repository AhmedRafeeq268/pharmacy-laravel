<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PosSession;
use App\Models\DamagedItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DamagedItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::all();
        return view('damaged.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
        ]);

        $product = Product::findOrFail($data['product_id']);

        if ($product->quantity < $data['quantity']) {
            return back()->withErrors(['quantity' => 'الكمية المدخلة أكبر من الكمية المتوفرة.']);
        }

        // خصم الكمية
        $product->quantity -= $data['quantity'];
        $product->save();

        $user_id = Auth::id() ?? 1;

        $currentSessionId = PosSession::where('user_id', $user_id)
                                    ->where('status', 'open')
                                    ->latest()
                                    ->value('id');

        // تحقق أن هناك جلسة مفتوحة
        if (!$currentSessionId) {
            return back()->withErrors(['session' => 'لا توجد جلسة مفتوحة حالياً.']);
        }

        // حفظ التالف
        DamagedItem::create([
            'product_id' => $product->id,
            'quantity' => $data['quantity'],
            'reason' => $data['reason'],
            'reported_by' => $user_id,
            'session_id' => $currentSessionId,
        ]);

        return redirect()->back()->with('success', 'تم تسجيل التالف بنجاح.');
    }


    /**
     * Display the specified resource.
     */
    public function show(DamagedItem $damagedItem)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DamagedItem $damagedItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DamagedItem $damagedItem)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DamagedItem $damagedItem)
    {
        //
    }
}
