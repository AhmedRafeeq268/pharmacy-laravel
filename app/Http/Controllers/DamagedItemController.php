<?php

namespace App\Http\Controllers;

use Mpdf\Mpdf;
use App\Models\Product;
use App\Models\PosSession;
use App\Models\DamagedItem;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\DamagedItemsExport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\StoreDamagedItemRequest;
use App\Http\Requests\UpdateDamagedItemRequest;

class DamagedItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        abort_if(Gate::denies('view-damaged-item'), 403);
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

        return Excel::download(new DamagedItemsExport($damagedItems), 'damagedItems.xlsx');
    }

    public function exportPDF(Request $request)
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

        // تحميل Blade كـ HTML
        $html = view('damaged.damagedItemsPDF', compact('damagedItems'))->render();

        // إعداد mPDF
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => app()->getLocale() == 'ar' ? 'Cairo' : 'dejavusans',
            'directionality' => app()->getLocale() == 'ar' ? 'rtl' : 'ltr',
        ]);

        // كتابة HTML في PDF
        $mpdf->WriteHTML($html);
        $filename = app()->getLocale() == 'ar' ? 'تقرير_التالف.pdf' : 'Damaged_Report.pdf';

        // تحميل PDF مباشرة
        return $mpdf->Output($filename, 'D');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort_if(Gate::denies('create-damaged-item'), 403);
        $products = Product::all();
        return view('damaged.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDamagedItemRequest $request)
    {
        $data = $request->validated();

        $product = Product::findOrFail($data['product_id']);

        if ($product->quantity < $data['quantity']) {
            return back()->withErrors(['quantity' => 'الكمية المدخلة أكبر من الكمية المتوفرة.']);
        }

        $product->decrement('quantity', $data['quantity']);

        $user_id = Auth::id() ?? 1;

        $currentSessionId = PosSession::where('user_id', $user_id)
                                    ->where('status', 'open')
                                    ->latest()
                                    ->value('id');

        if (!$currentSessionId) {
            return back()->withErrors(['session' => 'لا توجد جلسة مفتوحة حالياً.']);
        }

        DamagedItem::create([
            'product_id'  => $product->id,
            'quantity'    => $data['quantity'],
            'reason'      => $data['reason'],
            'reported_by' => $user_id,
            'session_id'  => $currentSessionId,
        ]);

        return redirect()->back()->with('success', 'تم تسجيل التالف بنجاح.');
    }



    /**
     * Display the specified resource.
     */
    public function show($damagedItemID )
    {
        abort_if(Gate::denies('view-damaged-item'), 403);
        $damagedItem = DamagedItem::findOrFail($damagedItemID);
        return view('damaged.show',compact('damagedItem'));
    }

    /**
     * Show the form for editing the specified resource.
     */
     public function edit($id){
        abort_if(Gate::denies('edit-damaged-item'), 403);
        $damagedItem=DamagedItem::findOrFail($id);
        $products = Product::select('id','name')->get();
        return view('damaged.edit',compact('damagedItem','products'));
    }

    public function update(UpdateDamagedItemRequest $request, $id)
    {
        $data = $request->validated();

        $damagedItem = DamagedItem::findOrFail($id);

        $oldQuantity = $damagedItem->quantity;

        $newQuantity = $data['quantity'];

        $quantityDifference = $newQuantity - $oldQuantity;

        $product = Product::findOrFail($data['product_id']);

        if ($quantityDifference > 0) {
            if ($product->quantity < $quantityDifference) {
                return back()->withErrors([
                    'quantity' => __('messages.damaged.not_enough_stock', [
                        'available' => $product->quantity
                    ])
                ])->withInput();
            }

            $product->decrement('quantity', $quantityDifference);
        } elseif ($quantityDifference < 0) {
            $product->increment('quantity', abs($quantityDifference));
        }

        $damagedItem->update([
            'product_id' => $data['product_id'],
            'quantity'   => $newQuantity,
            'reason'     => $data['reason'],
        ]);

        return to_route('damaged.index', ['page' => $request->get('page', 1)])
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
