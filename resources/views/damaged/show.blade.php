@extends('layouts.master')

@section('title', __('messages.damaged_details'))

@section('content')

<div class="main_content_container py-5">
    <div class="main_container  main_menu_open">

        {{-- العنوان --}}
        <div class="mb-5 text-center mt-4">
            <h1 class="display-5 fw-bold text-primary">@lang('messages.damaged_details')</h1>
            {{-- <hr class="mx-auto" style="width: 80px; border-top: 3px solid #0d6efd;"> --}}
        </div>

        {{-- جدول البيانات مع تباعد وحواف --}}
        <table class="table table-striped table-bordered align-middle" style="border-radius: 10px; overflow: hidden;">
            <tbody>
                <tr>
                    <th class="w-25 text-end bg-light">id</th>
                    <td>{{ $damagedItem->id }}</td>
                </tr>
                <tr>
                    <th class="text-end bg-light">@lang('messages.damaged.prodact_name')</th>
                    <td>{{ $damagedItem->product->name }}</td>
                </tr>
                <tr>
                    <th class="text-end bg-light">@lang('messages.product.manufacturer')</th>
                    <td>{{ $damagedItem->product->manufacture_company }}</td>
                </tr>
                <tr>
                    <th class="text-end bg-light">@lang('messages.damaged.damaged_quantity')</th>
                    <td>{{ $damagedItem->quantity }}</td>
                </tr>
                <tr>
                    <th class="text-end bg-light">@lang('messages.product.unit_price')</th>
                    <td>{{ $damagedItem->product->unit_price }}</td>
                </tr>
                <tr>
                    <th class="text-end bg-light">@lang('messages.damaged.damage_reason')</th>
                    <td>{{ $damagedItem->reason }}</td>
                </tr>
                <tr>
                    <th class="text-end bg-light">@lang('messages.damaged.reported_by')</th>
                    <td>{{ $damagedItem->user->name }}</td>
                </tr>

            </tbody>
        </table>

        {{-- أزرار التحكم --}}
        <div class="d-flex justify-content-center gap-3 mt-4 flex-wrap">
            <a href="{{ route('damaged.index') }}" class="btn btn-outline-primary btn-lg px-4">
                <i class="bi bi-arrow-left"></i> @lang('messages.back_to_list')
            </a>
            <a href="" class="btn btn-primary btn-lg px-4">
                {{-- {{ route('damaged.edit', $customer->id) }} --}}
                <i class="bi bi-pencil"></i> @lang('messages.edit')
            </a>
        </div>

    </div>
</div>
@endsection
