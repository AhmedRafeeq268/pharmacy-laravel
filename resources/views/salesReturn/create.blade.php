@extends('layouts.master')

@section('title', __('messages.salesReturn.page_title'))

@section('content')
@include('layouts.partials.sweet_alert')

<div class="main_content_container">
    <div class="main_container main_menu_open">
        <div class="page_content">
            <h1 class="heading_title" style="margin-top: 90px;">
                @lang('messages.salesReturn.create_new_return')
            </h1>

            {{-- إدخال رقم الفاتورة --}}
            <div class="form mb-4 col-md-4">
                <label class="mb-1">@lang('messages.salesReturn.original_bill_number')</label>
                <input type="text" id="bill_number" class="form-control"
                       placeholder="@lang('messages.salesReturn.enter_bill_number')">
                <button type="button" class="btn btn-primary mt-3" onclick="loadBill()">
                    @lang('messages.salesReturn.show_bill_data')
                </button>
            </div>

            <form action="{{ route('salesReturn.store') }}" method="POST">
                @csrf

                {{-- بيانات الزبون وطريقة الإرجاع --}}
                <div class="row">
                    <div class="col-md-4">
                        <label>@lang('messages.salesReturn.customer_name')</label>
                        <input type="text" id="customer_name" class="form-control mt-2" readonly>
                        <input type="hidden" name="customer_id" id="customer_id">
                    </div>

                    <div class="col-md-4">
                        <label>@lang('messages.salesReturn.original_bill_number')</label>
                        <input type="text" name="bill_id" id="pos_bill_id" class="form-control mt-2" readonly>
                    </div>

                    <div class="col-md-4">
                        <label>@lang('messages.salesReturn.return_method')</label>
                        <select name="refund_method" class="form-control mt-2" id="refund_method" required>
                            <option value="" disabled selected>@lang('messages.salesReturn.choose_method')</option>
                            <option value="cash">@lang('messages.salesReturn.cash')</option>
                            <option value="debt">@lang('messages.salesReturn.add_to_customer_account')</option>
                        </select>
                    </div>
                </div>

                {{-- جدول الأصناف المرتجعة --}}
                <h4 class="mt-4">@lang('messages.salesReturn.returned_items_details')</h4>
                <table class="table table-bordered" id="items-table">
                    <thead>
                        <tr>
                            <th>@lang('messages.salesReturn.product')</th>
                            <th>@lang('messages.salesReturn.price')</th>
                            <th>@lang('messages.salesReturn.purchased_quantity')</th>
                            <th>@lang('messages.salesReturn.previously_returned_qty')</th>
                            <th>@lang('messages.salesReturn.remaining_qty')</th>
                            <th>@lang('messages.salesReturn.qty_to_return')</th>
                            <th>@lang('messages.salesReturn.total')</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- سيتم تعبئة الأصناف ديناميكياً --}}
                    </tbody>
                </table>

                {{-- المجموع الكلي --}}
                <div class="mb-3">
                    <label>@lang('messages.salesReturn.grand_total')</label>
                    <input type="text" id="total" class="form-control" readonly value="0.00">
                </div>


                {{-- زر الحفظ --}}
                <button type="submit" class="btn btn-success mt-3">
                    @lang('messages.salesReturn.save_return')
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function loadBill() {
    const billNumber = document.getElementById('bill_number').value;
    if (!billNumber) {
        alert("@lang('messages.salesReturn.enter_bill_alert')");
        return;
    }

    fetch(`/salesReturns/${billNumber}/details`)
        .then(res => {
            if (!res.ok) throw new Error("@lang('messages.salesReturn.fetch_bill_failed')");
            return res.json();
        })
        .then(data => {
            // التعامل مع عدم وجود زبون
            const customerName = data.customer ? data.customer.name : '';
            const customerId = data.customer ? data.customer.id : '';
            document.getElementById('customer_name').value = customerName;
            document.getElementById('customer_id').value = customerId;
            document.getElementById('pos_bill_id').value = data.bill.id;

            // حماية خيار "debt" إذا لا يوجد زبون
            const refundSelect = document.getElementById('refund_method');
            if (!data.customer) {
                refundSelect.querySelector('option[value="debt"]').disabled = true;
                refundSelect.value = 'cash';
            } else {
                refundSelect.querySelector('option[value="debt"]').disabled = false;
                refundSelect.value = '';
            }

            const tbody = document.querySelector('#items-table tbody');
            tbody.innerHTML = '';

            data.items.forEach((item, index) => {
                const remaining = item.quantity - item.returned;
                const price = item.price / item.quantity;
                const row = `
                    <tr>
                        <td>
                            <input type="text" class="form-control" value="${item.product_name}" readonly>
                            <input type="hidden" name="items[${index}][product_id]" value="${item.product_id}">
                        </td>
                        <td>
                            <input type="number" name="items[${index}][price]" class="form-control" value="${price}" readonly>
                        </td>
                        <td><input type="number" class="form-control" value="${item.quantity}" readonly></td>
                        <td><input type="number" class="form-control" value="${item.returned}" readonly></td>
                        <td><input type="number" class="form-control" value="${remaining}" readonly></td>
                        <td>
                            <input type="number" name="items[${index}][quantity]" class="form-control return-quantity"
                                   max="${remaining}" min="0" step="1" value="0"
                                   onfocus="this.select()"
                                   oninput="updateSubtotal(this)">
                        </td>
                        <td>
                            <input type="number" name="items[${index}][subtotal]" class="form-control subtotal" readonly value="0.00">
                        </td>
                    </tr>
                `;
                tbody.insertAdjacentHTML('beforeend', row);
            });

            updateTotal();
        })
        .catch(err => alert("@lang('messages.salesReturn.error_occurred'): " + err.message));
}

function updateSubtotal(input) {
    const row = input.closest('tr');
    const price = parseFloat(row.querySelector('input[name$="[price]"]').value);
    let quantity = parseInt(input.value) || 0;
    const max = parseInt(input.getAttribute('max')) || 0;
    if (quantity > max) input.value = quantity = max;

    row.querySelector('.subtotal').value = (price * quantity).toFixed(2);
    updateTotal();
}

function updateTotal() {
    let total = 0;
    document.querySelectorAll('.subtotal').forEach(el => total += parseFloat(el.value) || 0);
    document.getElementById('total').value = total.toFixed(2);
}
</script>
@endpush
