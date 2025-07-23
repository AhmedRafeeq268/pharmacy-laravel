@extends('layouts.master')

@section('title', __('messages.supplier_details'))

@section('content')

<div class="main_content_container py-5">
    <div class="main_container main_menu_open">

        {{-- العنوان --}}
        <div class="mb-5 text-center mt-4">
            <h1 class="display-5 fw-bold text-primary">@lang('messages.supplier_details')</h1>
        </div>

        {{-- جدول البيانات --}}
        <table class="table table-striped table-bordered align-middle" style="border-radius: 10px; overflow: hidden;">
            <tbody>
                <tr>
                    <th class="w-25 text-end bg-light">ID</th>
                    <td>{{ $supplier->id }}</td>
                </tr>
                <tr>
                    <th class="text-end bg-light">@lang('messages.suppliers.name')</th>
                    <td>{{ $supplier->name }}</td>
                </tr>
                <tr>
                    <th class="text-end bg-light">@lang('messages.suppliers.phone')</th>
                    <td>{{ $supplier->phone }}</td>
                </tr>
                <tr>
                    <th class="text-end bg-light">@lang('messages.suppliers.email')</th>
                    <td>{{ $supplier->email }}</td>
                </tr>

                {{-- بيانات الحساب البنكي --}}
                <tr>
                    <th class="text-end bg-light">@lang('messages.suppliers.bank_account')</th>
                    <td>{{ $supplier->bankAccount->IPAN ?? '-' }}</td>
                </tr>
                <tr>
                    <th class="text-end bg-light">@lang('messages.suppliers.bank_name')</th>
                    <td>
                        {{$supplier->bankAccount->bank->desc_ar ?? '-' }}
                    </td>
                </tr>

                {{-- بيانات المحفظة --}}
                <tr>
                    <th class="text-end bg-light">@lang('messages.suppliers.wallet_phone')</th>
                    <td>{{ $supplier->bankAccount->wallet_phone_number ?? '-' }}</td>
                </tr>
                <tr>
                    <th class="text-end bg-light">@lang('messages.suppliers.wallet_type')</th>
                    <td>
                        {{$supplier->bankAccount->wallet->desc_ar ?? '-'}}
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- أزرار التحكم --}}
        <div class="d-flex justify-content-center gap-3 mt-4 flex-wrap">
            <a href="{{ route('supplier.index') }}" class="btn btn-outline-primary btn-lg px-4">
                <i class="bi bi-arrow-left"></i> @lang('messages.back_to_list')
            </a>
            <a href="{{ route('supplier.edit', $supplier->id) }}" class="btn btn-primary btn-lg px-4">
                <i class="bi bi-pencil"></i> @lang('messages.edit')
            </a>
        </div>

    </div>
</div>

@endsection
