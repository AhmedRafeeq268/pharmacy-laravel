@extends('layouts.master')
@section('title', __('messages.bill.edit_bill'))

@section('content')
@include('layouts.partials.sweet_alert')
<!--Start Main content container-->
<div class="main_content_container">
    <div class="main_container main_menu_open">
        <div class="page_content">
            <h1 class="heading_title" style="margin-top: 90px;">@lang('messages.bill.edit_bill')</h1>
            <div class="form">
                <form class="form-horizontal" method="POST" action="{{ route('bill.update', ['bill' => $bill->id, 'page' => request()->get('page')]) }}">
                    @csrf
                    @method('put')
                    <div class="row">
                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.bill.total_amount')</label>
                            <input type="number" class="form-control" name="total_amount"
                                   value="{{ old('total_amount', $bill->total_amount) }}"
                                   placeholder="@lang('messages.bill.total_amount')">
                            @error('total_amount') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.bill.currency_type')</label>
                            <select name="currancy_type" class="form-control">
                                <option value="">@lang('messages.bill.currency_type')</option>
                                @foreach ($currancies as $currancy)
                                    <option value="{{ $currancy->desc_en }}"
                                        {{ old('currancy_type', $bill->currancy_type) == $currancy->desc_en ? 'selected' : '' }}>
                                        {{ $currancy->desc_en }} - {{ $currancy->desc_ar }}
                                    </option>
                                @endforeach
                            </select>
                            @error('currancy_type') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.bill.bill_number')</label>
                            <input type="number" class="form-control" name="bill_number"
                                   value="{{ old('bill_number', $bill->bill_number) }}"
                                   placeholder="@lang('messages.bill.bill_number')">
                            @error('bill_number') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.bill.bill_date')</label>
                            <input type="date" class="form-control" name="bill_date"
                                value="{{ old('bill_date', date('Y-m-d', strtotime($bill->bill_date ?? now()))) }}"
                                placeholder="@lang('messages.bill.bill_date')">
                            @error('bill_date')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.bill.receiving_employee')</label>
                            <select name="employee_receipt" class="form-control">
                                <option value="">@lang('messages.bill.select_employee')</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->name }}"
                                        {{ old('employee_receipt', $bill->employee_receipt) == $employee->name ? 'selected' : '' }}>
                                        {{ $employee->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('employee_receipt') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-9"></div>
                    </div>

                    {{-- الأزرار --}}
                    <div class="row mt-2">
                        <div class="col-md-12 text-dir">
                            <button type="submit" class="btn btn-danger me-2">@lang('messages.save')</button>
                            <a href="{{ route('bill.index', ['page' => request()->get('page', 1)]) }}" class="btn btn-info">@lang('messages.back_to_list')</a>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
<!--/End Main content container-->
@endsection
