@extends('layouts.master')
@section('title','edit Supplier')

@section('content')
@include('layouts.partials.sweet_alert')
<!--Start Main content container-->
<div class="main_content_container">
    <div class="main_container main_menu_open">
        <div class="page_content">
            <h1 class="heading_title" style="margin-top: 90px;">@lang('messages.suppliers.edit_distributor_data')</h1>
            <div class="form">
                <form method="POST" action="{{ route('supplier.update', ['supplier'=>$supplier->id , 'page'=>request()->get('page')]) }}">
                    @csrf
                    @method('put')

                    <div class="row">
                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.suppliers.name')</label>
                            <input type="text" class="form-control" name="name" placeholder=@lang('messages.suppliers.name') value="{{ old('name', $supplier->name) }}">
                            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.suppliers.phone')</label>
                            <input type="number" class="form-control" name="phone" placeholder=@lang('messages.suppliers.phone') value="{{ old('phone', $supplier->phone) }}">
                            @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.suppliers.email')</label>
                            <input type="email" class="form-control" name="email" placeholder=@lang('messages.suppliers.email') value="{{ old('email', $supplier->email) }}">
                            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.suppliers.bank_account')</label>
                            <input type="number" class="form-control" name="bank_account" value="{{ old('bank_account', $supplier->bankAccount->IPAN ?? '') }}">
                            @error('bank_account') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    {{-- السطر الثاني --}}
                    <div class="row mt-3">


                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.suppliers.bank_name')</label>
                            <select name="bank_name" class="form-control">
                                <option value="" disabled {{ old('bank_name', $supplier->bankAccount->bank_cd ?? '') ? '' : 'selected' }}>
                                    @lang('messages.supplier.select_bank')
                                </option>
                                @foreach ($banks as $bank)
                                    <option value="{{ $bank->sub_cd }}"
                                        {{ old('bank_name', $supplier->bankAccount->bank_cd ?? '') == $bank->sub_cd ? 'selected' : '' }}>
                                        {{ $bank->desc_ar }}
                                    </option>
                                @endforeach
                            </select>
                            @error('bank_name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.suppliers.wallet_phone')</label>
                            <input type="number" class="form-control" name="wallet_phone" value="{{ old('wallet_phone', $supplier->bankAccount->wallet_phone_number ?? '') }}">
                            @error('wallet_phone') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.suppliers.wallet_type')</label>
                            <select name="wallet_type" class="form-control">
                                <option value="" disabled {{ old('wallet_type', $supplier->bankAccount->wallet_cd ?? '') ? '' : 'selected' }}>
                                    @lang('messages.supplier.select_wallet')
                                </option>
                                @foreach ($wallets as $wallet)
                                    <option value="{{ $wallet->sub_cd }}"
                                        {{ old('wallet_type', $supplier->bankAccount->wallet_cd ?? '') == $wallet->sub_cd ? 'selected' : '' }}>
                                        {{ $wallet->desc_ar }}
                                    </option>
                                @endforeach
                            </select>
                            @error('wallet_cd') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12 text-dir">
                            <button type="submit" class="btn btn-danger">@lang('messages.save')</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
<!--/End Main content container-->
@endsection
