
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

                    <button id="exportBtn" class="btn btn-success mb-2">
                        <i class="bi bi-file-earmark-excel"></i>
                        @lang('messages.debts.export_debts_excel')
                    </button>
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
            const exportBtn = document.getElementById('exportBtn'); // لازم يكون موجود زر للتصدير بالـ id هذا

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

            exportBtn.addEventListener('click', function () {
                const search = searchInput.value.trim();

                // تحقق من وجود بيانات في الجدول
                // مثلا إذا جدول العملاء فيه صفوف <tr> في tbody، أو أي عنصر يعبر عن وجود بيانات
                const tableBody = document.querySelector('#debtTable table tbody');
                if (!tableBody || tableBody.children.length === 0) {
                    alert('لا توجد بيانات لتصديرها.');
                    return;
                }

                // اذهب للرابط مع تمرير كلمة البحث
                window.location.href = `{{ route('debts.printDebtsExcel') }}?search=${encodeURIComponent(search)}`;
            });
        });

    </script>
@endpush
