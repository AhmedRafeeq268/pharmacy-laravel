@extends('layouts.master')

@section('title', __('messages.product.view_products'))

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
                    <input type="text" id="searchInput" class="form-control w-50  mb-2"
                        placeholder="@lang('messages.product.search_product')">

                    <!-- Export Dropdown -->
                    <div class="btn-group mb-2">
                        <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            @lang('messages.product.export_products')
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

                    <!-- Import Excel -->
                    <div class="btn-group mb-2">
                        <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            📥 @lang('messages.product.import_products')
                        </button>
                        <ul class="dropdown-menu p-3" style="min-width: 300px;">
                            <li>
                                {{-- <form action="{{ route('product.import') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-2">
                                        <input type="file" name="file" class="form-control" accept=".xlsx,.xls" required>
                                    </div>
                                    <button type="submit" class="btn btn-success w-100">
                                        ✅ @lang('messages.upload')
                                    </button>
                                </form> --}}
                            </li>
                        </ul>
                    </div>
                </div>

                <div id="productTable">
                    @include('product._table',['products'=>$products])
                </div>
            </div>
            <div class="quick_links text-center">
                <a href="{{ route('product.create') }}" class="btn text-white py-3" style="background-color: #d35400">
                    <h6 class="mb-0 text-white">@lang('messages.product.add_new_product')</h6>
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
        const exportBtnExel = document.getElementById('exportExcelBtn');
        const exportPdfBtn = document.getElementById('exportPdfBtn');

        searchInput.addEventListener('keyup', function () {
            let search = this.value;

            fetch(`{{ route('product.index') }}?search=${encodeURIComponent(search)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(data => {
                document.getElementById('productTable').innerHTML = data;
            });
        });

        exportBtnExel.addEventListener('click', function () {
            const search = searchInput.value.trim();
            const tableBody = document.querySelector('#productTable table tbody');
            if (!tableBody || tableBody.children.length === 0) {
                alert('لا توجد بيانات لتصديرها.');
                return;
            }
            window.location.href = `{{ route('product.exportExcel') }}?search=${encodeURIComponent(search)}`;
        });

        exportPdfBtn.addEventListener('click', function () {
            const search = searchInput.value.trim();
            const tableBody = document.querySelector('#productTable table tbody');

            if (!tableBody || tableBody.children.length === 0) {
                alert('@lang("messages.no_data_to_export")');
                return;
            }

            window.location.href = `{{ route('product.exportPDF') }}?search=${encodeURIComponent(search)}`;
        });
    });
</script>
