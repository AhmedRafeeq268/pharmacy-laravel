
@extends('layouts.master')
@section('title',__('messages.balanceStore.title'))

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
            <div class="w-100 mt-5">

                <input type="text" id="searchInput" class="form-control w-50 mx-auto"  placeholder="@lang('messages.balanceStore.search')">

                <div id="balanceStoreTable">
                    @include('balanceStore._table', ['balances'=>$balances])
                </div>

            </div>

        </div>
        <!--/End Main content container-->
    </div>
    <!--/End body container section-->
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchInput');

        searchInput.addEventListener('keyup', function () {
            let search = this.value;

            fetch(`{{ route('balanceStore.index') }}?search=${search}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(data => {
                document.getElementById('balanceStoreTable').innerHTML = data;
            });
        });
    });
</script>

