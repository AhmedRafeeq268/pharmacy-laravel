<?php

namespace App\Http\Controllers;

use Mpdf\Mpdf;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Exports\ProductsExport;
use App\Imports\ProductsImport;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;

class ProductController extends Controller
{

    public function index(Request $request)
    {
        abort_if(Gate::denies('view-product'), 403);
        $search = $request->input('search');

        $products = Product::with(['productCategory'])
            ->when($search, function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                    ->orWhereHas('productCategory', fn($q) => $q->where('name', 'like', "%$search%"));
        })->orderBy('id', 'desc')->paginate(8); // حدد عدد العناصر في كل صفحة

        // إذا كان الطلب AJAX نعيد جزء الـ Table فقط
        if ($request->ajax()) {
            return view('product._table', compact('products'))->render();
        }

        // أما إذا كان تحميل الصفحة عادي
        return view('product.index', compact('products'));
    }

    public function export(Request $request)
    {
        $search = $request->input('search');

        $products = Product::with(['productCategory'])
        ->when($search, function ($query) use ($search) {
            $query->where('name', 'like', "%$search%")
                ->orWhereHas('productCategory', fn($q) => $q->where('name', 'like', "%$search%"));
        })->get();

        if ($products->isEmpty()) {
            return redirect()->back()->with('error', 'لا توجد بيانات لتصديرها.');
        }

        return Excel::download(new ProductsExport($products), 'product.xlsx');
    }

    public function exportPDF(Request $request)
    {
        $search = $request->input('search');

        $productsItems = Product::with(['productCategory'])
        ->when($search, function ($query) use ($search) {
            $query->where('name', 'like', "%$search%")
                ->orWhereHas('productCategory', fn($q) => $q->where('name', 'like', "%$search%"));
        })->get();

        if ($productsItems->isEmpty()) {
            return redirect()->back()->with('error', 'لا توجد بيانات لتصديرها.');
        }

        // تحميل Blade كـ HTML
        $html = view('product.productItemsPDF', compact('productsItems'))->render();

        // إعداد mPDF
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => app()->getLocale() == 'ar' ? 'Cairo' : 'dejavusans',
            'directionality' => app()->getLocale() == 'ar' ? 'rtl' : 'ltr',
        ]);

        // كتابة HTML في PDF
        $mpdf->WriteHTML($html);
        $filename = app()->getLocale() == 'ar' ? 'تقرير المنتجات.pdf' : 'Product_Report.pdf';

        // تحميل PDF مباشرة
        return $mpdf->Output($filename, 'D');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        $import = new ProductsImport;

        // استيراد الملف
        Excel::import($import, $request->file('file'));

        // التحقق من الأخطاء
        if (!empty($import->errors)) {
            $msg = implode('<br>', $import->errors);
            return redirect()->back()->with('error', $msg);
        }

        return redirect()->back()->with('success', 'تم استيراد المنتجات بنجاح!');
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort_if(Gate::denies('create-product'), 403);
         $productCategories = ProductCategory::select('id','name')->get();
        return view('product.create',compact('productCategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request){
        $user  = Auth::user();
        Product::create($request->validated());
        return to_route('product.create')->with('success', __('messages.added'));
    }

    /**
     * Display the specified resource.
     */
     public function show($id){
        abort_if(Gate::denies('view-product'), 403);
        $product = Product::with('productCategory')->findOrFail($id);
        return view('product.show',compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($productId){
        abort_if(Gate::denies('edit-product'), 403);
        $product=Product::findOrFail($productId);
         $productCategories = ProductCategory::select('id','name')->get();

        return view('product.edit',compact('product','productCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request ,$productId){
        $product = Product::findOrFail($productId);
        $product->update($request->validated());
        $page = $request->get('page', 1);
        return to_route('product.index',['page' => $page])
        ->with('success', __('messages.updated'));
    }

    public function destroy(Request $request ,$productId){
        $product = Product::find($productId);
        if (!$product)
        {
            return redirect()->back()->with('error', __('messages.not_found'));
        }
        $product->delete();
        $page = $request->get('page', 1);
        return to_route('product.index',['page' => $page])
        ->with('success', __('messages.deleted'));
    }
}
