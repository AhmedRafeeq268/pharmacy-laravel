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

            {{-- زر إغلاق الصندوق --}}
            <div class="row mt-4">
                <div class="col-auto">
                    <form method="POST" action="{{ route('pos.closeCashbox') }}" onsubmit="return confirm('{{ __('messages.pos.confirm_close_cashbox') }}')">
                        @csrf
                        <input type="hidden" name="employee_id" value="5">
                        <button type="submit" class="btn btn-danger d-flex align-items-center gap-2">
                            <i class="bi bi-lock-fill"></i>
                            @lang('messages.pos.close_cashbox')
                        </button>
                    </form>
                </div>
            </div>


            <div class="form">
                <form method="POST" action="{{ route('pos.store', ['pos_bill_id' => $pos_bill_id]) }}">
                    @csrf
                    <input type="hidden" name="pos_bill_id" value="{{ $pos_bill_id }}">

                    <div class="row">
                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.pos.barcode')</label>
                            <input type="number" class="form-control" id="barcode" name="barcode"
                                   placeholder="{{ __('messages.pos.barcode') }}" value="{{ old('barcode') }}">
                            @error('barcode') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.pos.product_name')</label>
                            <input type="text" class="form-control" id="product_name" name="product_name"
                                   placeholder="{{ __('messages.pos.product_name') }}" disabled>
                        </div>

                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.pos.unit_price')</label>
                            <input type="number" class="form-control" id="unit_price" name="unit_price"
                                   placeholder="{{ __('messages.pos.unit_price') }}" disabled>
                        </div>

                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.pos.quantity')</label>
                            <input type="number" class="form-control" id="quantity" name="quantity"
                                   placeholder="{{ __('messages.pos.quantity') }}">
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

                            <button type="button" class="btn btn-info d-flex align-items-center gap-2"
                                    onclick="submitFinishForm()">
                                <i class="bi bi-check-circle-fill"></i>
                                @lang('messages.pos.finished_entry')
                            </button>



                        </div>
                    </div>
                </form>

                {{-- نموذج الإنهاء (مخفي) --}}
                <form id="finishForm" method="POST" action="{{ route('pos.finish', ['pos_bill_id' => $pos_bill_id ?: 0]) }}">

                    @csrf
                    <input type="hidden" name="discount" id="final_discount">
                </form>
            </div>

            {{-- جدول تفاصيل الفاتورة --}}
            @includeWhen(isset($posBillsDetails), 'pos._table', ['posBillsDetails' => $posBillsDetails])

            {{-- المجاميع --}}
            <div class="row mt-3">
                <div class="col-md-3">
                    <label class="mb-2">@lang('messages.pos.total_amount')</label>
                    <input type="number" class="form-control" id="total_amount" name="total_amount"
                           placeholder="{{ __('messages.pos.total_amount') }}" disabled
                           value="{{ $posBillsDetails->sum('price') ?? 0 }}">
                </div>

                <div class="col-md-3">
                    <label class="mb-2">@lang('messages.pos.discount')</label>
                    <input type="number" class="form-control" id="discount" name="discount"
                           placeholder="{{ __('messages.pos.discount') }}"
                           value="{{ old('discount', 0) }}">
                </div>

                <div class="col-md-3">
                    <label class="mb-2">@lang('messages.pos.net_amount')</label>
                    <input type="number" class="form-control" id="net_amount" name="net_amount"
                           placeholder="{{ __('messages.pos.net_amount') }}" disabled
                           value="{{ ($posBillsDetails->sum('price') ?? 0) - old('discount', 0) }}">
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const barcodeInput     = document.getElementById('barcode');
    const productNameInput = document.getElementById('product_name');
    const unitPriceInput   = document.getElementById('unit_price');
    const quantityInput    = document.getElementById('quantity');
    const totalAmountInput = document.getElementById('total_amount');
    const discountInput    = document.getElementById('discount');
    const netAmountInput   = document.getElementById('net_amount');

    barcodeInput.focus();

    let timer;
    barcodeInput.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(fetchProduct, 1000);
    });

    function fetchProduct() {
        const code = barcodeInput.value.trim();
        if (!code) {
            resetFields();
            return;
        }

        fetch(`/pos/fetchProduct/${code}`)
            .then(r => r.json())
            .then(({success, product, message}) => {
                if (!success) return resetFields(() => alert(message));
                productNameInput.value = product.name;
                unitPriceInput.value   = product.unit_price;
                quantityInput.value    = 1;
                quantityInput.focus();
                updateTotals();
            })
            .catch(console.error);
    }

    quantityInput.addEventListener('input', updateTotals);
    discountInput.addEventListener('input', updateTotals);

    function updateTotals() {
        const discount = parseFloat(discountInput.value) || 0;
        const total    = parseFloat(totalAmountInput.value) || 0;
        const net      = total - discount;

        netAmountInput.value = (net > 0 ? net : 0).toFixed(2);
    }

    function resetFields(cb) {
        productNameInput.value = '';
        unitPriceInput.value   = '';
        quantityInput.value    = '';
        barcodeInput.value     = '';
        if (typeof cb === 'function') cb();
    }

    document.addEventListener('keydown', e => {
        if (!quantityInput) return;
        const keys = ['+', '=', '-', 'NumpadAdd', 'NumpadSubtract'];
        if (!keys.includes(e.key) && !keys.includes(e.code)) return;

        let qty = parseInt(quantityInput.value) || 1;
        if ((e.key === '+' || e.key === '=' || e.code === 'NumpadAdd')) {
            quantityInput.value = qty + 1;
        } else if ((e.key === '-' || e.code === 'NumpadSubtract') && qty > 1) {
            quantityInput.value = qty - 1;
        }

        quantityInput.dispatchEvent(new Event('input'));
        e.preventDefault();
    });

    window.submitFinishForm = function () {
    const discountValue = document.getElementById('discount').value || 0;
    const url = document.getElementById('finishForm').action;

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ discount: discountValue })
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            alert(data.message || 'حدث خطأ أثناء حفظ الفاتورة.');
            return;
        }

        // عرض رسالة الطباعة
        if (confirm('هل تريد طباعة الفاتورة؟')) {
            window.open(`/pos/print/${data.bill_id}`, '_blank');
        }

        // إعادة تحميل الصفحة بعد الحفظ
        window.location.href = "{{ route('pos.create') }}";
    })
    .catch(error => {
        console.error('خطأ في الحفظ:', error);
        alert('حدث خطأ في الحفظ، الرجاء المحاولة لاحقاً.');
    });
};

});
</script>
@endpush
