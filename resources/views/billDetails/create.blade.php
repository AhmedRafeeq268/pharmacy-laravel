@extends('layouts.master')
@section('title', __('messages.billDetails.add_bill_details'))

@section('content')
@include('layouts.partials.sweet_alert')
<div class="main_content_container">
    <div class="main_container main_menu_open">
        <div class="page_content">

            <h3 class="heading_title text-center mb-4" style="margin-top: 90px;">
                @lang('messages.billDetails.add_bill_details')
            </h3>

            {{-- معلومات الفاتورة --}}
            <h4 class="text-center text-primary mb-3">@lang('messages.billDetails.bill_info')</h4>
            <div class="table-responsive">
                <table class="table table-bordered table-striped text-center">
                    <thead>
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
                            <td>{{ date('d-m-Y', strtotime($bill->bill_date)) }}</td>
                            <td>{{ $bill->employee_receipt }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- نموذج إدخال تفاصيل المنتجات --}}
            <h4 class="text-center text-success mt-5 mb-4">@lang('messages.billDetails.add_bill_details')</h4>
            <form method="POST" action="{{ route('billDetails.store', ['billId' => $billId]) }}">
                @csrf
                <input type="hidden" name="billId" value="{{ $billId }}">

                <div class="table-responsive">
                    <table class="table table-bordered text-center" id="itemsTable">
                        <thead class="table-light">
                            <tr>
                                <th>@lang('messages.billDetails.product_id')</th>
                                {{-- <th>@lang('messages.billDetails.product_category')</th> --}}
                                <th>@lang('messages.billDetails.quantity')</th>
                                <th>@lang('messages.billDetails.cost')</th>
                                <th>@lang('messages.billDetails.discount')</th>
                                <th>@lang('messages.billDetails.total')</th>
                                <th>تاريخ الإنتاج</th>
                                <th>تاريخ الانتهاء</th>
                                <th>المصنع</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <select name="product_id[]" class="form-control">
                                        <option value="">اختر المنتج</option>
                                        @foreach ($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->id }} - {{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                {{-- <td>
                                    <select name="product_category[]" class="form-control">
                                        <option value="">@lang('messages.billDetails.select_category')</option>
                                        @foreach ($ProductCategories as $category)
                                        <option value="{{ $category->name }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </td> --}}
                                <td><input type="number" name="quantity[]" class="form-control quantity" min="1"></td>
                                <td><input type="number" name="cost[]" class="form-control cost" min="0" step="0.01"></td>
                                <td><input type="number" name="discount[]" class="form-control discount" value="0" min="0" step="0.01"></td>
                                <td><input type="number" name="total[]" class="form-control total" readonly></td>
                                <td><input type="date" name="production_date[]" class="form-control"></td>
                                <td><input type="date" name="exp_date[]" class="form-control"></td>
                                <td><input type="text" name="manufacture[]" class="form-control"></td>
                                <td><button type="button" class="btn btn-danger btn-sm removeRow">X</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <button type="button" id="addRow" class="btn btn-secondary mb-3">+ إضافة صف</button>
                <div class="mt-3">
                    <button type="submit" class="btn btn-success">@lang('messages.save')</button>
                    <button type="button" id="finishBillBtn" class="btn btn-outline-info">
                        @lang('messages.billDetails.finished_entry')
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

@endsection

<script>
document.addEventListener('DOMContentLoaded', function () {
    function calculateRow(row) {
        const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
        const cost = parseFloat(row.querySelector('.cost').value) || 0;
        const discount = parseFloat(row.querySelector('.discount').value) || 0;
        const total = (quantity * cost) - discount;
        row.querySelector('.total').value = total.toFixed(2);
    }

    document.querySelector('#itemsTable').addEventListener('input', function (e) {
        if (e.target.classList.contains('quantity') ||
            e.target.classList.contains('cost') ||
            e.target.classList.contains('discount')) {
            calculateRow(e.target.closest('tr'));
        }
    });

    document.getElementById('addRow').addEventListener('click', function () {
        const tableBody = document.querySelector('#itemsTable tbody');
        const newRow = tableBody.querySelector('tr').cloneNode(true);
        newRow.querySelectorAll('input, select').forEach(input => input.value = '');
        tableBody.appendChild(newRow);
    });

    document.querySelector('#itemsTable').addEventListener('click', function (e) {
        if (e.target.classList.contains('removeRow')) {
            const row = e.target.closest('tr');
            if (document.querySelectorAll('#itemsTable tbody tr').length > 1) {
                row.remove();
            }
        }
    });

    const finishBtn = document.getElementById('finishBillBtn');
    const printUrl = "{{ route('billDetails.print', ['billId' => $billId]) }}";
    const closeUrl = "{{ route('billDetails.close', ['billId' => $billId]) }}";

    finishBtn.addEventListener('click', function () {
        if (confirm("هل تريد طباعة الفاتورة؟")) {
            window.location.href = printUrl;
        } else {
            window.location.href = closeUrl;
        }
    });
});
</script>
