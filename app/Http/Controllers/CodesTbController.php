<?php

namespace App\Http\Controllers;

use App\Models\CodesTb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\StoreCodesTbRequest;
use App\Http\Requests\UpdateCodesTbRequest;

class CodesTbController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('view-codesTb'), 403);
        $query = CodesTb::query();

        if ($request->filled('search')) {
            $query->where('desc_ar', 'like', '%' . $request->search . '%')
                ->orWhere('desc_en', 'like', '%' . $request->search . '%');
        }

        $codesTb = $query->orderBy('id', 'desc')->paginate(8)->appends($request->all());

        if ($request->ajax()) {
            return view('codesTb._table', compact('codesTb'))->render();
        }

        return view('codesTb.index', compact('codesTb'));
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
         abort_if(Gate::denies('create-codesTb'), 403);
         $mainCodes = CodesTb::where('sub_cd', 0)->get();
        return view('codesTb.create',compact('mainCodes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCodesTbRequest $request)
    {
        $details = $request->input('details', ' '); // القيمة الافتراضية إذا لم توجد

        if ($request->has('status')) {
            $newMainCd = CodesTb::max('main_cd') + 1;

            CodesTb::create([
                'main_cd' => $newMainCd,
                'sub_cd' => 0,
                'desc_ar' => $request->desc_ar,
                'desc_en' => $request->desc_en,
                'details' => $details,
                'is_active' => $request->is_active,
                'is_editables' => $request->is_editables,
            ]);
        } else {
            $mainCd = $request->father;
            $newSubCd = CodesTb::where('main_cd', $mainCd)->max('sub_cd') + 1;

            CodesTb::create([
                'main_cd' => $mainCd,
                'sub_cd' => $newSubCd,
                'desc_ar' => $request->desc_ar,
                'desc_en' => $request->desc_en,
                'details' => $details,
                'is_active' => $request->is_active,
                'is_editables' => $request->is_editables,
            ]);
        }

        return to_route('codeTb.index')
            ->with('success', __('messages.added'));
    }



    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        abort_if(Gate::denies('view-codesTb'), 403);
        $codeTb=CodesTb::findOrFail($id);
        return view('codesTb.show',compact('codeTb'));

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id){
        abort_if(Gate::denies('edit-codesTb'), 403);
        $codeTb=CodesTb::findOrFail($id);
        return view('codesTb.edit',compact('codeTb'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCodesTbRequest $request, $codeTb)
    {
        $codeTbModel = CodesTb::findOrFail($codeTb);
        $codeTbModel->update($request->validated());

        $page = $request->get('page', 1);
        return to_route('codeTb.index', ['page' => $page])->with('success', __('messages.updated'));;
    }


    /**
     * Remove the specified resource from storage.
     */
        public function destroy(Request $request, $codeTbId)
        {
            $codeTb = CodesTb::find($codeTbId);

            if (!$codeTb) {
                return redirect()->back()->with('error', __('messages.not_found'));
            }

            $codeTb->delete();

            $page = $request->get('page', 1);
            return to_route('codeTb.index', ['page' => $page])
                ->with('success', __('messages.deleted'));
        }

}
