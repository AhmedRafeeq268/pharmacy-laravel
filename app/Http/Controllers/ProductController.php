<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Exports\PosBillsExport;
use App\Exports\ProductsExport;
use App\Models\ProductCategory;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{

    public function index(Request $request)
    {
        $search = $request->input('search');

        $products = Product::with(['productCategory'])
            ->when($search, function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                    ->orWhereHas('productCategory', fn($q) => $q->where('name', 'like', "%$search%"));
        })->paginate(8); // حدد عدد العناصر في كل صفحة

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
        })->paginate(8);

        if ($products->isEmpty()) {
            return redirect()->back()->with('error', 'لا توجد بيانات لتصديرها.');
        }

        return Excel::download(new ProductsExport($search), 'product.xlsx');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
         $productCategories = ProductCategory::select('id','name')->get();
        return view('product.create',compact('productCategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request){
        request()->validate([
            'name' => ['required'],
            'barcode' => ['required'],
            'manufacture_company' => ['required'],
            'unit_price' => ['required'],
            'category_id' => ['required'],

        ]);



        // $data = request()->all();
        $name = $request->name;
        $barcode = $request->barcode;
        $manufacture_company = $request->manufacture_company;
        $unit_price = $request->unit_price;
        $category_id = $request->category_id;

        Product::create([
            'name'=>$name,
            'barcode'=>$barcode,
            'manufacture_company'=>$manufacture_company,
            'unit_price'=>$unit_price,
            'category_id'=>$category_id,

        ]);
        return to_route('product.create')->with('success', __('messages.added'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($productId){
        $product=Product::findOrFail($productId);
         $productCategories = ProductCategory::select('id','name')->get();

        return view('product.edit',compact('product','productCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request ,$productId){
        request()->validate([
            'name' => ['required'],
            'barcode' => ['required'],
            'manufacture_company' => ['required'],
            'unit_price' => ['required'],
            'category_id' => ['required'],

        ]);

        // $data = request()->all();
        $name = $request->name;
        $barcode = $request->barcode;
        $manufacture_company = $request->manufacture_company;
        $unit_price = $request->unit_price;
        $category_id = $request->category_id;

        $product = Product::findOrFail($productId);
        $product->update([
            'name' => $name,
            'barcode' => $barcode,
            'manufacture_company' => $manufacture_company,
            'unit_price' => $unit_price,
            'category_id' => $category_id,
        ]);
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
