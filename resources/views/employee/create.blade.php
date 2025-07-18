@extends('layouts.master')
@section('title', __('messages.employee.add_new_employee'))

@section('content')
@include('layouts.partials.sweet_alert')
<!--Start Main content container-->
<div class="main_content_container">
    <div class="main_container main_menu_open">
        <div class="page_content">
            {{-- <div class="mt-5">
                @include('layouts.partials.alerts')
            </div> --}}
    @include('layouts.partials.sweet_alert')


            <h1 class="heading_title" style="margin-top: 90px;">@lang('messages.employee.add_new_employee')</h1>
            <div class="form">
                <form class="form-horizontal" method="POST" action="{{ route('employee.store') }}">
                    @csrf

                    {{-- السطر الأول --}}
                    <div class="row">
                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.employee.employee_name')</label>
                            <input type="text" class="form-control" name="name" value="{{ old('name') }}" placeholder="@lang('messages.employee.employee_name')">
                            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.employee.phone_number')</label>
                            <input type="number" class="form-control" name="phone" value="{{ old('phone') }}" placeholder="@lang('messages.employee.phone_number')">
                            @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.employee.email')</label>
                            <input type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="@lang('messages.employee.email')">
                            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.employee.id_card')</label>
                            <input type="number" class="form-control" name="id_card" value="{{ old('id_card') }}" placeholder="@lang('messages.employee.id_card')">
                            @error('id_card') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    {{-- السطر الثاني --}}
                    <div class="row mt-3">
                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.employee.bank_account_number')</label>
                            <input type="number" class="form-control" name="bank_account" value="{{ old('bank_account') }}" placeholder="@lang('messages.employee.bank_account_number')">
                            @error('bank_account') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.employee.bank_name')</label>
                            <select name="bank_name" class="form-control">
                                <option value="" disabled {{ old('bank_name') ? '' : 'selected' }}>@lang('messages.employee.select_bank')</option>
                                @foreach ($banks as $bank)
                                    <option value="{{ $bank->sub_cd }}" {{ old('bank_name') == $bank->sub_cd ? 'selected' : '' }}>
                                        {{ $bank->desc_ar }}
                                    </option>
                                @endforeach
                            </select>
                            @error('bank_name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.employee.wallet_phone_number')</label>
                            <input type="number" class="form-control" name="wallet_phone" value="{{ old('wallet_phone') }}" placeholder="@lang('messages.employee.wallet_phone_number')">
                            @error('wallet_phone') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.employee.wallet_type')</label>
                            <select name="wallet_type" class="form-control">
                                <option value="" disabled {{ old('wallet_type') ? '' : 'selected' }}>@lang('messages.employee.select_wallet')</option>
                                @foreach ($wallets as $wallet)
                                    <option value="{{ $wallet->sub_cd }}" {{ old('wallet_type') == $wallet->sub_cd ? 'selected' : '' }}>
                                        {{ $wallet->desc_ar }}
                                    </option>
                                @endforeach
                            </select>
                            @error('wallet_type') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    {{-- الأزرار --}}
                    <div class="row mt-4">
                        <div class="col-md-12 text-dir">
                            <button type="submit" class="btn btn-success me-2">@lang('messages.save')</button>
                            <a href="{{ route('employee.index') }}" class="btn btn-info">@lang('messages.employee.show_employees')</a>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
<!--/End Main content container-->
@endsection
