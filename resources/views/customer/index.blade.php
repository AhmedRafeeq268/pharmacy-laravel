
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

                <input type="text" id="searchInput" class="form-control w-50 mx-auto mb-3"  placeholder="@lang('messages.customer.search_customer')">

                <div id="customerTable">
                    @include('customer._table', ['customers'=>$customers])
                </div>

            </div>
            <div class="quick_links text-center">
                <a href="{{ route('customer.create') }}" class="btn text-white py-3" style="background-color: #d35400 "> <h6 class="mb-0 text-white">@lang('messages.customer.add_new_customer')</h6> </a>
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

            fetch(`{{ route('customer.index') }}?search=${search}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(data => {
                document.getElementById('customerTable').innerHTML = data;
            });
        });
    });
</script>
