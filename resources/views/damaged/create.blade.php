@extends('layouts.master')
@section('title', __('messages.damaged.record_damage_item'))

@section('content')
@include('layouts.partials.sweet_alert')
<!--Start Main content container-->
<div class="main_content_container">
    <div class="main_container main_menu_open">
        <div class="page_content">
            <h1 class="heading_title" style="margin-top: 90px;">@lang('messages.damaged.record_damage_item')</h1>
            <div class="form">
                <form class="form-horizontal" method="POST" action="{{ route('damaged.store') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <label class="mb-2">@lang('messages.damaged.product')</label>
                            <select name="product_id" class="form-control">
                                <option value="">@lang('messages.damaged.select_product')</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }} (@lang('messages.damaged.available') {{ $product->quantity }})
                                    </option>
                                @endforeach
                            </select>
                            @error('product_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="mb-2">@lang('messages.damaged.damaged_quantity')</label>
                            <input type="number" name="quantity" class="form-control" value="{{ old('quantity') }}" placeholder="@lang('messages.damaged.damaged_quantity')">
                        </div>

                        <div class="col-md-4">
                            <label class="mb-2">@lang('messages.damaged.damage_reason')</label>
                            <textarea name="reason" class="form-control" rows="1" placeholder="@lang('messages.damaged.damage_reason')">{{ old('reason') }}</textarea>
                         </div>
                    </div>

                    <button type="submit" class="btn btn-danger mt-3">@lang('messages.damaged.save_damaged_item')</button>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection
