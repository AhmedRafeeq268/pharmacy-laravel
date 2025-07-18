
@extends('layouts.master')
@section('title','certified bill')

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
                @include('certified._table',['billDetailsItems'=>$billDetailsItems , 'billId'=>$billId , 'bills' =>$bills])
            </div>
            <div class="quick_links text-center d-flex justify-content-center gap-3 mt-4">

                <form method="POST"
                    action="{{ route('billCertified.store', ['billId' => $billId, 'page' => request()->get('page')]) }}">
                    @csrf
                    <button type="submit" class="btn btn-success text-white">
                        <h4 class="mb-0">@lang('messages.certify')</h4>
                    </button>
                </form>

                <form method="POST" action="{{ route('billCertified.reject', ['billId' => $billId , 'page' => request()->get('page')]) }}">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        <h4 class="mb-0">@lang('messages.uncertified')</h4>
                    </button>
                </form>

            </div>

        </div>
        <!--/End Main content container-->


    </div>
    <!--/End body container section-->
@endsection
