@extends('layouts.master')
@section('title', __('messages.damaged.edit_damage_item'))

@section('content')
@include('layouts.partials.sweet_alert')
<!--Start Main content container-->
<div class="main_content_container">
    <div class="main_container main_menu_open">
        <div class="page_content">
            <h1 class="heading_title" style="margin-top: 90px;">@lang('messages.damaged.record_damage_item')</h1>
            <div class="form">
                <form class="form-horizontal" method="POST" action="{{ route('damaged.update',['damagedItem'=>$damagedItem->id ,'page' => request()->get('page')]) }}">
                    @csrf
                    @method('put')

                    <div class="row">
                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.damaged.product')</label>
                            <select name="product_id" class="form-control">
                                <option value="" disabled >
                                    @lang('messages.product.product_category')
                                </option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}"
                                            {{ old('product_id', $damagedItem->product_id) == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('product_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="mb-2">@lang('messages.damaged.damaged_quantity')</label>
                            <input type="number" name="quantity" class="form-control" value="{{ old('quantity',$damagedItem->quantity) }}">
                        </div>

                        <div class="col-md-4">
                            <label class="mb-2">@lang('messages.damaged.damage_reason')</label>
                            <textarea name="reason" class="form-control" rows="1" placeholder="@lang('messages.damaged.damage_reason')">{{ old('reason',$damagedItem->reason) }}</textarea>
                         </div>
                    </div>

                    <div class="d-flex gap-3 mt-1 flex-wrap">
                        <button type="submit" class="btn btn-danger px-4 mt-3">
                            @lang('messages.damaged.save_damaged_item')
                        </button>
                        <a href="{{ route('damaged.index') }}" class="btn btn-outline-primary px-4 mt-3">
                            <i class="bi bi-arrow-left"></i> @lang('messages.back_to_list')
                        </a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection
