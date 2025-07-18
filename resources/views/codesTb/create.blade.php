@extends('layouts.master')
@section('title','Add CodeTb')

@section('content')
@include('layouts.partials.sweet_alert')
<!--Start Main content container-->
<div class="main_content_container">
    <div class="main_container main_menu_open">
        <div class="page_content">
            <h1 class="heading_title" style="margin-top: 90px;">@lang('messages.codesTb.add_new_constant')</h1>
            <div class="form">
                <form class="form-horizontal" method="POST" action="{{ route('codeTb.store') }}">
                    @csrf

                    {{-- السطر الأول: 4 حقول --}}
                    <div class="row">
                        <div class="col-md-2">
                            <label class="mb-2">@lang('messages.codesTb.parent_category')</label>
                            <input type="number" class="form-control" name="father" id="father_input" value="{{ old('father') }}" placeholder=@lang('messages.codesTb.parent_category')>

                            <select name="father" id="father_select" class="form-control" style="display:none;">
                                <option value="">@lang('messages.codesTb.select_parent_category')</option>
                                @foreach ($mainCodes as $code)
                                    <option value="{{ $code->main_cd }}" {{ old('father') == $code->main_cd ? 'selected' : '' }}>
                                        {{ $code->main_cd }} - {{ $code->desc_ar }}
                                    </option>
                                @endforeach
                            </select>

                            @error('father') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-2">
                            <label class="mb-2">@lang('messages.codesTb.main_category')</label><br>
                            <input type="checkbox" name="status" id="main_status_checkbox" {{ old('status') == 'on' ? 'checked' : '' }}>
                        </div>

                        <div class="col-md-4">
                            <label class="mb-2">@lang('messages.codesTb.description_ar')</label>
                            <input type="text" class="form-control" name="desc_ar" value="{{ old('desc_ar') }}" placeholder=@lang('messages.codesTb.description_ar')>
                            @error('desc_ar') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="mb-2">@lang('messages.codesTb.description_en')</label>
                            <input type="text" class="form-control" name="desc_en" value="{{ old('desc_en') }}" placeholder=@lang('messages.codesTb.description_en')>
                            @error('desc_en') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    {{-- السطر الثاني --}}
                    <div class="row mt-3">
                        <div class="col-md-5">
                            <label class="mb-2">@lang('messages.codesTb.details')</label>
                            <input type="text" class="form-control" name="details" value="{{ old('details') }}" placeholder=@lang('messages.codesTb.details')>
                            {{-- @error('details') <small class="text-danger">{{ $message }}</small> @enderror --}}
                        </div>

                        <div class="col-md-2">
                            <label class="mb-2 d-block">@lang('messages.codesTb.status')</label>
                            <button type="button" id="toggleActiveBtn" class="btn {{ old('is_active', 0) ? 'btn-success' : 'btn-secondary' }}">
                                {{ old('is_active', 0) ? __('messages.codesTb.active') : __('messages.codesTb.inactive') }}
                            </button>
                            <input type="hidden" name="is_active" id="is_active_input" value="{{ old('is_active', 0) }}">
                            @error('is_active') <small class="text-danger d-block">{{ $message }}</small> @enderror
                        </div>


                        <div class="col-md-2">
                            <label class="mb-2 d-block">@lang('messages.codesTb.editable') ?</label>
                            <button type="button" id="toggleEditableBtn" class="btn {{ old('is_editables', 0) ? 'btn-success' : 'btn-secondary' }}">
                                {{ old('is_editables', 0) ? __('messages.codesTb.editable') : __('messages.codesTb.not_editable') }}

                            </button>
                            <input type="hidden" name="is_editables" id="is_editables_input" value="{{ old('is_editables', 0) }}">
                            @error('is_editables') <small class="text-danger d-block">{{ $message }}</small> @enderror
                        </div>


                        <div class="col-md-3"></div>
                    </div>

                    {{-- الأزرار --}}
                    <div class="row mt-3">
                        <div class="col-md-12 text-dir">
                            <button type="submit" class="btn btn-success me-2">@lang('messages.save')</button>
                            <a href="{{ route('codeTb.index') }}" class="btn btn-info">@lang('messages.codesTb.show_codes_table')</a>
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
        // تفعيل/تعطيل حالة "مفعل"
        const btnActive = document.getElementById('toggleActiveBtn');
        const inputActive = document.getElementById('is_active_input');

        btnActive.addEventListener('click', function () {
            const isActive = inputActive.value === '1';
            inputActive.value = isActive ? '0' : '1';
            btnActive.classList.toggle('btn-success', !isActive);
            btnActive.classList.toggle('btn-secondary', isActive);
            btnActive.textContent = isActive ? '{{ __('messages.codesTb.active') }}' : '{{ __('messages.codesTb.inactive') }}';
        });

        // تفعيل/تعطيل "قابل للتعديل"
        const btnEditable = document.getElementById('toggleEditableBtn');
        const inputEditable = document.getElementById('is_editables_input');

        btnEditable.addEventListener('click', function () {
            const isEditable = inputEditable.value === '1';
            inputEditable.value = isEditable ? '0' : '1';
            btnEditable.classList.toggle('btn-success', !isEditable);
            btnEditable.classList.toggle('btn-secondary', isEditable);
            btnEditable.textContent = isEditable ? '{{ __('messages.codesTb.editable') }}' : '{{ __('messages.codesTb.not_editable') }}';
        });

        // التحكم بعرض حقل التصنيف الأب
        const mainStatusCheckbox = document.getElementById('main_status_checkbox');
        const fatherInput = document.getElementById('father_input');
        const fatherSelect = document.getElementById('father_select');

        function updateFatherInputState() {
            if (mainStatusCheckbox.checked) {
                // تصنيف رئيسي: اخفاء الحقلين
                fatherInput.style.display = 'none';
                fatherInput.value = '';

                fatherSelect.style.display = 'none';
                fatherSelect.value = '';
            } else {
                // تصنيف فرعي: اظهار Dropdown وإخفاء input رقم
                fatherInput.style.display = 'none';
                fatherInput.value = '';

                fatherSelect.style.display = 'block';
            }
        }

        updateFatherInputState();
        mainStatusCheckbox.addEventListener('change', updateFatherInputState);
    });
</script>
@endpush
