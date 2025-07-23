<?php

namespace App\Http\Controllers;

use App\Models\CodesTb;
use Illuminate\Http\Request;

class CodesTbController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index(){
    //     $codesTb=CodesTb::orderBy('id', 'desc')->paginate(8);
    //     return view('codesTb.index',['codesTb'=>$codesTb]);
    // }
    public function index(Request $request)
    {
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
         $mainCodes = CodesTb::where('sub_cd', 0)->get();
        return view('codesTb.create',compact('mainCodes'));
    }

    /**
     * Store a newly created resource in storage.
     */
public function store(Request $request)
{
    $request->validate([
        'status' => ['nullable'],
        'father' => [$request->has('status') ? 'nullable' : 'required', 'numeric'],
        'desc_ar' => ['required'],
        'desc_en' => ['required'],
        'is_active' => ['required'],
        'is_editables' => ['required'],
    ]);

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

    return to_route('codeTb.create')
        ->with('success', __('messages.added'));
}


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $codeTb=CodesTb::findOrFail($id);
        return view('codesTb.show',compact('codeTb'));

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id){
        $codeTb=CodesTb::findOrFail($id);
        return view('codesTb.edit',compact('codeTb'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $codeTb)
{
    $request->validate([
        'main_cd' => ['required'],
        'sub_cd' => ['required'],
        'desc_ar' => ['required'],
        'desc_en' => ['required'],
        'details' => ['required'],
        'is_active' => ['required'],
        'is_editables' => ['required'],
    ]);

    $codeTbModel = CodesTb::findOrFail($codeTb);

    $codeTbModel->update([
        'main_cd' => $request->main_cd,
        'sub_cd' => $request->sub_cd,
        'desc_ar' => $request->desc_ar,
        'desc_en' => $request->desc_en,
        'details' => $request->details,
        'is_active' => $request->is_active,
        'is_editables' => $request->is_editables,
    ]);

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
