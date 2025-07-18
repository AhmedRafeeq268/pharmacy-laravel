@extends('layouts.master')
@section('title', __('messages.employee.edit_employee_data'))

@section('content')
    @include('layouts.partials.sweet_alert')
<!--Start Main content container-->
<div class="main_content_container">
    <div class="main_container main_menu_open">
        <div class="page_content">
            <h1 class="heading_title" style="margin-top: 90px;">@lang('messages.employee.edit_employee_data')</h1>
            <div class="form">
                <form method="POST" action="{{ route('employee.update', ['employee'=>$employee->id ,'page' => request()->get('page')]) }}">
                    @csrf
                    @method('PUT')

                    {{-- السطر الأول --}}
                    <div class="row">
                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.employee.employee_name')</label>
                            <input type="text" class="form-control" name="name" value="{{ old('name', $employee->name) }}">
                            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.employee.phone_number')</label>
                            <input type="number" class="form-control" name="phone" value="{{ old('phone', $employee->phone) }}">
                            @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.employee.email')</label>
                            <input type="email" class="form-control" name="email" value="{{ old('email', $employee->email) }}">
                            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.employee.id_card')</label>
                            <input type="number" class="form-control" name="id_card" value="{{ old('id_card', $employee->id_card) }}">
                            @error('id_card') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    {{-- السطر الثاني --}}
                    <div class="row mt-3">
                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.employee.bank_account_number')</label>
                            <input type="number" class="form-control" name="bank_account" value="{{ old('bank_account', $employee->bank_account) }}">
                            @error('bank_account') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.employee.bank_name')</label>
                            <select name="bank_name" class="form-control">
                                <option value="" disabled {{ old('bank_name', $employee->bank_name) ? '' : 'selected' }}>
                                    @lang('messages.employee.select_bank')
                                </option>
                                @foreach ($banks as $bank)
                                    <option value="{{ $bank->sub_cd }}"
                                        {{ old('bank_name', $employee->bank_name) == $bank->sub_cd ? 'selected' : '' }}>
                                        {{ $bank->desc_ar }}
                                    </option>
                                @endforeach
                            </select>
                            @error('bank_name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.employee.wallet_phone_number')</label>
                            <input type="number" class="form-control" name="wallet_phone" value="{{ old('wallet_phone', $employee->wallet_phone) }}">
                            @error('wallet_phone') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.employee.wallet_type')</label>
                            <select name="wallet_type" class="form-control">
                                <option value="" disabled {{ old('wallet_type', $employee->wallet_type) ? '' : 'selected' }}>
                                    @lang('messages.employee.select_wallet')
                                </option>
                                @foreach ($wallets as $wallet)
                                    <option value="{{ $wallet->sub_cd }}"
                                        {{ old('wallet_type', $employee->wallet_type) == $wallet->sub_cd ? 'selected' : '' }}>
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
                            <button type="submit" class="btn btn-danger me-2">@lang('messages.save')</button>
                            <a href="{{ route('employee.index') }}" class="btn btn-secondary">@lang('messages.back_to_list')</a>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
<!--/End Main content container-->
@endsection
