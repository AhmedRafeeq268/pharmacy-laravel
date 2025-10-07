<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\StoreProductCategoryRequest;
use App\Http\Requests\UpdateProductCategoryRequest;

class ProductCategoryController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('view-productCategory'), 403);
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
        abort_if(Gate::denies('create-productCategory'), 403);
        return view('productCategory.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductCategoryRequest $request){
        ProductCategory::create($request->validated());
        return to_route('productCategory.create')->with('success', __('messages.added'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id){
        abort_if(Gate::denies('view-productCategory'), 403);
        $productCategory = ProductCategory::findOrFail($id);
        return view('productCategory.show',compact('productCategory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($productCategoryId){
        abort_if(Gate::denies('edit-productCategory'), 403);
        $singleProductCategoryFromDB=ProductCategory::findOrFail($productCategoryId);
        return view('productCategory.edit',['productCategory'=>$singleProductCategoryFromDB]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductCategoryRequest $request,$productCategoryId){

        $productCategory = productCategory::findOrFail($productCategoryId);
        $productCategory::update($request->validated());

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
