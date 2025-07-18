@extends('layouts.master')

@section('title', __('messages.productCategory.productCategories'))

@section('content')
@include('layouts.partials.sweet_alert')
    <!--Start Main content container-->
    <div class="main_content_container">
        <div class="main_container  main_menu_open">
            <!--Start system bath-->
            <div class="home_pass hidden-xs">
                <ul>
                    <li class="bring_right"><span class="glyphicon glyphicon-home "></span></li>
                    <li class="bring_right"><a href="#">@lang('messages.home_page_dashboard')</a></li>
                </ul>
            </div>
            <!--/End system bath-->
            <div class="w-90 mt-5">
                <input type="text" id="searchInput" class="form-control w-50 mx-auto mb-3"  placeholder="@lang('messages.productCategory.search_category')">

                <div id="productCategoryTable">
                    @include('productCategory._table',['productCategorys'=>$productCategorys])
                </div>
            </div>

            <div class="quick_links text-center">
                <a href="{{ route('productCategory.create') }}" class="btn text-white py-3" style="background-color: #d35400">
                    <h6 class="mb-0 text-white">@lang('messages.productCategory.add_new_category')</h6>
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

        searchInput.addEventListener('keyup', function () {
            let search = this.value;

            fetch(`{{ route('productCategory.index') }}?search=${search}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(data => {
                document.getElementById('productCategoryTable').innerHTML = data;
            });
        });
    });
</script>
