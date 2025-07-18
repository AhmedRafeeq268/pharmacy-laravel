<!DOCTYPE html>
<html lang="{{ App::getLocale() }}">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>

    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/icon.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/style.css') }}" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="{{ asset('libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    {{-- <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet"> --}}


    @if(App::getLocale() == 'ar')
        <link href="{{ asset('css/ar.css') }}" rel="stylesheet" />
    @else
        <link href="{{ asset('css/en.css') }}" rel="stylesheet" />
    @endif
</head>
<body>
<div class="container-fluid">

    <!--Start header-->
    <div class="row header_section align-items-center" style="height:60px;">

        <div class="col-sm-3 col-xs-12 logo_area bring_right d-flex align-items-center justify-content-between">
            <h1 class="inline-block mb-4">
                <img src="../img/logo.png"  style="height:40px; margin-left:10px;" />
                @lang('messages.pharmacy')
            </h1>
            <i class="bi bi-list open_close_menu " style="font-size: 1.8rem; cursor: pointer;"
               data-bs-toggle="tooltip" data-bs-placement="right" title="@lang('messages.master.list')"></i>
        </div>

        <div class="col-sm-3 col-xs-12 head_buttons_area bring_right hidden-xs text-end">
            <button class="btn btn-danger rounded-0 px-3 d-flex align-items-center" type="button" title= @lang('messages.master.screen')>
                <i class="bi bi-fullscreen fs-4 text-white mb-3"></i>
            </button>
        </div>

        <div class="col-sm-4 col-xs-12 user_header_area bring_left left_text d-flex align-items-center justify-content-end mb-4">
            <!-- زر تبديل اللغة -->
            <a href="{{ route('toggle.language') }}"
               class="change_lang btn btn-outline-primary btn-sm me-2">
                @if(App::getLocale() == 'ar') English @else العربية @endif
            </a>

            <!-- معلومات المستخدم -->
            <div class="user_info d-flex align-items-center">
                <div class="user-icon rounded-circle bg-primary d-flex justify-content-center align-items-center me-3"
                    style="width: 40px; height: 40px; color: #fff; font-size: 1.4rem;">
                    <i class="bi bi-person-fill"></i>
                </div>
                <span class="user_name h5 mb-0 me-3" style="font-weight: 600; color: #faf2f2;">
                    {{ Auth::user()->name }}
                </span>
                <span class="glyphicon glyphicon-cog" style="font-size: 1.2rem; color: #6c757d; cursor: pointer;" title="الإعدادات"></span>
            </div>


        </div>
    </div>
    <!--/End header-->

    <!--Start body container section-->
    <div class="row container_section">

        <!--Start left sidebar-->
        <div class="user_details close_user_details bring_left p-3">

            <div class="user_area text-center mb-4">
                <!-- أيقونة الموظف بدلاً من الصورة -->
                <div class="user-icon rounded-circle bg-primary mx-auto d-flex justify-content-center align-items-center mb-3"
                    style="width: 80px; height: 80px; color: white; font-size: 2.5rem;">
                    <i class="bi bi-person-badge-fill"></i>
                </div>

                <h1 class="h4 mb-2">{{ Auth::user()->name }}</h1>

                <p><a href="#" class="text-decoration-none">بيانات المستخدم</a></p>
                {{-- <p><a href="#" class="text-decoration-none">تغيير كلمة المرور</a></p> --}}
                <p><a href="#" class="text-decoration-none">المساعدة</a></p>

                <form method="get" action="{{ route('password.change') }}" class="mb-2">
                    @csrf
                    <button type="submit" class="btn btn-outline-primary btn-sm w-100">@lang('messages.master.change_password')</button>
                </form>

                <form method="post" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm w-100">@lang('messages.master.logout')</button>
                </form>
            </div>

            <div class="who_is_online">
                <h3 class="mb-3">العاملين حاليا على النظام</h3>

                @for($i = 0; $i < 5; $i++)
                <div class="employee_online d-flex align-items-center mb-3">
                    <div class="employee-icon rounded-circle bg-secondary d-flex justify-content-center align-items-center me-3"
                        style="width:40px; height:40px; color: white; font-size: 1.2rem;">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div>
                        <p class="mb-0 fw-semibold">حسام جمال توفيق زوين</p>
                        <small class="text-muted">مركز التقنية - جامعة المنصورة</small>
                    </div>
                </div>
                @endfor
            </div>
        </div>

        <!--/End left sidebar-->

        <!--Start Side bar main menu-->
        <div class="main_sidebar bring_right">
            <div class="main_sidebar_wrapper">
                <form class="form-inline search_box text-center mb-3">
                    <div class="form-group d-flex">
                        <input type="search" class="form-control" placeholder="@lang('messages.master.search')" />
                        <button type="submit" class="btn btn-default ms-2">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>

                <ul>
                    <li>
                        <a href="{{ route('farmacy.index') }}">
                            <i class="bi bi-house me-1"></i> @lang('messages.master.home_page')
                        </a>
                    </li>

                    <li>
                        <a href="#">
                            <i class="bi bi-gear me-1"></i> @lang('messages.master.system_settings')
                        </a>
                        <ul class="drop_main_menu me-1">
                            <li><a href="{{ route('codeTb.index') }}">@lang('messages.master.system_constants ')</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="#">
                            <i class="bi bi-person me-1"></i>@lang('messages.master.customer_management ')
                        </a>
                        <ul class="drop_main_menu">
                            <li><a href="{{ route('customer.create') }}">@lang('messages.master.add_new ')</a></li>
                            <li><a href="{{ route('customer.index') }}">@lang('messages.master.view_all ')</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="#">
                            <i class="bi bi-people-fill me-1"></i> @lang('messages.master.employee_management ')
                        </a>
                        <ul class="drop_main_menu">
                            <li><a href="{{ route('employee.create') }}">@lang('messages.master.add_new ')</a></li>
                            <li><a href="{{ route('employee.index') }}">@lang('messages.master.view_all ')</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="#">
                            <i class="bi bi-archive-fill me-1"></i>@lang('messages.master.warehouses_purchases ')
                        </a>
                        <ul class="drop_main_menu">
                            <li>
                                <a href="#">
                                    <i class="bi bi-truck me-1"></i>@lang('messages.master.suppliers-management ')
                                </a>
                                <ul class="drop_main_menu">
                                    <li><a href="{{ route('supplier.create') }}">@lang('messages.master.add_new ')</a></li>
                                    <li><a href="{{ route('supplier.index') }}">@lang('messages.master.view_all ')</a></li>
                                </ul>
                            </li>

                            <li>
                                <a href="#">
                                    <i class="bi bi-tags me-1"></i>@lang('messages.master.product_classification')
                                </a>
                                <ul class="drop_main_menu">
                                    <li><a href="{{ route('productCategory.create') }}">@lang('messages.master.add_new ')</a></li>
                                    <li><a href="{{ route('productCategory.index') }}">@lang('messages.master.view_all ')</a></li>
                                </ul>
                            </li>

                            <li>
                                <a href="#">
                                    <i class="bi bi-box-seam me-1"></i>@lang('messages.master.product_management')
                                </a>
                                <ul class="drop_main_menu">
                                    <li><a href="{{ route('product.create') }}">@lang('messages.master.add_new ')</a></li>
                                    <li><a href="{{ route('product.index') }}">@lang('messages.master.view_all ')</a></li>
                                </ul>
                            </li>

                            <li>
                                <a href="#">
                                    <i class="bi bi-receipt me-1"></i>@lang('messages.master.bills')
                                </a>
                                <ul class="drop_main_menu">
                                    <li><a href="{{ route('bill.create') }}">@lang('messages.master.add_new ')</a></li>
                                    <li><a href="{{ route('bill.index') }}">@lang('messages.master.view_all ')</a></li>
                                </ul>
                            </li>


                            <li><a href="#"><i class="bi bi-cart-plus me-1"></i> @lang('messages.master.purchasing')</a></li>
                            <li><a href="#"><i class="bi bi-receipt me-1"></i>@lang('messages.master.sales_operations')</a></li>
                            <li><a href="#"><i class="bi bi-boxes me-1"></i>@lang('messages.master.stors_balance')</a></li>
                            <li><a href="#"><i class="bi bi-credit-card-2-front me-1"></i>@lang('messages.master.pos')</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="#">
                            <i class="bi bi-images me-2"></i>@lang('messages.master.photo_album')
                        </a>
                        <ul class="drop_main_menu">
                            <li><a href="">@lang('messages.master.add_new ')</a></li>
                            <li><a href="">@lang('messages.master.view_all ')</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
        <!--/End side bar main menu-->

    </div>

    @yield('content')

</div>

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script type="text/javascript" src="{{ asset('/js/jquery-2.1.4.min.js') }}"></script>
<script src="{{ asset('/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('/js/js.js') }}"></script>
<script src="{{ asset('libs/sweetalert2/sweetalert2@11.js') }}"></script>
<!-- SweetAlert2 JS -->
{{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}


<!-- تفعيل tooltips بوتستراب 5 -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>


@stack('scripts')

</body>
</html>
