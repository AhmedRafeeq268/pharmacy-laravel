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

                        <div class="col-md-3"></div>
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
