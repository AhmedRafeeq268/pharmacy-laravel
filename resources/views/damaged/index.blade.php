@extends('layouts.master')
@section('title', __('messages.damage'))

@section('content')
@include('layouts.partials.sweet_alert')

<div class="main_content_container">
    <div class="main_container main_menu_open">

        <!-- Breadcrumb -->
        <div class="home_pass hidden-xs">
            <ul>
                <li class="bring_right"><span class="glyphicon glyphicon-home"></span></li>
                <li class="bring_right"><a href="">@lang('messages.home')</a></li>
            </ul>
        </div>

        <!-- Search and Export -->
        <div class="w-100 mt-5">
            <div class="d-flex justify-content-center align-items-center mb-3 gap-2 flex-wrap">
                <input type="text" id="searchInput" class="form-control w-50 mb-2" placeholder="@lang('messages.damaged.search_damaged')">

                <!-- Export Dropdown -->
                <div class="btn-group mb-2">
                    <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        @lang('messages.damaged.export_damaged')
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
        </div>

        <!-- Table -->
        <div id="damagedTable">
            @include('damaged._table', ['damagedItems' => $damagedItems])
        </div>

        <!-- Add New Damaged Item -->
        <div class="quick_links text-center mt-4">
            <a href="{{ route('damaged.create') }}"
               class="btn text-white fw-bold px-4 py-2"
               style="background-color: #d35400; border-radius: 8px; font-size: 1rem;">
                @lang('messages.damaged.add_new_damaged_items')
            </a>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const exportExcelBtn = document.getElementById('exportExcelBtn');
    const exportPdfBtn = document.getElementById('exportPdfBtn');

    // Search functionality
    searchInput.addEventListener('keyup', function () {
        let search = this.value;

        fetch(`{{ route('damaged.index') }}?search=${encodeURIComponent(search)}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.text())
        .then(data => {
            document.getElementById('damagedTable').innerHTML = data;
        });
    });

    // Export Excel
    exportExcelBtn.addEventListener('click', function () {
        const search = searchInput.value.trim();
        const tableBody = document.querySelector('#damagedTable table tbody');

        if (!tableBody || tableBody.children.length === 0) {
            alert('@lang("messages.no_data_to_export")');
            return;
        }

        window.location.href = `{{ route('damaged.printDamagedExcel') }}?search=${encodeURIComponent(search)}`;
    });

    // Export PDF
    exportPdfBtn.addEventListener('click', function () {
        const search = searchInput.value.trim();
        const tableBody = document.querySelector('#damagedTable table tbody');

        if (!tableBody || tableBody.children.length === 0) {
            alert('@lang("messages.no_data_to_export")');
            return;
        }

        window.location.href = `{{ route('damaged.printDamagedPdf') }}?search=${encodeURIComponent(search)}`;
    });
});
</script>
@endpush
