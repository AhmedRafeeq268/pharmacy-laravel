@extends('layouts.master')

@section('title', __('messages.debt'))

@section('content')
@include('layouts.partials.sweet_alert')
<div class="main_content_container">
    <div class="main_container main_menu_open">
        <div class="page_content">
            <h1 class="heading_title" style="margin-top: 90px;">@lang('messages.debt')</h1>

            <div class="form">
                <form id="debtPaymentForm" method="POST" action="{{ route('debts.pay') }}">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="customer_id">@lang('messages.debts.select_customer')</label>
                            <select name="customer_id" id="customer_id" class="form-control mt-2">
                                <option value="">@lang('messages.debts.select_customer_option')</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                            <small id="loadingMessage" class="text-muted d-none">@lang('messages.debts.loading')</small>
                        </div>
                        <div class="col-md-3">
                            <label>@lang('messages.debts.total_debt')</label>
                            <input type="text" id="totalDebt" class="form-control mt-2" readonly>
                        </div>
                        <div class="col-md-3">
                            <label>@lang('messages.debts.payment_amount')</label>
                            <input type="number" name="amount" id="amount" class="form-control mt-2">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-success w-100">@lang('messages.debts.pay')</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="container">
                <div id="debtDetails" class="mt-4 d-none">
                    <h4>@lang('messages.debts.debt_details')</h4>
                    <div id="debtContent"></div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@php
    $confirmPaymentText = __('messages.debts.confirm_payment_text', ['amount' => ':amount']);
@endphp


<script>
    document.addEventListener('DOMContentLoaded', () => {
        const confirmTextTemplate = @json($confirmPaymentText); // محتوى الترجمة

        const customerSelect  = document.getElementById('customer_id');
        const debtDetailsDiv  = document.getElementById('debtDetails');
        const debtContentDiv  = document.getElementById('debtContent');
        const loadingMsg      = document.getElementById('loadingMessage');
        const totalDebtInput  = document.getElementById('totalDebt');
        const amountInput     = document.getElementById('amount');
        const form            = document.getElementById('debtPaymentForm');

        customerSelect.addEventListener('change', loadDebts);

        function loadDebts() {
            const customerId = customerSelect.value;
            debtContentDiv.innerHTML = '';
            debtDetailsDiv.classList.add('d-none');
            totalDebtInput.value = '';
            amountInput.value = '';

            if (!customerId) return;

            loadingMsg.classList.remove('d-none');

            fetch(`/debts/ajax/${customerId}`)
                .then(response => response.json())
                .then(data => {
                    loadingMsg.classList.add('d-none');

                    if (data.success && data.debts.length > 0) {
                        let html = '<table class="table table-bordered"><thead><tr><th>@lang("messages.debts.debt_number")</th><th>@lang("messages.debts.total_amount")</th><th>@lang("messages.debts.remaining_amount")</th><th>@lang("messages.debts.products")</th></tr></thead><tbody>';
                        let totalRemaining = 0;

                        data.debts.forEach(debt => {
                            totalRemaining += parseFloat(debt.remaining_amount);

                            html += `<tr>
                                <td>#${debt.id}</td>
                                <td>${debt.total_amount}</td>
                                <td>${debt.remaining_amount}</td>
                                <td><ul>`;
                            debt.products.forEach(p => {
                                html += `<li>${p.name} - @lang('messages.debts.quantity'): ${p.quantity} - @lang('messages.debts.price'): ${p.price}</li>`;
                            });
                            html += `</ul></td></tr>`;
                        });

                        html += '</tbody></table>';
                        debtContentDiv.innerHTML = html;
                        debtDetailsDiv.classList.remove('d-none');
                        totalDebtInput.value = totalRemaining.toFixed(2);
                    } else {
                        debtContentDiv.innerHTML = '<p>@lang("messages.debts.no_debts")</p>';
                        debtDetailsDiv.classList.remove('d-none');
                        totalDebtInput.value = '0.00';
                    }
                })
                .catch(error => {
                    loadingMsg.classList.add('d-none');
                    console.error('خطأ:', error);
                    Swal.fire('@lang("messages.debts.error_fetching")', '', 'error');
                });
        }

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(form);
            const customerId = formData.get('customer_id');
            const amount = formData.get('amount');

            if (!customerId || !amount) {
                Swal.fire('@lang("messages.debts.alert")', '@lang("messages.debts.enter_customer_and_amount")', 'warning');
                return;
            }

            const confirmText = confirmTextTemplate.replace(':amount', amount);

            Swal.fire({
                title: '@lang("messages.debts.confirm_payment")',
                text: confirmText,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '@lang("messages.debts.confirm_yes")',
                cancelButtonText: '@lang("messages.debts.cancel")'
            }).then(result => {
                if (result.isConfirmed) {
                    fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('@lang("messages.debts.success")', data.message || '@lang("messages.debts.payment_successful")', 'success');
                            loadDebts();
                        } else {
                            Swal.fire('@lang("messages.debts.payment_failed")', data.message || '', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('خطأ:', error);
                        Swal.fire('@lang("messages.debts.error_during_payment")', '', 'error');
                    });
                }
            });
        });
    });
</script>
@endpush



