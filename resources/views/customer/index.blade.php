@extends('layouts.master')
@section('title', __('messages.customers'))

@section('content')
@include('layouts.partials.sweet_alert')
    <!--Start Main content container-->
    <div class="main_content_container">
        <div class="main_container  main_menu_open">
            <!--Start system bath-->
            <div class="home_pass hidden-xs">
                <ul>
                    <li class="bring_right"><span class="glyphicon glyphicon-home "></span></li>
                    <li class="bring_right"><a href="">الصفحة الرئيسية للوحة تحكم الموقع</a></li>
                </ul>
            </div>
            <!--/End system bath-->

            <div class="w-100 mt-5">
                <div class="d-flex justify-content-center align-items-center mb-3 gap-2 flex-wrap">
                    <!-- Input Search -->
                    <input type="text" id="searchInput" class="form-control w-50 mb-2" placeholder="@lang('messages.customer.search_customer')">

                    <!-- Export Dropdown -->
                    <div class="btn-group mb-2">
                        <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            @lang('messages.customer.export_customers')
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <button id="exportExcelBtn" class="dropdown-item">
                                    <i class="bi bi-file-earmark-excel"></i>
                                    @lang('messages.export_excel')
                                </button>
                            </li>
                            <li>
                                <button id="exportPdfBtn" class="dropdown-item">
                                    <i class="bi bi-file-earmark-pdf"></i>
                                    @lang('messages.export_pdf')
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Customer Table -->
                <div id="customerTable">
                    @include('customer._table', ['customers' => $customers])
                </div>
            </div>

            <!-- Quick Links -->
            <div class="quick_links text-center">
                <a href="{{ route('customer.create') }}" class="btn text-white py-3" style="background-color: #d35400 ">
                    <h6 class="mb-0 text-white">@lang('messages.customer.add_new_customer')</h6>
                </a>
            </div>
        </div>
        <!--/End Main content container-->
    </div>
    <!--/End body container section-->
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput    = document.getElementById('searchInput');
    const exportExcelBtn = document.getElementById('exportExcelBtn');
    const exportPdfBtn   = document.getElementById('exportPdfBtn');

    // 🔍 البحث
    searchInput.addEventListener('keyup', function () {
        let search = this.value;

        fetch(`{{ route('customer.index') }}?search=${encodeURIComponent(search)}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.text())
        .then(data => {
            document.getElementById('customerTable').innerHTML = data;
        });
    });

    // 📊 تصدير Excel
    exportExcelBtn.addEventListener('click', function () {
        const search = searchInput.value.trim();
        const tableBody = document.querySelector('#customerTable table tbody');

        if (!tableBody || tableBody.children.length === 0) {
            alert('@lang("messages.no_data_to_export")');
            return;
        }

        window.location.href = `{{ route('customer.printCustomersExcel') }}?search=${encodeURIComponent(search)}`;
    });

    // 📑 تصدير PDF
    exportPdfBtn.addEventListener('click', function () {
        const search = searchInput.value.trim();
        const tableBody = document.querySelector('#customerTable table tbody');

        if (!tableBody || tableBody.children.length === 0) {
            alert('@lang("messages.no_data_to_export")');
            return;
        }

        window.location.href = `{{ route('customer.printCustomerPdf') }}?search=${encodeURIComponent(search)}`;
    });
});
</script>
@endpush
