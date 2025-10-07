<?php

namespace App\Http\Controllers;

use App\Models\BalanceStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BalanceStoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        abort_if(Gate::denies('view-balanceStore'), 403);
         $query = BalanceStore::query();

        if ($request->filled('search')) {
            $query->where('product_name', 'like', '%' . $request->search . '%')
                ->orWhere('manufacture', 'like', '%' . $request->search . '%');
        }

        $balances = $query->orderBy('id', 'desc')->paginate(8)->appends($request->all());

        if ($request->ajax()) {
            return view('balanceStore._table', compact('balances'))->render();
        }

        return view('balanceStore.index', compact('balances'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(BalanceStore $balanceStore)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BalanceStore $balanceStore)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BalanceStore $balanceStore)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BalanceStore $balanceStore)
    {
        //
    }
}
