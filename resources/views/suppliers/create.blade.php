@extends('layouts.master')
@section('title','Add Supplier')

@section('content')
@include('layouts.partials.sweet_alert')
<!--Start Main content container-->
<div class="main_content_container">
    <div class="main_container main_menu_open">
        <div class="page_content">
            <h1 class="heading_title" style="margin-top: 90px;">@lang('messages.suppliers.add_new_supplier')</h1>
            <div class="form">
                <form method="POST" action="{{ route('supplier.store') }}">
                    @csrf

                    {{-- الصف الأول --}}
                    <div class="row">
                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.suppliers.name')</label>
                            <input type="text" class="form-control" name="name" placeholder=@lang('messages.suppliers.name') value="{{ old('name') }}">
                            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.suppliers.phone')</label>
                            <input type="number" class="form-control" name="phone" placeholder=@lang('messages.suppliers.phone') value="{{ old('phone') }}">
                            @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.suppliers.email')</label>
                            <input type="email" class="form-control" name="email" placeholder=@lang('messages.suppliers.email') value="{{ old('email') }}">
                            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.suppliers.bank_account')</label>
                            <input type="number" class="form-control" name="bank_account" placeholder=@lang('messages.suppliers.bank_account') value="{{ old('bank_account') }}">
                            @error('bank_account') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    {{-- الصف الثاني --}}
                    <div class="row mt-3">
                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.suppliers.bank_name')</label>
                            <select name="bank_name" class="form-control">
                                <option value="" disabled {{ old('bank_name') ? '' : 'selected' }}>@lang('messages.suppliers.select_bank')</option>
                                @foreach ($banks as $bank)
                                    <option value=" {{ $bank->sub_cd }}" @php
                                        if (old('bank_name') == ($bank->desc_ar)) {
                                            'selected';
                                        }
                                        else {
                                            '';
                                        }
                                    @endphp> {{ $bank->desc_ar}}</option>
                                @endforeach

                            </select>
                            @error('bank_name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.suppliers.wallet_phone')</label>
                            <input type="number" class="form-control" name="wallet_phone" placeholder=@lang('messages.suppliers.wallet_phone') value="{{ old('wallet_phone') }}">
                            @error('wallet_phone') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.suppliers.wallet_type')</label>
                                <select name="wallet_type" class="form-control">
                                    <option value="" disabled {{ old('wallet_type') ? '' : 'selected' }}>@lang('messages.suppliers.select_wallet')</option>
                                    @foreach ($wallets as $wallet)
                                        <option value=" {{ $wallet->sub_cd }}"
                                          @php
                                            if (old('wallet_type') == ($wallet->desc_ar)) {
                                                'selected';
                                            }
                                            else {
                                                '';
                                            }
                                          @endphp> {{ $wallet->desc_ar}}</option>
                                    @endforeach

                            </select>
                            @error('wallet_type') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3"></div> {{-- للتوازن --}}
                    </div>

                    {{-- الأزرار --}}
                    <div class="row mt-4">
                        <div class="col-md-12 text-dir">
                            <button type="submit" class="btn btn-success me-2">@lang('messages.save')</button>
                            <a href="{{ route('supplier.index') }}" class="btn btn-info">@lang('messages.suppliers.show_suppliers')</a>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
<!--/End Main content container-->
@endsection
