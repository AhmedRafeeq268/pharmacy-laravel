<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     $products=Product::orderBy('id', 'desc')->paginate(8);
    //     return view('product.index',['products'=>$products]);
    // }

    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->with('ProductCategory')->orderBy('id')->paginate(8)->appends($request->all());

        if ($request->ajax()) {
            return view('product._table', compact('products'))->render();
        }

        return view('product.index', compact('products'));
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
