
@extends('layouts.master')
@section('title',__('messages.debt'))

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
                    <input type="text" id="searchInput" class="form-control w-50 mb-2" placeholder="@lang('messages.debts.search_debt')">

                    <!-- Export Dropdown -->
                    <div class="btn-group mb-2">
                        <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            @lang('messages.debts.export_debts')
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

                <div class="mb-3 text-center">
                    <h4>
                        @lang('messages.debts.total_debt') :
                        <span class="badge bg-primary">
                            {{ number_format($total_debts, 2) }}
                        </span>
                    </h4>
                </div>

                <div id="debtTable">
                    @include('debts._table', ['debts' => $debts])
                </div>
            </div>
        </div>
        <!--/End Main content container-->


    </div>
    <!--/End body container section-->
@endsection
@push('scripts')
    <script>
            document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchInput');
            const exportBtnExcel = document.getElementById('exportExcelBtn');
            const exportBtnPdf = document.getElementById('exportPdfBtn');

            searchInput.addEventListener('keyup', function () {
                let search = this.value;

                fetch(`{{ route('debts.index') }}?search=${encodeURIComponent(search)}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(data => {
                    document.getElementById('debtTable').innerHTML = data;
                });
            });

            exportBtnExcel.addEventListener('click', function () {
                const search = searchInput.value.trim();
                const tableBody = document.querySelector('#debtTable table tbody');
                if (!tableBody || tableBody.children.length === 0) {
                    alert('لا توجد بيانات لتصديرها.');
                    return;
                }
                window.location.href = `{{ route('debts.printDebtsExcel') }}?search=${encodeURIComponent(search)}`;
            });

            exportPdfBtn.addEventListener('click', function () {
                const search = searchInput.value.trim();
                const tableBody = document.querySelector('#debtTable table tbody');

                if (!tableBody || tableBody.children.length === 0) {
                    alert('@lang("messages.no_data_to_export")');
                    return;
                }

                window.location.href = `{{ route('debts.printDebtsPdf') }}?search=${encodeURIComponent(search)}`;
            });

        });

    </script>
@endpush
