@extends('layouts.master')

@section('title', 'بحث عن ديون زبون')

@section('content')
@include('layouts.partials.sweet_alert')
<div class="main_content_container">
    <div class="main_container main_menu_open">
        <div class="page_content">
            <h1 class="heading_title" style="margin-top: 90px;">بحث عن ديون زبون</h1>

            <div class="form">
                <form id="debtPaymentForm" method="POST" action="{{ route('debts.pay') }}">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="customer_id">اختر الزبون</label>
                            <select name="customer_id" id="customer_id" class="form-control mt-2">
                                <option value="">-- اختر الزبون --</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                            <small id="loadingMessage" class="text-muted d-none">...جاري التحميل</small>
                        </div>
                        <div class="col-md-3">
                            <label>إجمالي الديون</label>
                            <input type="text" id="totalDebt" class="form-control mt-2" readonly>
                        </div>
                        <div class="col-md-3">
                            <label>مبلغ الدفع</label>
                            <input type="number" name="amount" id="amount" class="form-control mt-2">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-success w-100">دفع</button>
                        </div>
                    </div>
                </form>
            </div>

            <div id="debtDetails" class="mt-4 d-none">
                <h4>تفاصيل الديون</h4>
                <div id="debtContent"></div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
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
                    let html = '<table class="table table-bordered"><thead><tr><th>رقم الدين</th><th>المبلغ الكلي</th><th>المتبقي</th><th>المنتجات</th></tr></thead><tbody>';
                    let totalRemaining = 0;

                    data.debts.forEach(debt => {
                        totalRemaining += parseFloat(debt.remaining_amount);

                        html += `<tr>
                            <td>#${debt.id}</td>
                            <td>${debt.total_amount}</td>
                            <td>${debt.remaining_amount}</td>
                            <td><ul>`;
                        debt.products.forEach(p => {
                            html += `<li>${p.name} - الكمية: ${p.quantity} - السعر: ${p.price}</li>`;
                        });
                        html += `</ul></td></tr>`;
                    });

                    html += '</tbody></table>';
                    debtContentDiv.innerHTML = html;
                    debtDetailsDiv.classList.remove('d-none');
                    totalDebtInput.value = totalRemaining.toFixed(2);
                } else {
                    debtContentDiv.innerHTML = '<p>لا يوجد ديون مفتوحة لهذا الزبون.</p>';
                    debtDetailsDiv.classList.remove('d-none');
                    totalDebtInput.value = '0.00';
                }
            })
            .catch(error => {
                loadingMsg.classList.add('d-none');
                console.error('خطأ:', error);
                Swal.fire('خطأ', 'حدث خطأ أثناء جلب تفاصيل الدين.', 'error');
            });
    }

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(form);
        const customerId = formData.get('customer_id');
        const amount = formData.get('amount');

        if (!customerId || !amount) {
            Swal.fire('تنبيه', 'يرجى اختيار الزبون وإدخال مبلغ الدفع.', 'warning');
            return;
        }

        Swal.fire({
            title: 'تأكيد الدفع',
            text: `هل تريد دفع مبلغ ${amount}؟`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'نعم، ادفع',
            cancelButtonText: 'إلغاء'
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
                        Swal.fire('تم', data.message, 'success');
                        loadDebts();
                    } else {
                        Swal.fire('خطأ', data.message || 'فشل في الدفع.', 'error');
                    }
                })
                .catch(error => {
                    console.error('خطأ:', error);
                    Swal.fire('خطأ', 'حدث خطأ أثناء الدفع.', 'error');
                });
            }
        });
    });
});
</script>
@endpush
