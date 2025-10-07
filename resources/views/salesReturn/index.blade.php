@extends('layouts.master')

@section('title', __('messages.salesReturn.page_title'))

@section('content')
@include('layouts.partials.sweet_alert')
<!--Start Main content container-->
<div class="main_content_container">
    <div class="main_container main_menu_open">
        <!--Start system bath-->
        <div class="home_pass hidden-xs">
            <ul>
                <li class="bring_right"><span class="glyphicon glyphicon-home"></span></li>
                <li class="bring_right"><a href="#">@lang('messages.home_page_dashboard')</a></li>
            </ul>
        </div>
        <!--/End system bath-->

        <div class="w-90 mt-5">
            <div class="d-flex justify-content-center align-items-center mb-3 gap-2 flex-wrap">
                <input type="text" id="searchInput" class="form-control w-50 mb-2"
                       placeholder="@lang('messages.salesReturn.search_placeholder')">

                <!-- Export Dropdown -->
                    <div class="btn-group mb-2">
                        <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            @lang('messages.salesReturn.export_salesRetun')
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

            <div id="salesReturnsTable">
                @include('salesReturn._table',['salesReturns'=>$salesReturns])
            </div>
        </div>

        <div class="quick_links text-center d-flex justify-content-center align-items-center">
            <a href="{{ route('salesReturn.create') }}"
               class="btn text-white d-flex justify-content-center align-items-center py-3 px-5"
               style="background-color: #d35400; white-space: nowrap; min-width: 220px; height: 50px;">
                @lang('messages.salesReturn.add_new_return')
            </a>
        </div>
    </div>
    <!--/End Main content container-->
</div>
<!--/End body container section-->
@endsection

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const exportBtnExcel = document.getElementById('exportExcelBtn');
    const exportBtnPdf = document.getElementById('exportPdfBtn');

    searchInput.addEventListener('keyup', function () {
        let search = this.value;

        fetch(`{{ route('salesReturn.index') }}?search=${encodeURIComponent(search)}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(data => {
            document.getElementById('salesReturnsTable').innerHTML = data;
        });
    });

    exportBtnExcel.addEventListener('click', function () {
        const search = searchInput.value.trim();

        const tableBody = document.querySelector('#salesReturnsTable table tbody');
        if (!tableBody || tableBody.children.length === 0) {
            alert("@lang('messages.no_data_to_export')");
            return;
        }

        window.location.href = `{{ route('salesReturn.printSalesReturnsExcel') }}?search=${encodeURIComponent(search)}`;
    });

    exportPdfBtn.addEventListener('click', function () {
        const search = searchInput.value.trim();
        const tableBody = document.querySelector('#salesReturnsTable table tbody');

        if (!tableBody || tableBody.children.length === 0) {
            alert('@lang("messages.no_data_to_export")');
            return;
        }

        window.location.href = `{{ route('salesReturn.printSalesReturnsPdf') }}?search=${encodeURIComponent(search)}`;
    });
});
</script>
