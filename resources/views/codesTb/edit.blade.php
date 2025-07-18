@extends('layouts.master')
@section('title','Edit CodeTb')

@section('content')
@include('layouts.partials.sweet_alert')
<!--Start Main content container-->
<div class="main_content_container">
    <div class="main_container main_menu_open">
        <div class="page_content">
            <h1 class="heading_title" style="margin-top: 90px;">@lang('messages.codesTb.edit_constant')</h1>
            <div class="form">
                <form class="form-horizontal" method="Post" action="{{ route('codeTb.update', ['codeTb' => $codeTb->id ,'page' => request()->get('page')]) }}">
                    @csrf
                    @method('put')

                    {{-- السطر الأول --}}
                    <div class="row">
                        <div class="col-md-3">
                            <label class="mb-2">main_cd</label>
                            <input type="number" class="form-control" name="main_cd" value="{{old('main_cd', $codeTb->main_cd )}}" placeholder="main_cd">
                            @error('main_cd') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="mb-2">sub_cd</label>
                            <input type="number" class="form-control" name="sub_cd" value="{{old('sub_cd', $codeTb->sub_cd) }}" placeholder="sub_cd">
                            @error('sub_cd') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.codesTb.description_ar')</label>
                            <input type="text" class="form-control" name="desc_ar" value="{{old('desc_ar',$codeTb->desc_ar) }}" placeholder="@lang('messages.codesTb.description_ar')">
                            @error('desc_ar') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.codesTb.description_en')</label>
                            <input type="text" class="form-control" name="desc_en" value="{{old('desc_en', $codeTb->desc_en )}}" placeholder="@lang('messages.codesTb.description_en')">
                            @error('desc_en') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    {{-- السطر الثاني --}}
                    <div class="row mt-3">
                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.codesTb.details')</label>
                            <input type="text" class="form-control" name="details" value="{{old('details', $codeTb->details )}}" placeholder="@lang('messages.codesTb.details')">
                            @error('details') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="mb-2 d-block">@lang('messages.codesTb.status')</label>
                            <button type="button" id="toggleActiveBtn"
                                class="btn {{ old('is_active', $codeTb->is_active ?? 0) ? 'btn-success' : 'btn-secondary' }}">
                                {{ old('is_active', $codeTb->is_active ?? 0) ? __('messages.codesTb.active') : __('messages.codesTb.inactive') }}
                            </button>
                            <input type="hidden" name="is_active" id="is_active_input"
                                value="{{ old('is_active', $codeTb->is_active ?? 0) }}">
                            @error('is_active') <small class="text-danger d-block">{{ $message }}</small> @enderror
                        </div>


                        <div class="col-md-3">
                            <label class="mb-2 d-block">@lang('messages.codesTb.editable')؟</label>
                            <button type="button" id="toggleEditableBtn"
                                class="btn {{ old('is_editables', $codeTb->is_editables ?? 0) ? 'btn-success' : 'btn-secondary' }}">
                                {{ old('is_editables', $codeTb->is_editables ?? 0) ? __('messages.codesTb.editable') : __('messages.codesTb.not_editable') }}
                            </button>
                            <input type="hidden" name="is_editables" id="is_editables_input"
                                value="{{ old('is_editables', $codeTb->is_editables ?? 0) }}">
                            @error('is_editables') <small class="text-danger d-block">{{ $message }}</small> @enderror
                        </div>


                        <div class="col-md-3"></div>
                    </div>

                    {{-- زر الحفظ --}}
                    <div class="row mt-3">
                        <div class="col-md-12 text-dir">
                            <button type="submit" class="btn btn-danger me-2">@lang('messages.save')</button>
                            <a href="{{ route('codeTb.index') }}" class="btn btn-secondary">@lang('messages.back_to_list')</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--/End Main content container-->
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const toggleBtn = document.getElementById('toggleActiveBtn');
            const input = document.getElementById('is_active_input');

            toggleBtn.addEventListener('click', function () {
                const isActive = input.value === '1';
                if (isActive) {
                    input.value = '0';
                    toggleBtn.classList.remove('btn-success');
                    toggleBtn.classList.add('btn-secondary');
                    toggleBtn.textContent = 'غير مفعل';
                } else {
                    input.value = '1';
                    toggleBtn.classList.remove('btn-secondary');
                    toggleBtn.classList.add('btn-success');
                    toggleBtn.textContent = 'مفعل';
                }
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const editableBtn = document.getElementById('toggleEditableBtn');
            const editableInput = document.getElementById('is_editables_input');

            editableBtn.addEventListener('click', function () {
                const isEditable = editableInput.value === '1';
                if (isEditable) {
                    editableInput.value = '0';
                    editableBtn.classList.remove('btn-success');
                    editableBtn.classList.add('btn-secondary');
                    editableBtn.textContent = 'غير قابل';
                } else {
                    editableInput.value = '1';
                    editableBtn.classList.remove('btn-secondary');
                    editableBtn.classList.add('btn-success');
                    editableBtn.textContent = 'قابل للتعديل';
                }
            });
        });
    </script>

@endpush
