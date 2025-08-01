@extends('layouts.master')

@section('title', __('messages.purchaseReturns_details'))

@section('content')

<div class="main_content_container py-5">
    <div class="main_container  main_menu_open">

        {{-- العنوان --}}
        <div class="mb-5 text-center mt-4">
            <h1 class="display-5 fw-bold text-primary">@lang('messages.purchaseReturns_details')</h1>
            {{-- <hr class="mx-auto" style="width: 80px; border-top: 3px solid #0d6efd;"> --}}
        </div>

        {{-- جدول البيانات مع تباعد وحواف --}}
        <table class="table table-striped table-bordered align-middle" style="border-radius: 10px; overflow: hidden;">
            <tbody>
                <tr>
                    <th class="w-25 text-end bg-light">id</th>
                    <td>{{ $purchaseReturn->id }}</td>
                </tr>
                <tr>
                    <th class="text-end bg-light">@lang('messages.purchaseReturns.purchase_bill_id')</th>
                    <td>{{ $purchaseReturn->purchase_bill_id }}</td>
                </tr>
                <tr>
                    <th class="text-end bg-light">@lang('messages.purchaseReturns.supplier_name')</th>
                    <td>{{ $purchaseReturn->supplier->name }}</td>
                </tr>
                <tr>
                    <th class="text-end bg-light">@lang('messages.purchaseReturns.product_name')</th>
                    <td>{{ $purchaseReturn->product->name }}</td>
                </tr>
                <tr>
                    <th class="text-end bg-light">@lang('messages.purchaseReturns.quantity')</th>
                    <td>{{ $purchaseReturn->quantity }}</td>
                </tr>
                <tr>
                    <th class="text-end bg-light">@lang('messages.purchaseReturns.return_amount')</th>
                    <td>{{ $purchaseReturn->return_amount }}</td>
                </tr>
                <tr>
                    <th class="text-end bg-light">@lang('messages.purchaseReturns.reason')</th>
                    <td>{{ $purchaseReturn->reason }}</td>
                </tr>
                <tr>
                    <th class="text-end bg-light">@lang('messages.purchaseReturns.refunded_in_cash')</th>
                    <td>{{ $purchaseReturn->refunded_in_cash ? __('messages.yes') : __('messages.no') }}</td>
                </tr>
                <tr>
                    <th class="text-end bg-light">@lang('messages.purchaseReturns.created_by')</th>
                    <td>{{ $purchaseReturn->creator->name }}</td>
                </tr>
                <tr>
                    <th class="text-end bg-light">@lang('messages.purchaseReturns.session_id')</th>
                    <td>{{ $purchaseReturn->session_id }}</td>
                </tr>


            </tbody>
        </table>

        {{-- أزرار التحكم --}}
        <div class="d-flex justify-content-center gap-3 mt-4 flex-wrap">
            <a href="{{ route('purchaseReturns.index') }}" class="btn btn-outline-primary btn-lg px-4">
                <i class="bi bi-arrow-left"></i> @lang('messages.back_to_list')
            </a>
            <a href="{{ route('purchaseReturns.edit', $purchaseReturn->id) }}" class="btn btn-primary btn-lg px-4">

                <i class="bi bi-pencil"></i> @lang('messages.edit')
            </a>
        </div>

    </div>
</div>
@endsection
