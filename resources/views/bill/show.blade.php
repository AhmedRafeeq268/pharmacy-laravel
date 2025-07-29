@extends('layouts.master')

@section('title', __('messages.bill_details'))

@section('content')

<div class="main_content_container py-5">
    <div class="main_container main_menu_open">

        {{-- العنوان --}}
        <div class="mb-5 text-center mt-4">
            <h1 class="display-5 fw-bold text-primary">@lang('messages.bill_details')</h1>
        </div>

        {{-- جدول البيانات --}}
        <table class="table table-striped table-bordered align-middle" style="border-radius: 10px; overflow: hidden;">
            <tbody>
                <tr>
                    <th class="w-25 text-end bg-light">ID</th>
                    <td>{{ $bill->id }}</td>
                </tr>
                <tr>
                    <th class="text-end bg-light">@lang('messages.suppliers.name')</th>
                    <td>{{ $bill->supplier->name }}</td>
                </tr>

                <tr>
                    <th class="text-end bg-light">@lang('messages.bill.total_amount')</th>
                    <td>{{ $bill->total_amount }}</td>
                </tr>
                <tr>
                    <th class="text-end bg-light">@lang('messages.bill.currency_type')</th>
                    <td>{{ $bill->currancy_type }}</td>
                </tr>
                <tr>
                    <th class="text-end bg-light">@lang('messages.bill.bill_number')</th>
                    <td>{{ $bill->bill_number }}</td>
                </tr>
                <tr>
                    <th class="text-end bg-light">@lang('messages.bill.bill_date')</th>
                    <td>{{ $bill->bill_date }}</td>
                </tr>
                <tr>
                    <th class="text-end bg-light">@lang('messages.bill.receiving_employee')</th>
                    <td>{{ $bill->employee_receipt }}</td>
                </tr>
                <tr>
                    <th class="text-end bg-light">@lang('messages.bill.manufacturer')</th>
                    <td>{{ $bill->manufacturer }}</td>
                </tr>
                <tr>
                    <th class="text-end bg-light">@lang('messages.bill.adopt_bill')</th>
                    <td>{{ $bill->adopt_bill }}</td>
                </tr>
                <tr>
                    <th class="text-end bg-light">@lang('messages.bill.authorized_employee')</th>
                    <td>{{ $bill->authorized_employee }}</td>
                </tr>
                <tr>
                    <th class="text-end bg-light">@lang('messages.bill.certified_or_not')</th>
                    <td>{{ $bill->certified_or_not }}</td>
                </tr>
                <tr>
                    <th class="text-end bg-light">@lang('messages.bill.paid')</th>
                    <td>{{ $bill->paid }}</td>
                </tr>
                <tr>
                    <th class="text-end bg-light">@lang('messages.bill.remaining')</th>
                    <td>{{ $bill->remaining }}</td>
                </tr>
                <tr>
                    <th class="text-end bg-light">@lang('messages.bill.status')</th>
                    <td>{{ $bill->status }}</td>
                </tr>


            </tbody>
        </table>

        {{-- أزرار التحكم --}}
        <div class="d-flex justify-content-center gap-3 mt-4 flex-wrap">
            <a href="{{ route('bill.index') }}" class="btn btn-outline-primary btn-lg px-4">
                <i class="bi bi-arrow-left"></i> @lang('messages.back_to_list')
            </a>
            <a href="{{ route('bill.edit', $bill->id) }}" class="btn btn-primary btn-lg px-4">
                <i class="bi bi-pencil"></i> @lang('messages.edit')
            </a>
        </div>

    </div>
</div>

@endsection
