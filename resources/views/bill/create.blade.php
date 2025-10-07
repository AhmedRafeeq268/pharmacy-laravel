@extends('layouts.master')
@section('title', __('messages.bill.add_new_bill'))

@section('content')
@include('layouts.partials.sweet_alert')

<div class="main_content_container">
    <div class="main_container main_menu_open">
        <div class="page_content">

            <h1 class="heading_title mt-4 mb-4">@lang('messages.bill.add_new_bill')</h1>

            <div class="card shadow-sm">
                <div class="card-body">

                    <form method="POST" action="{{ route('bill.store') }}">
                        @csrf

                        {{-- القسم الأول: معلومات الفاتورة --}}
                        <h5 class="mb-3">@lang('messages.bill.bill_info')</h5>
                        <div class="row g-3 mb-4">

                            <div class="col-md-3">
                                <label class="form-label">@lang('messages.bill.total_amount')</label>
                                <input type="number" class="form-control" name="total_amount" value="{{ old('total_amount') }}" placeholder="@lang('messages.bill.total_amount')">
                                @error('total_amount') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">@lang('messages.bill.currency_type')</label>
                                <select name="currancy_type" class="form-control">
                                    <option value="">@lang('messages.bill.currency_type')</option>
                                    @foreach ($currancies as $currancy)
                                        <option value="{{ $currancy->desc_en }}" {{ old('currancy_type') == $currancy->desc_en ? 'selected' : '' }}>
                                            {{ $currancy->desc_en }} - {{ $currancy->desc_ar }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('currancy_type') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">@lang('messages.bill.bill_date')</label>
                                <input type="date" class="form-control" name="bill_date" value="{{ old('bill_date') }}">
                                @error('bill_date') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">@lang('messages.bill.receiving_employee')</label>
                                <select name="employee_receipt" class="form-control">
                                    <option value="">@lang('messages.bill.select_employee')</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->name }}" {{ old('employee_receipt') == $employee->name ? 'selected' : '' }}>
                                            {{ $employee->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('employee_receipt') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                        </div>

                        {{-- القسم الثاني: معلومات المورد والمنتج --}}
                        <h5 class="mb-3">@lang('messages.bill.supplier_info')</h5>
                        <div class="row g-3 mb-4">

                            <div class="col-md-3">
                                <label class="form-label">@lang('messages.bill.manufacturer')</label>
                                <select name="manufacturer" class="form-control">
                                    <option value="">@lang('messages.bill.manufacturer')</option>
                                    @foreach ($manufacturers as $manufacturer)
                                        <option value="{{ $manufacturer->desc_en }}" {{ old('manufacturer') == $manufacturer->desc_en ? 'selected' : '' }}>
                                            {{ $manufacturer->desc_en }} - {{ $manufacturer->desc_ar }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">@lang('messages.suppliers.name')</label>
                                <select name="supplier_id" class="form-control">
                                    <option value="">@lang('messages.suppliers.name')</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">@lang('messages.bill.paid')</label>
                                <input type="number" name="paid" class="form-control" placeholder="@lang('messages.bill.paid')">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">@lang('messages.bill.status')</label>
                                <select name="status" class="form-control">
                                    <option value="paid">@lang('messages.bill.status_paid')</option>
                                    <option value="partial">@lang('messages.bill.status_partial')</option>
                                    <option value="unpaid" selected>@lang('messages.bill.status_unpaid')</option>
                                </select>
                            </div>

                        </div>

                        {{-- الأزرار --}}
                        <div class="mt-4">
                            <button type="submit" class="btn btn-success me-2">@lang('messages.save')</button>
                            <a href="{{ route('bill.index') }}" class="btn btn-outline-info">@lang('messages.bill.view_bills')</a>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
