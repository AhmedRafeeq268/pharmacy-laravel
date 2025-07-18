@extends('layouts.master')
@section('title','pharmacy')
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
            <div class="page_content">
                <div class="page_content">
                    <div class="quick_links text-center">
                        <h1 class="heading_title">@lang('messages.farmacy.quick_access')</h1>
                        <a href="{{ route('employee.create') }}" style="background-color: #c0392b">
                            <h4>@lang('messages.farmacy.add_employee')</h4>
                        </a>
                        <a href="{{ route('bill.create') }}" style="background-color: #2980b9">
                            <h4 >@lang('messages.farmacy.add_bill')</h4>
                        </a>
                        <a href="{{ route('customer.index') }}" style="background-color: #8e44ad">
                            <h4 >@lang('messages.farmacy.view_customers')</h4>
                        </a>
                        <a href="{{ route('customer.create') }}" style="background-color: #d35400">
                            <h4>@lang('messages.farmacy.add_customer')</h4>
                        </a>
                        <a href="" style="background-color: #2c3e50">
                            <h4>@lang('messages.farmacy.pos')</h4>
                        </a>
                    </div>
                    <div class="home_statics text-center">
                        <h1 class="heading_title">@lang('messages.farmacy.general_site_statistics')</h1>

                        <div style="background-color: #9b59b6" class="p-3 mb-3 text-white rounded">
                            <i class="bi bi-house-fill fs-1 bring_right"></i>
                            <h5 class="mt-3">@lang('messages.farmacy.site_visits')</h5>
                            <p class="h4">55</p>
                        </div>

                        <div style="background-color: #34495e" class="p-3 mb-3 text-white rounded">
                            <i class="bi bi-person-fill fs-1 bring_right"></i>
                            <h5 class="mt-3">@lang('messages.farmacy.number_of_employees')</h5>
                            <p class="h4">{{ $employees }}</p>
                        </div>

                        <div style="background-color: #00adbc" class="p-3 mb-3 text-white rounded">
                            <i class="bi bi-people-fill fs-1 bring_right"></i>
                            <h5 class="mt-3">@lang('messages.farmacy.number_of_customers')</h5>
                            <p class="h4">{{ $customers }}</p>
                        </div>

                        <div style="background-color: #f39c12" class="p-3 mb-3 text-white rounded">
                            <i class="bi bi-pencil-fill fs-1 bring_right"></i>
                            <h4 class="mt-3">@lang('messages.farmacy.number_of_articles')</h4>
                            <p class="h4">55</p>
                        </div>

                        <div style="background-color: #2ecc71" class="p-3 mb-3 text-white rounded">
                            <i class="bi bi-calendar-fill fs-1 bring_right"></i>
                            <h5 class="mt-3">@lang('messages.farmacy.site_age_days')</h5>
                            <p class="h4">55</p>
                        </div>
                    </div>


                </div>
            </div>
        </div>
        <!--/End Main content container-->


    </div>
    <!--/End body container section-->
@endsection
