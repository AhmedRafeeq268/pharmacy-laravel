@extends('layouts.master')
@section('title', __('messages.customer.add_new_customer'))

@section('content')
@include('layouts.partials.sweet_alert')
<!--Start Main content container-->
<div class="main_content_container">
    <div class="main_container main_menu_open">
        <div class="page_content">
            <h1 class="heading_title" style="margin-top: 90px;">@lang('messages.customer.add_new_customer')</h1>
            <div class="form">
                <form class="form-horizontal" method="Post" action="{{ route('customer.store') }}">
                    @csrf

                    {{-- السطر الأول: الاسم، الهاتف، الإيميل، العنوان --}}
                    <div class="row ">
                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.customer.customer_name')</label>
                            <input type="text" class="form-control" name="name" value="{{ old('name') }}" placeholder="@lang('messages.customer.customer_name')">
                            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.customer.phone_number')</label>
                            <input type="number" class="form-control" name="phone" value="{{ old('phone') }}" placeholder="@lang('messages.customer.phone_number')">
                            @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.customer.email')</label>
                            <input type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="@lang('messages.customer.email')">
                            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.customer.address')</label>
                            <input type="text" class="form-control" name="address" value="{{ old('address') }}" placeholder="@lang('messages.customer.address')">
                            @error('address') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    {{-- السطر الثاني: الهوية، تفاصيل العنوان --}}
                    <div class="row mt-3 ">
                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.customer.id_number')</label>
                            <input type="number" class="form-control" name="id_card" value="{{ old('id_card') }}" placeholder="@lang('messages.customer.id_number')">
                            @error('id_card') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.customer.address_details')</label>
                            <input type="text" class="form-control" name="address_details" value="{{ old('address_details') }}" placeholder="@lang('messages.customer.address_details')">
                            @error('address_details') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                            {{-- @php
                                $descField = app()->getLocale() === 'ar' ? 'desc_ar' : 'desc_en';
                            @endphp --}}
                        {{-- <div class="col-md-3">
                            <label class="mb-2">@lang('messages.customer.status')</label>
                            <select name="status_cd" class="form-control">
                                <option value="" disabled {{ old('status_cd') ? '' : 'selected' }}>@lang('messages.customer.status')</option>
                                @foreach ($status as $item)
                                    <option value="{{ $item->sub_cd }}" {{ old('status_cd') == $item->sub_cd ? 'selected' : '' }}>
                                        {{ $item->$descField }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status_cd') <small class="text-danger">{{ $message }}</small> @enderror
                        </div> --}}
                        <div class="col-md-2">
                            <label class="mb-2 d-block">@lang('messages.customer.status')</label>
                            <button type="button" id="toggleActiveBtn" class="btn {{ old('status_cd', 0) ? 'btn-success' : 'btn-secondary' }}">
                                {{ old('status_cd', 0) ? __('messages.codesTb.active') : __('messages.codesTb.inactive') }}
                            </button>
                            <input type="hidden" name="status_cd" id="is_active_input" value="{{ old('is_active', 0) }}">
                            @error('status_cd') <small class="text-danger d-block">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3"></div>
                    </div>

                    {{-- الأزرار --}}
                    <div class="row mt-2">
                        <div class="col-md-12 text-dir">
                            <button type="submit" class="btn btn-success me-2">@lang('messages.save')</button>
                            <a href="{{ route('customer.index') }}" class="btn btn-info">@lang('messages.customer.show_customers')</a>
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
            btnActive.classList.toggle('btn-secondary', !isActive);
            btnActive.classList.toggle('btn-success', isActive);
            btnActive.textContent = isActive ? '{{ __('messages.codesTb.active') }}' : '{{ __('messages.codesTb.inactive') }}';
        });
    });

</script>

@endpush
