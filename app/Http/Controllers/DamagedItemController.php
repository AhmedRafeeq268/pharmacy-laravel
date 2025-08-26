<?php

namespace App\Http\Controllers;

use App\Exports\DamagedItemsExport;
use App\Models\Product;
use App\Models\PosSession;
use App\Models\DamagedItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class DamagedItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $damagedItems = DamagedItem::with(['product'])
            ->when($search, function ($query) use ($search) {
                $query->where('quantity', 'like', "%$search%")
                      ->orWhereHas('product', fn($q) => $q->where('name', 'like', "%$search%"));
            })
            ->orderBy('id')
            ->paginate(8)
            ->appends($request->all());

        // إذا كان الطلب AJAX نعيد جزء الـ Table فقط
        if ($request->ajax()) {
            return view('damaged._table', compact('damagedItems'))->render();
        }

        // أما إذا كان تحميل الصفحة عادي
        return view('damaged.index', compact('damagedItems'));
    }

    public function export(Request $request)
    {
        $search = $request->input('search');

          $damagedItems = DamagedItem::with(['product'])
            ->when($search, function ($query) use ($search) {
                $query->where('quantity', 'like', "%$search%")
                      ->orWhereHas('product', fn($q) => $q->where('name', 'like', "%$search%"));
            })->get();

        if ($damagedItems->isEmpty()) {
            return redirect()->back()->with('error', 'لا توجد بيانات لتصديرها.');
        }

        return Excel::download(new DamagedItemsExport($search), 'damagedItems.xlsx');
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
    public function show($damagedItemID )
    {
        $damagedItem = DamagedItem::findOrFail($damagedItemID);
        return view('damaged.show',compact('damagedItem'));
    }

    /**
     * Show the form for editing the specified resource.
     */
     public function edit($id){
        $damagedItem=DamagedItem::findOrFail($id);
        $products = Product::select('id','name')->get();
        return view('damaged.edit',compact('damagedItem','products'));
    }

    public function update($id)
    {
        request()->validate([
            'product_id' => ['required'],
            'quantity' => ['required', 'numeric', 'min:1'],
            'reason' => ['required'],
        ]);

        $damagedItem = DamagedItem::findOrFail($id);

        // الكمية القديمة من السجل
        $oldQuantity = $damagedItem->quantity;

        // الكمية الجديدة المطلوبة
        $newQuantity = request()->quantity;

        // الفرق بين الكميتين
        $quantityDifference = $newQuantity - $oldQuantity;

        // المنتج المرتبط (سواء تغير المنتج أو بقي نفسه)
        $productId = request()->product_id;
        $product = Product::findOrFail($productId);

        // معالجة حسب الفرق
        if ($quantityDifference > 0) {
            // نحتاج خصم من المخزون
            if ($product->quantity < $quantityDifference) {
                return redirect()->back()->withErrors([
                    'quantity' => __('messages.damaged.not_enough_stock', [
                        'available' => $product->quantity
                    ])
                ])->withInput();
            }

            // خصم الفرق من المخزون
            $product->quantity -= $quantityDifference;
            $product->save();
        } elseif ($quantityDifference < 0) {
            // زادت الكمية في المخزون لأن التالف قل
            $product->quantity += abs($quantityDifference);
            $product->save();
        }

        // تحديث سجل التالف
        $damagedItem->update([
            'product_id' => $productId,
            'quantity' => $newQuantity,
            'reason' => request()->reason,
        ]);

        $page = request()->get('page', 1);
        return to_route('damaged.index', ['page' => $page])
            ->with('success', __('messages.updated'));
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request ,$damagedId)
    {
        $damagedItem = DamagedItem::find($damagedId);
        if (!$damagedItem)
        {
            return redirect()->back()->with('error', __('messages.not_found'));
        }
        $damagedItem->delete();
        $page = $request->get('page', 1);
        return to_route('damaged.index',['page' => $page])
        ->with('success', __('messages.deleted'));
    }
}
