@extends('layouts.master')
@section('title', __('messages.billDetails.edit_bill_details'))

@section('content')
@include('layouts.partials.sweet_alert')
<!--Start Main content container-->
<div class="main_content_container">
    <div class="main_container main_menu_open">
        <div class="page_content">

            {{-- العنوان الرئيسي --}}
            <h3 class="heading_title text-center mb-5" style="margin-top: 90px;">

                @lang('messages.billDetails.edit_bill_details')
            </h3>
            <form method="POST" action="{{ route('billDetails.update',$billDetails->id) }}">
                @csrf
                @method('put')
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="mb-2">@lang('messages.billDetails.product_id')</label>
                        <select name="product_id" class="form-control">
                            <option value="">@lang('messages.billDetails.product_id') - @lang('messages.billDetails.product_name')</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" @selected(old('product_id', $billDetails->product_id) == $product->id)>
                                    {{ $product->id }} - {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('product_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>


                    <div class="col-md-3 mb-3">
                        <label class="mb-2">@lang('messages.billDetails.product_category')</label>
                        <select name="product_category" class="form-control">
                            <option value="">@lang('messages.billDetails.select_category')</option>
                            @foreach ($ProductCategories as $category)
                                <option value="{{ $category->name }}" {{ old('product_category',$billDetails->product_category) == $category->name ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('product_category') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="mb-2">@lang('messages.billDetails.product_description')</label>
                        <textarea name="product_data" class="form-control" rows="1" placeholder="@lang('messages.billDetails.product_description')">{{ old('product_data',$billDetails->product_data) }}</textarea>
                        @error('product_data') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="mb-2">@lang('messages.billDetails.quantity')</label>
                        <input type="number" class="form-control" name="quantity" value="{{ old('quantity',$billDetails->quantity) }}" placeholder=@lang('messages.billDetails.quantity')>
                        @error('quantity') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="mb-2">@lang('messages.billDetails.cost')</label>
                        <input type="number" class="form-control" name="cost" value="{{ old('cost',$billDetails->cost) }}" placeholder=@lang('messages.billDetails.cost')>
                        @error('cost') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="mb-2">@lang('messages.billDetails.discount')</label>
                        <input type="number" class="form-control" name="discount" value="{{ old('discount',$billDetails->discount) }}" placeholder=@lang('messages.billDetails.discount')>
                        @error('discount') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="mb-2">@lang('messages.billDetails.total')</label>
                        <input type="number" class="form-control" name="total" value="{{ old('total',$billDetails->total) }}" placeholder=@lang('messages.billDetails.total')>
                        @error('total') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="">
                        <input type="hidden" name="billId" value="{{ $billId }}">

                    </div>
                </div>

                <div class=" mt-3">
                    <button type="submit" class="btn btn-success me-2">@lang('messages.save')</button>
                </div>
            </form>


        </div>
    </div>
</div>
<!--/End Main content container-->
@endsection
