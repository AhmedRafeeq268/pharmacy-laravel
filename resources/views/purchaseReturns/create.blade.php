@extends('layouts.master')

@section('title', __('messages.purchaseReturns.return_products_to_supplier'))


@section('content')
@include('layouts.partials.sweet_alert')
<div class="main_content_container">
    <div class="main_container main_menu_open">
        <div class="page_content">
            <h1 class="heading_title" style="margin-top: 90px; width:300px;">
                 @lang('messages.purchaseReturns.return_products_to_supplier')
            </h1>
        <!-- اختيار الفاتورة -->
        <form method="GET" action="{{ route('purchaseReturns.create') }}" class="mb-4">
            <div class="col-md-3">
                <label class="mb-2">@lang('messages.purchaseReturns.select_purchase_bill')</label>
                <select name="bill_id" class="form-control" onchange="this.form.submit()">
                    <option value="">@lang('messages.purchaseReturns.select_purchase_bill')</option>
                    @foreach ($bills as $b)
                        <option value="{{ $b->id }}" {{ (isset($bill) && $bill->id == $b->id) ? 'selected' : '' }}>
                            {{ __('messages.purchaseReturns.select_bill_option', ['id' => $b->id, 'name' => $b->supplier->name]) }}
                        </option>

                    @endforeach
                </select>
                @error('bill_id') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
        </form>

        @if($bill)
        <form method="POST" action="{{ route('purchaseReturns.store') }}">
            @csrf
            <input type="hidden" name="purchase_bill_id" value="{{ $bill->id }}">
            <input type="hidden" name="supplier_id" value="{{ $bill->supplier->id }}">

            <table class="table table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>@lang('messages.purchaseReturns.product')</th>
                        <th>@lang('messages.purchaseReturns.original_quantity')</th>
                        <th>@lang('messages.purchaseReturns.returned_quantity')</th>
                        <th>@lang('messages.purchaseReturns.returned_amount') (₪)</th>
                        <th>@lang('messages.purchaseReturns.reason')</th>
                        <th>@lang('messages.purchaseReturns.cash_refund')</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bill->details as $index => $detail)
                    <tr>
                        <td>
                            {{ $detail->product->name }}
                            <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $detail->product->id }}">
                        </td>
                        <td>{{ $detail->quantity }}</td>
                        <td>
                            <input
                                type="number"
                                name="items[{{ $index }}][quantity]"
                                class="form-control"
                                min="0" max="{{ $detail->quantity }}" required value="{{ old('items.' . $index . '.quantity') }}">
                        </td>
                        <td>
                            <input
                                type="number"
                                name="items[{{ $index }}][return_amount]"
                                class="form-control"
                                step="0.01"
                                min="0"
                                required
                                value="{{ old('items.' . $index . '.return_amount') }}"
                            >
                        </td>
                        <td>
                            <input
                                type="text"
                                name="items[{{ $index }}][reason]"
                                class="form-control"
                                value="{{ old('items.' . $index . '.reason') }}"
                            >
                        </td>
                        <td>
                            <select name="items[{{ $index }}][refunded_in_cash]" class="form-control" required>
                                <option value="0" {{ old('items.' . $index . '.refunded_in_cash') == '0' ? 'selected' : '' }}>لا</option>
                                <option value="1" {{ old('items.' . $index . '.refunded_in_cash') == '1' ? 'selected' : '' }}>نعم</option>
                            </select>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <button type="submit" class="btn btn-danger btn-block">@lang('messages.purchaseReturns.save_returns')</button>
            <a href="{{ route('purchaseReturns.index') }}" class="btn btn-outline-info">
                                @lang('messages.purchaseReturns.view_purchase_returns') </a>
        </form>
        @endif
    </div>
    </div>
    </div>
@endsection
