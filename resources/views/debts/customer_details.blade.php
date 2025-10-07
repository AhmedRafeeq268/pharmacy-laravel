@extends('layouts.master')

@section('title',__('messages.debts.debt_details'))

@section('content')
@include('layouts.partials.sweet_alert')

<div class="main_content_container">
    <div class="main_container main_menu_open">
        <div class="page_content">
            <h1 class="heading_title" style="margin-top: 90px;">
                @lang('messages.debts.customer_debt_details')
            </h1>
            {{-- <h4 class="text-center text-muted mb-4">{{ $customer->name }}</h4> --}}
            {{-- اسم الزبون --}}
            <h5 class="text-center text-muted mb-3" style="font-weight: 500;">
                {{ $customer->name }}
            </h5>

                <div class="mb-3 text-center">
                    <h4>
                        @lang('messages.debts.total_remaining'):
                        <span class="badge bg-primary">
                            {{ number_format($total_remaining, 2) }}
                            <span style="font-size: 0.9rem;">₪</span>
                        </span>

                    </h4>
                </div>

            {{-- <div class="alert alert-success text-center py-2" style="max-width: 300px; margin: 15px auto; font-size: 1rem;">
                <strong>@lang('messages.debts.total_remaining'):</strong>
                {{ number_format($total_remaining, 2) }} @lang('messages.shekel')
            </div> --}}


            <table class="table table-bordered table-striped table-hover text-center">

                @forelse ($debts as $debt)
                <thead>
                    <tr class="table-primary">
                        <th>#</th>
                        <th>@lang('messages.debts.pos_bill_id')</th>
                        <th>@lang('messages.debts.bill_date')</th>
                        <th>@lang('messages.debts.total_amount')</th>
                        <th>@lang('messages.debts.remaining_amount')</th>
                        <th>@lang('messages.debts.is_paid')</th>
                    </tr>
                </thead>
                <tbody class="table-group-divider">
                        {{-- صف الفاتورة --}}
                        <tr>
                            <th>{{ $debt->id }}</th>
                            <td>{{ $debt->pos_bill_id }}</td>
                            <td>{{ $debt->created_at->format('Y-m-d') }}</td>
                            <td>{{ number_format($debt->total_amount,2) }}</td>
                            <td>{{ number_format($debt->remaining_amount,2) }}</td>
                            <td>{{ $debt->is_paid ? __('messages.yes') : __('messages.no') }}</td>
                        </tr>

                        {{-- صف المنتجات تحت الفاتورة --}}
                        <tr>
                            <td colspan="6">
                                <table class="table table-sm table-bordered text-center mb-0">
                                    <thead>
                                        <tr class="table-secondary">
                                            <th>#</th>
                                            <th>@lang('messages.debts.product_name')</th>
                                            <th>@lang('messages.debts.quantity')</th>
                                            <th>@lang('messages.debts.price')</th>
                                            <th>@lang('messages.debts.total')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($debt->posBill->details as $index => $detail)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $detail->product->name ?? '-' }}</td>
                                                <td>{{ $detail->quantity }}</td>
                                                <td>{{ number_format((($detail->price)/$detail->quantity), 2) }}</td>
                                                <td>{{ number_format($detail->price, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">لا توجد ديون لهذا الزبون.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <a href="{{ route('debts.index') }}" class="btn btn-outline-primary btn-lg px-4 mb-4">
                <i class="bi bi-arrow-left"></i> @lang('messages.back_to_list')
            </a>
        </div>
    </div>
</div>
@endsection
