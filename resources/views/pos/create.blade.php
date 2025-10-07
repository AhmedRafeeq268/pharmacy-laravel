@extends('layouts.master')

@section('title', __('messages.pos.create_pos'))

@section('content')
@include('layouts.partials.sweet_alert')

<div class="main_content_container">
    <div class="main_container main_menu_open">
        <div class="page_content">
            <h1 class="heading_title" style="margin-top: 90px;">
                @lang('messages.pos.create_pos')
            </h1>
            <input type="hidden" name="session_id" value="{{ $currentSession->id }}">

            <div class="col-auto">
                <button type="button" class="btn btn-danger d-flex align-items-center gap-2" onclick="handleCloseCashbox({{ $currentSession->id }})">
                    <i class="bi bi-lock-fill"></i>
                    @lang('messages.pos.close_cashbox')
                </button>
            </div>
            <input type="hidden" name="session_id" value="{{ $currentSession->id }}">

            <div class="form">
                <form method="POST" action="{{ route('pos.store', ['pos_bill_id' => $pos_bill_id]) }}">
                    @csrf
                    <input type="hidden" name="pos_bill_id" value="{{ $pos_bill_id }}">

                    <div class="row">
                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.pos.barcode')</label>
                            <input type="number" class="form-control" id="barcode" name="barcode" placeholder="{{ __('messages.pos.barcode') }}" value="{{ old('barcode') }}">
                            @error('barcode') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.pos.product_name')</label>
                            <input type="text" class="form-control" id="product_name" name="product_name" placeholder="{{ __('messages.pos.product_name') }}" disabled>
                        </div>

                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.product.price_sell')</label>
                            <input type="number" class="form-control" id="price_sell" name="price_sell" placeholder="{{ __('messages.product.price_sell') }}" disabled>
                        </div>

                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.pos.quantity')</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" placeholder="{{ __('messages.pos.quantity') }}">
                            @error('quantity') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    {{-- أزرار الإضافة وإنهاء الإدخال --}}
                    <div class="row mt-3">
                        <div class="col-auto d-flex gap-2">
                            <button type="submit" class="btn btn-success d-flex align-items-center gap-2">
                                <i class="bi bi-plus-circle-fill"></i>
                                @lang('messages.pos.add_to_cart')
                            </button>

                            <button type="button" class="btn btn-info d-flex align-items-center gap-2" onclick="submitFinishForm()">
                                <i class="bi bi-check-circle-fill"></i>
                                @lang('messages.pos.finished_entry')
                            </button>
                        </div>
                    </div>

                    {{-- أزرار الدفع --}}
                    <div class="row mt-4">
                        <div class="col-auto d-flex gap-2">
                            <button type="button" class="btn btn-primary d-flex align-items-center gap-2" onclick="submitFinishForm('cash')">
                                <i class="bi bi-cash"></i>
                                @lang('messages.pos.pay_cash')
                            </button>

                            <button type="button" class="btn btn-secondary d-flex align-items-center gap-2" onclick="submitFinishForm('visa')">
                                <i class="bi bi-credit-card"></i>
                                @lang('messages.pos.pay_visa')
                            </button>

                            <button type="button" class="btn btn-warning d-flex align-items-center gap-2" onclick="submitFinishForm('debt')">
                                <i class="bi bi-receipt"></i>
                                @lang('messages.pos.pay_debt')
                            </button>
                        </div>
                    </div>

                </form>
            </div>

            {{-- جدول تفاصيل الفاتورة --}}
            @includeWhen(isset($posBillsDetails), 'pos._table', ['posBillsDetails' => $posBillsDetails])

            {{-- المجاميع --}}
            <div class="row mt-3">
                <div class="col-md-3">
                    <label class="mb-2">@lang('messages.pos.total_amount')</label>
                    <input type="number" class="form-control" id="total_amount" name="total_amount" disabled value="{{ $posBillsDetails->sum('price') ?? 0 }}">
                </div>

                <div class="col-md-3">
                    <label class="mb-2">@lang('messages.pos.discount')</label>
                    <input type="number" class="form-control" id="discount" name="discount" value="{{ old('discount', 0) }}">
                </div>

                <div class="col-md-3">
                    <label class="mb-2">@lang('messages.pos.net_amount')</label>
                    <input type="number" class="form-control" id="net_amount" name="net_amount" disabled value="{{ ($posBillsDetails->sum('price') ?? 0) - old('discount', 0) }}">
                </div>
            </div>

            {{-- مودال اختيار الزبون عند الدفع بالدين --}}
            <div class="modal fade" id="debtModal" tabindex="-1" aria-labelledby="debtModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <form id="debtForm" class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="debtModalLabel">اختر الزبون لتسجيل الدين</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                        </div>
                        <div class="modal-body">
                            <label for="customer_id" class="form-label">الزبون</label>
                            <select id="customer_id" name="customer_id" class="form-select" required>
                                <option value="">-- اختر الزبون --</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">تأكيد</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        </div>
                    </form>
                </div>
            </div>
        </div> {{-- نهاية page_content --}}
    </div>
</div>
@endsection
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
@push('scripts')
<script>
    const posBillId = @json($pos_bill_id);

    // إنهاء الفاتورة
    // إنهاء الفاتورة
window.submitFinishForm = function(paymentType) {
    const discountValue = document.getElementById('discount')?.value || 0;
    const debtModalEl = document.getElementById('debtModal');
    const debtModal = new bootstrap.Modal(debtModalEl);

    let confirmMessage = '';
    if (paymentType === 'cash') {
        confirmMessage = "هل أنت متأكد من الدفع نقداً؟";
    } else if (paymentType === 'visa') {
        confirmMessage = "هل أنت متأكد من الدفع بالبطاقة (فيزا)؟";
    } else if (paymentType === 'debt') {
        confirmMessage = "هل تريد تسجيل الفاتورة كدين على الزبون؟";
    } else {
        confirmMessage = "هل تريد فقط إنهاء الإدخال بدون دفع؟";
    }

    if (!confirm(confirmMessage)) {
        return; // إذا اختار إلغاء، ما يرسل الطلب
    }

    if (paymentType === 'debt') {
        debtModal.show();
        return;
    }
    if (paymentType === 'cash') {
        sendFinishRequest({ discount: discountValue, payment_status: 'cash' });
        return;
    }
    if (paymentType === 'visa') {
        sendFinishRequest({ discount: discountValue, payment_status: 'visa' });
        return;
    }
    // مجرد إنهاء إدخال
    sendFinishRequest({ discount: discountValue, payment_status: 'pending' });
};


    // إرسال الطلب
    function sendFinishRequest(payload) {
        const url = "{{ route('pos.finish', ['pos_bill_id' => $pos_bill_id ?: 0]) }}";

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(r => r.json())
        .then(data => {
            if (!data.success) {
                alert(data.message || 'حدث خطأ أثناء حفظ الفاتورة.');
                return;
            }
            if (confirm('هل تريد طباعة الفاتورة؟')) {
                window.open(`/pos/print/${data.bill_id}`, '_blank');
                setTimeout(() => window.location.href = "{{ route('pos.create') }}", 1500);
            } else {
                window.location.href = "{{ route('pos.create') }}";
            }
        })
        .catch(e => {
            console.error(e);
            alert('حدث خطأ في الحفظ، الرجاء المحاولة لاحقاً.');
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        const barcodeInput     = document.getElementById('barcode');
        const productNameInput = document.getElementById('product_name');
        const PriceSellInput   = document.getElementById('price_sell');
        const quantityInput    = document.getElementById('quantity');
        const totalAmountInput = document.getElementById('total_amount');
        const discountInput    = document.getElementById('discount');
        const netAmountInput   = document.getElementById('net_amount');

        barcodeInput.focus();

        let timer;
        barcodeInput.addEventListener('input', () => {
            clearTimeout(timer);
            timer = setTimeout(fetchProduct, 600);
        });

        function fetchProduct() {
            const code = barcodeInput.value.trim();
            if (!code) return resetFields();
            fetch(`/pos/fetchProduct/${code}`)
                .then(r => r.json())
                .then(({success, product, message}) => {
                    if (!success) return resetFields(() => alert(message));
                    productNameInput.value = product.name;
                    PriceSellInput.value   = product.price_sell;
                    quantityInput.value    = 1;
                    updateTotals();
                    quantityInput.focus();
                })
                .catch(console.error);
        }

        quantityInput.addEventListener('input', updateTotals);
        discountInput.addEventListener('input', updateTotals);

        function updateTotals() {
            const price    = parseFloat(PriceSellInput.value)   || 0;
            const qty      = parseInt(quantityInput.value)      || 0;
            const discount = parseFloat(discountInput.value)    || 0;

            const total = price * qty;
            totalAmountInput.value = total.toFixed(2);
            netAmountInput.value   = Math.max(total - discount, 0).toFixed(2);
        }

        function resetFields(cb) {
            productNameInput.value = '';
            PriceSellInput.value   = '';
            quantityInput.value    = '';
            totalAmountInput.value = '';
            netAmountInput.value   = '';
            barcodeInput.value     = '';
            if (cb) cb();
        }

        // التحكم بالكميات عبر لوحة المفاتيح
        document.addEventListener('keydown', e => {
            if (!quantityInput) return;
            if (e.key === '+' || e.code === 'NumpadAdd') {
                quantityInput.value = (parseInt(quantityInput.value) || 1) + 1;
            } else if (e.key === '-' || e.code === 'NumpadSubtract') {
                let qty = parseInt(quantityInput.value) || 1;
                if (qty > 1) quantityInput.value = qty - 1;
            } else return;
            quantityInput.dispatchEvent(new Event('input'));
            e.preventDefault();
        });

        // مودال الدين
        const debtForm = document.getElementById('debtForm');
        const debtModalEl = document.getElementById('debtModal');
        const debtModal = new bootstrap.Modal(debtModalEl);

        debtForm.addEventListener('submit', e => {
            e.preventDefault();
            const customerId = debtForm.customer_id.value;
            if (!customerId) {
                alert('يرجى اختيار الزبون');
                return;
            }
            const discountValue = discountInput.value || 0;
            sendFinishRequest({ discount: discountValue, payment_status: 'debt', customer_id: customerId });
            debtModal.hide();
        });
    });
        // سؤال الطباعة

        function handleCloseCashbox(employeeId) {
            const closeCashboxUrl = "{{ route('pos.closeCashbox') }}";
            const printUrl = `/pos/cashboxReport/${employeeId}`;
            const redirectUrl = "{{ route('pos.index') }}";

            if (!confirm("هل أنت متأكد أنك تريد إغلاق الصندوق؟")) {
                return;
            }

            fetch(closeCashboxUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ employee_id: employeeId })
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => { throw new Error(text) });
                }
                return response.json();
            })
            .then(data => {
                console.log(data); // debug

                if (confirm("هل تريد طباعة التقرير؟")) {
                    window.open(printUrl, '_blank');
                }
                window.location.href = redirectUrl;
            })
            .catch(error => {
                alert("خطأ: " + error.message);
            });
        }



</script>

@endpush
