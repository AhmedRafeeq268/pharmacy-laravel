@extends('layouts.master')
@section('title', __('messages.billDetails.view_billDetails'))

@section('content')
@include('layouts.partials.sweet_alert')
<!--Start Main content container-->
<div class="main_content_container">
    <div class="main_container  main_menu_open">
        <!--Start system bath-->
        <div class="home_pass hidden-xs">
            <ul>
                <li class="bring_right"><span class="glyphicon glyphicon-home "></span></li>
                <li class="bring_right"><a href="">@lang('messages.home')</a></li>
            </ul>
        </div>
        <!--/End system bath-->
        <div class="w-100 mt-5">
            @include('certified._table',['bills'=>$bills]);
        </div>
        <div class="quick_links text-center">
            <a href="{{ route('bill.create') }}" class="btn text-white py-3" style="background-color: #d35400"><h6 class="mb-0 text-white">@lang('messages.bill.add_new_bill')</h6></a>
        </div>
    </div>
    <!--/End Main content container-->
</div>
<!--/End body container section-->
@endsection
