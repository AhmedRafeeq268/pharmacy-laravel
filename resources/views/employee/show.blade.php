@extends('layouts.master')

@section('title', __('messages.employee_details'))

@section('content')

<div class="main_content_container py-5">
    <div class="main_container main_menu_open">

        {{-- العنوان --}}
        <div class="mb-5 text-center mt-4">
            <h1 class="display-5 fw-bold text-primary">@lang('messages.employee_details')</h1>
        </div>

        {{-- جدول البيانات --}}
        <table class="table table-striped table-bordered align-middle" style="border-radius: 10px; overflow: hidden;">
            <tbody>
                <tr>
                    <th class="w-25 text-end bg-light">ID</th>
                    <td>{{ $employee->id }}</td>
                </tr>
                <tr>
                    <th class="text-end bg-light">@lang('messages.employee.employee_name')</th>
                    <td>{{ $employee->name }}</td>
                </tr>
                <tr>
                    <th class="text-end bg-light">@lang('messages.employee.phone_number')</th>
                    <td>{{ $employee->phone }}</td>
                </tr>
                <tr>
                    <th class="text-end bg-light">@lang('messages.employee.email')</th>
                    <td>{{ $employee->email }}</td>
                </tr>
                <tr>
                    <th class="text-end bg-light">@lang('messages.employee.id_card')</th>
                    <td>{{ $employee->id_card }}</td>
                </tr>

                {{-- بيانات الحساب البنكي --}}
                <tr>
                    <th class="text-end bg-light">@lang('messages.employee.bank_account_number')</th>
                    <td>{{ $employee->bankAccount->IPAN ?? '-' }}</td>
                </tr>
                <tr>
                    <th class="text-end bg-light">@lang('messages.employee.bank_name')</th>
                    <td>
                        {{$employee->bankAccount->bank->desc_ar ?? '-' }}
                    </td>
                </tr>

                {{-- بيانات المحفظة --}}
                <tr>
                    <th class="text-end bg-light">@lang('messages.employee.wallet_phone_number')</th>
                    <td>{{ $employee->bankAccount->wallet_phone_number ?? '-' }}</td>
                </tr>
                <tr>
                    <th class="text-end bg-light">@lang('messages.employee.wallet_type')</th>
                    <td>
                        {{$employee->bankAccount->wallet->desc_ar ?? '-'}}
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- أزرار التحكم --}}
        <div class="d-flex justify-content-center gap-3 mt-4 flex-wrap">
            <a href="{{ route('employee.index') }}" class="btn btn-outline-primary btn-lg px-4">
                <i class="bi bi-arrow-left"></i> @lang('messages.back_to_list')
            </a>
            <a href="{{ route('employee.edit', $employee->id) }}" class="btn btn-primary btn-lg px-4">
                <i class="bi bi-pencil"></i> @lang('messages.edit')
            </a>
        </div>

    </div>
</div>

@endsection
