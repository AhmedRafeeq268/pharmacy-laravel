
@extends('layouts.master')
@section('title', __('messages.damage'))

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
                    <input type="text" id="searchInput" class="form-control w-50 mb-2" placeholder="@lang('messages.damaged.search_damaged')">

                    <button id="exportBtn" class="btn btn-success mb-2">
                        <i class="bi bi-file-earmark-excel"></i>
                        @lang('messages.damaged.export_damaged_excel')
                    </button>
                </div>

                <div id="damagedTable">
                    @include('damaged._table', ['damagedItems' => $damagedItems])
                </div>
            </div>


            <div class="quick_links text-center">
                <a href="{{ route('damaged.create') }}"
                class="btn text-white fw-bold px-4 py-2"
                style="background-color: #d35400; border-radius: 8px; font-size: 1rem;">
                @lang('messages.damaged.add_new_damaged_items')
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
        const exportBtn = document.getElementById('exportBtn'); // لازم يكون موجود زر للتصدير بالـ id هذا

        searchInput.addEventListener('keyup', function () {
            let search = this.value;

            fetch(`{{ route('damaged.index') }}?search=${encodeURIComponent(search)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(data => {
                document.getElementById('damagedTable').innerHTML = data;
            });
        });

        exportBtn.addEventListener('click', function () {
            const search = searchInput.value.trim();

            // تحقق من وجود بيانات في الجدول
            // مثلا إذا جدول العملاء فيه صفوف <tr> في tbody، أو أي عنصر يعبر عن وجود بيانات
            const tableBody = document.querySelector('#damagedTable table tbody');
            if (!tableBody || tableBody.children.length === 0) {
                alert('لا توجد بيانات لتصديرها.');
                return;
            }

            // اذهب للرابط مع تمرير كلمة البحث
            window.location.href = `{{ route('damaged.printDamagedExcel') }}?search=${encodeURIComponent(search)}`;
        });
    });

</script>
