<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     $productCategorys=ProductCategory::orderBy('id', 'desc')->paginate(8);
    //     return view('productCategory.index',['productCategorys'=>$productCategorys]);
    // }

    public function index(Request $request)
    {
        $query = ProductCategory::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $productCategorys = $query->orderBy('id')->paginate(8)->appends($request->all());

        if ($request->ajax()) {
            return view('productCategory._table', compact('productCategorys'))->render();
        }

        return view('productCategory.index', compact('productCategorys'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('productCategory.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(){
        request()->validate([
            'name' => ['required'],
            'description' => ['required'],

        ]);

        // $data = request()->all();
        $name = request()->name;
        $description = request()->description;

        ProductCategory::create([
            'name'=>$name,
            'description'=>$description,

        ]);
        return to_route('productCategory.create')->with('success', __('messages.added'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id){
        $productCategory = ProductCategory::findOrFail($id);
        return view('productCategory.show',compact('productCategory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($productCategoryId){
        $singleProductCategoryFromDB=ProductCategory::findOrFail($productCategoryId);
        return view('productCategory.edit',['productCategory'=>$singleProductCategoryFromDB]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($productCategoryId){
        request()->validate([
            'name' => ['required'],
            'description' => ['required'],

        ]);

        // $data = request()->all();
        $name = request()->name;
        $description = request()->description;

        $productCategory = productCategory::findOrFail($productCategoryId);
        $productCategory->update([
            'name' => $name,
            'description' => $description,
        ]);
        $page = request()->get('page', 1);
        return to_route('productCategory.index',['page' => $page])
        ->with('success', __('messages.updated'));
    }

    public function destroy($productCategoryId){
        $productCategory = ProductCategory::find($productCategoryId);
        if (!$productCategory)
        {
            return redirect()->back()->with('error', __('messages.not_found'));
        }
        $productCategory->delete();
        $page = request()->get('page', 1);
        return to_route('productCategory.index',['page' => $page])
        ->with('success', __('messages.deleted'));
    }
}
