@extends('layouts.master')

@section('title', __('messages.customer_details'))

@section('content')

<div class="main_content_container py-5">
    <div class="main_container  main_menu_open">

        {{-- العنوان --}}
        <div class="mb-5 text-center mt-4">
            <h1 class="display-5 fw-bold text-primary">@lang('messages.customer_details')</h1>
            {{-- <hr class="mx-auto" style="width: 80px; border-top: 3px solid #0d6efd;"> --}}
        </div>

        {{-- جدول البيانات مع تباعد وحواف --}}
        <table class="table table-striped table-bordered align-middle" style="border-radius: 10px; overflow: hidden;">
            <tbody>
                <tr>
                    <th class="w-25 text-end bg-light">id</th>
                    <td>{{ $customer->id }}</td>
                </tr>
                <tr>
                    <th class="text-end bg-light">@lang('messages.customer.name')</th>
                    <td>{{ $customer->name }}</td>
                </tr>
                <tr>
                    <th class="text-end bg-light">@lang('messages.customer.phone')</th>
                    <td>{{ $customer->phone }}</td>
                </tr>
                <tr>
                    <th class="text-end bg-light">@lang('messages.customer.email')</th>
                    <td>{{ $customer->email }}</td>
                </tr>
                <tr>
                    <th class="text-end bg-light">@lang('messages.customer.id_card')</th>
                    <td>{{ $customer->id_card }}</td>
                </tr>
                <tr>
                    <th class="text-end bg-light">@lang('messages.customer.status')</th>
                    <td>{{ $customer->status_cd }}</td>
                </tr>
                <tr>
                    <th class="text-end bg-light">@lang('messages.customer.address')</th>
                    <td>{{ $customer->address }}</td>
                </tr>
                <tr>
                    <th class="text-end bg-light">@lang('messages.customer.address_details')</th>
                    <td>{{ $customer->address_details }}</td>
                </tr>

            </tbody>
        </table>

        {{-- أزرار التحكم --}}
        <div class="d-flex justify-content-center gap-3 mt-4 flex-wrap">
            <a href="{{ route('customer.index') }}" class="btn btn-outline-primary btn-lg px-4">
                <i class="bi bi-arrow-left"></i> @lang('messages.back_to_list')
            </a>
            <a href="{{ route('customer.edit', $customer->id) }}" class="btn btn-primary btn-lg px-4">
                <i class="bi bi-pencil"></i> @lang('messages.edit')
            </a>
        </div>

    </div>
</div>
@endsection
