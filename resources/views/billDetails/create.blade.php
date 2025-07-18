@extends('layouts.master')
@section('title', __('messages.billDetails.add_bill_details'))

@section('content')
@include('layouts.partials.sweet_alert')
<!--Start Main content container-->
<div class="main_content_container">
    <div class="main_container main_menu_open">
        <div class="page_content">

            {{-- العنوان الرئيسي --}}
            <h3 class="heading_title text-center mb-4" style="margin-top: 90px;">

                @lang('messages.billDetails.add_bill_details')
            </h3>

            {{-- معلومات الفاتورة --}}
            <h4 class="text-center text-primary mb-3">@lang('messages.billDetails.bill_info')</h4>
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover text-center">
                    <thead class="table-light">
                        <tr>
                            <th>@lang('messages.bill.total_amount')</th>
                            <th>@lang('messages.bill.currency_type')</th>
                            <th>@lang('messages.bill.bill_number')</th>
                            <th>@lang('messages.bill.bill_date')</th>
                            <th>@lang('messages.bill.receiving_employee')</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bills as $bill)
                            <tr>
                                <td>{{ $bill->total_amount }}</td>
                                <td>{{ $bill->currancy_type }}</td>
                                <td>{{ $bill->bill_number }}</td>
                                <td>{{ date('d-m-Y', strtotime($bill->bill_date))}}</td>
                                <td>{{ $bill->employee_receipt }}</td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- نموذج إدخال تفاصيل المنتج --}}
            <h4 class="text-center text-success mt-5 mb-4">@lang('messages.billDetails.add_bill_details')</h4>
            <form method="POST" action="{{ route('billDetails.store', ['billId' => request()->route('billId')]) }}">
                @csrf

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="mb-2">@lang('messages.billDetails.product_id')</label>
                        <select name="product_id" class="form-control">
                            <option value="">@lang('messages.billDetails.product_id') - @lang('messages.billDetails.product_name')</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->id }} - {{ $product->name }}
                                </option>
                            @endforeach
                        </select>

                        {{-- <input type="text" class="form-control" name="product_name" value="{{ old('product_name') }}" placeholder="@lang('messages.billDetails.product_name')"> --}}
                        @error('product_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="mb-2">@lang('messages.billDetails.product_category')</label>
                        <select name="product_category" class="form-control">
                            <option value="">@lang('messages.billDetails.select_category')</option>
                            @foreach ($ProductCategories as $category)
                                <option value="{{ $category->name }}" {{ old('product_category') == $category->name ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('product_category') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="mb-2">@lang('messages.billDetails.product_description')</label>
                        <textarea name="product_data" class="form-control" rows="1" placeholder="@lang('messages.billDetails.product_description')">{{ old('product_data') }}</textarea>
                        @error('product_data') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="mb-2">@lang('messages.billDetails.quantity')</label>
                        <input type="number" class="form-control" name="quantity" value="{{ old('quantity') }}" placeholder="@lang('messages.billDetails.quantity')">
                        @error('quantity') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="mb-2">@lang('messages.billDetails.cost')</label>
                        <input type="number" class="form-control" name="cost" value="{{ old('cost') }}" placeholder="@lang('messages.billDetails.cost')">
                        @error('cost') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="mb-2">@lang('messages.billDetails.discount')</label>
                        <input type="number" class="form-control" name="discount" value="{{ old('discount') }}" placeholder="@lang('messages.billDetails.discount')">
                        @error('discount') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="mb-2">@lang('messages.billDetails.total')</label>
                        <input type="number" class="form-control" name="total" value="{{ old('total') }}" placeholder="@lang('messages.billDetails.total')">
                        @error('total') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="">
                        <input type="hidden" name="billId" value="{{ $billId }}">

                    </div>
                </div>

                <div class=" mt-3">
                    <button type="submit" class="btn btn-success me-2">@lang('messages.save')</button>
                    <a href="{{ route('bill.create') }}" class="btn btn-info">@lang('messages.billDetails.finished_entry')</a>
                </div>
            </form>

            {{-- جدول تفاصيل الفاتورة --}}
            <h4 class="text-center text-dark mt-5 mb-3">@lang('messages.billDetails.bill_items_list')</h4>
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover text-center">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>@lang('messages.billDetails.bill_id')</th>
                            <th>@lang('messages.billDetails.product_name')</th>
                            <th>@lang('messages.billDetails.product_category')</th>
                            <th>@lang('messages.billDetails.product_description')</th>
                            <th>@lang('messages.billDetails.quantity')</th>
                            <th>@lang('messages.billDetails.cost')</th>
                            <th>@lang('messages.billDetails.total')</th>
                            <th>@lang('messages.billDetails.discount')</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $id = 1; @endphp
                        @foreach ($billDetails as $billDetail)
                            <tr>
                                <td>{{ $id++ }}</td>
                                <td>{{ $billDetail->bill_id }}</td>
                                <td>{{ $billDetail->product_name }}</td>
                                <td>{{ $billDetail->product_category }}</td>
                                <td>{{ $billDetail->product_data }}</td>
                                <td>{{ $billDetail->quantity }}</td>
                                <td>{{ $billDetail->cost }}</td>
                                <td>{{ $billDetail->total }}</td>
                                <td>{{ $billDetail->discount }}</td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="#" class="btn btn-info btn-sm">@lang('messages.view')</a>
                                        <a href="{{ route('billDetails.edit',$billDetail->id) }}" class="btn btn-primary btn-sm">@lang('messages.edit')</a>
                                        <form action="{{ route('billDetails.destroy',$billDetail->id) }}" method="POST" onsubmit="return confirm('@lang('messages.confirm_delete')')" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">@lang('messages.delete')</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
<!--/End Main content container-->
@endsection
