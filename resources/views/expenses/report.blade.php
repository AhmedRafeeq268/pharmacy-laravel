@extends('layouts.master')
@section('title', __('messages.expenses.expenses_report'))

@section('content')
@include('layouts.partials.sweet_alert')

<div class="main_content_container">
    <div class="main_container main_menu_open">
        <div class="page_content">
            <h1 class="heading_title" style="margin-top: 90px;">@lang('messages.expenses.expenses_report')</h1>
            <div class="form">
                <form method="GET" action="{{ route('expenses.report') }}" class="mb-3">
                    <div class="row">
                        <div class="col-3">
                            <label class="mb-2">@lang('messages.expenses.from_date')</label>
                            <input type="date" name="from" value="{{ request('from') }}" class="form-control">
                        </div>
                        <div class="col-3">
                            <label class="mb-2">@lang('messages.expenses.to_date')</label>
                            <input type="date" name="to" value="{{ request('to') }}" class="form-control">
                        </div>
                    </div>
                    <div class="col-1">
                        <button type="submit" class="btn btn-success w-100 mt-3">@lang('messages.expenses.show')</button>
                    </div>
                </form>
            </div>
            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>@lang('messages.expenses.date')</th>
                        <th>@lang('messages.expenses.type')</th>
                        <th>@lang('messages.expenses.description')</th>
                        <th>@lang('messages.expenses.amount') (₪)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($expenses as $expense)
                        <tr>
                            <td>{{ $expense->expense_date }}</td>
                            <td>{{ __('messages.expenses.'.$expense->type) }}</td>
                            <td>{{ $expense->description }}</td>
                            <td>{{ number_format($expense->amount, 2) }}</td>
                        </tr>
                    @endforeach
                    <tr class="table-primary">
                        <td colspan="3" class="text-center"><strong>@lang('messages.expenses.total')</strong></td>
                        <td><strong>{{ number_format($total, 2) }} ₪</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
