
@extends('layouts.master')
@section('title','suppliers')

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
                    <input type="text" id="searchInput" class="form-control w-50 mb-2"  placeholder="@lang('messages.suppliers.search_suppliers')">

                    <!-- Export Dropdown -->
                    <div class="btn-group mb-2">
                        <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            @lang('messages.suppliers.export_suppliers')
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
                <div id="supplierTable">
                    @include('suppliers._table',['suppliers'=>$suppliers])
                </div>
            </div>
            <div class="quick_links text-center">
                <a href="{{ route('supplier.create') }}" class="btn text-white py-3" style="background-color: #d35400"> <h5 class="mb-0 text-white">@lang('messages.suppliers.add_new_supplier')</h5> </a>
            </div>
        </div>
        <!--/End Main content container-->


    </div>
    <!--/End body container section-->
@endsection

<script>
        document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchInput');
        const exportExcelBtn = document.getElementById('exportExcelBtn');
        const exportPdfBtn   = document.getElementById('exportPdfBtn');

        searchInput.addEventListener('keyup', function () {
            let search = this.value;

            fetch(`{{ route('supplier.index') }}?search=${encodeURIComponent(search)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(data => {
                document.getElementById('supplierTable').innerHTML = data;
            });
        });

        exportExcelBtn.addEventListener('click', function () {
            const search = searchInput.value.trim();

            // تحقق من وجود بيانات في الجدول
            // مثلا إذا جدول العملاء فيه صفوف <tr> في tbody، أو أي عنصر يعبر عن وجود بيانات
            const tableBody = document.querySelector('#supplierTable table tbody');
            if (!tableBody || tableBody.children.length === 0) {
                alert('لا توجد بيانات لتصديرها.');
                return;
            }

            // اذهب للرابط مع تمرير كلمة البحث
            window.location.href = `{{ route('supplier.printSuppliersExcel') }}?search=${encodeURIComponent(search)}`;
        });

        exportPdfBtn.addEventListener('click', function () {
        const search = searchInput.value.trim();
        const tableBody = document.querySelector('#supplierTable table tbody');

        if (!tableBody || tableBody.children.length === 0) {
            alert('@lang("messages.no_data_to_export")');
            return;
        }

        window.location.href = `{{ route('supplier.printSupplierPdf') }}?search=${encodeURIComponent(search)}`;
    });
    });

</script>

