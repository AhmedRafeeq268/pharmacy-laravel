@extends('layouts.master')

@section('title',__('messages.sales_return_details'))

@section('content')

<div class="main_content_container py-5">
    <div class="main_container  main_menu_open">

        {{-- العنوان --}}
        <div class="mb-5 text-center mt-4">
            <h1 class="display-5 fw-bold text-primary">@lang('messages.sales_return_details')</h1></h1>
            {{-- <hr class="mx-auto" style="width: 80px; border-top: 3px solid #0d6efd;"> --}}
        </div>

        {{-- جدول البيانات مع تباعد وحواف --}}
        <table class="table table-striped table-bordered align-middle" style="border-radius: 10px; overflow: hidden;">
            <tbody>
                <tr>
                    <th class="w-25 text-end bg-light">id</th>
                    <td>{{ $salesReturn->id }}</td>
                </tr>
                <tr>
                    <th class="text-end bg-light">@lang('messages.salesReturn.pos_bill_number')</th>
                    <td>{{ $salesReturn->pos_bill_id }}</td>
                </tr>
                <tr>
                    <th class="text-end bg-light">@lang('messages.salesReturn.customer_name')</th>
                    <td>{{ $salesReturn->customer ? $salesReturn->customer->name : '-' }}</td>
                </tr>
                <tr>
                    <th class="text-end bg-light">@lang('messages.salesReturn.total')</th>
                    <td>{{ $salesReturn->total }}</td>
                </tr>
                <tr>
                    <th class="text-end bg-light">@lang('messages.salesReturn.refund_method')</th>
                    <td>{{ $salesReturn->refund_method }}</td>
                </tr>
                @foreach($salesReturn->details as $detail)
                    <tr>
                        <th class="text-end bg-light">@lang('messages.salesReturn.product_name')</th>
                            <td>
                                {{ optional($detail->product)->name }}<br>
                            </td>
                        </tr>

                        <tr>
                            <th class="text-end bg-light">@lang('messages.salesReturn.price')</th>
                            <td>{{ $detail->price }}</td>
                        </tr>
                        <tr>
                            <th class="text-end bg-light">@lang('messages.salesReturn.quantity')</th>
                            <td>{{ $detail->quantity }}</td>
                        </tr>
                        <tr>
                            <th class="text-end bg-light">@lang('messages.salesReturn.subtotal')</th>
                            <td>{{ $detail->subtotal }}</td>
                    </tr>
                @endforeach


            </tbody>
        </table>

        {{-- أزرار التحكم --}}
        <div class="d-flex justify-content-center gap-3 mt-4 flex-wrap">
            <a href="{{ route('salesReturn.index') }}" class="btn btn-outline-primary btn-lg px-4">
                <i class="bi bi-arrow-left"></i> @lang('messages.back_to_list')
            </a>
        </div>

    </div>
</div>
@endsection
