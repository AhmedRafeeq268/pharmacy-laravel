@extends('layouts.master')
@section('title', __('messages.expenses.add_expense'))

@section('content')
@include('layouts.partials.sweet_alert')

<div class="main_content_container">
    <div class="main_container main_menu_open">
        <div class="page_content">
            <h1 class="heading_title" style="margin-top: 90px;">@lang('messages.expenses.add_expense')</h1>
            <div class="form">
                <form action="{{ route('expenses.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-3 ">
                            <label class="mb-2">@lang('messages.expenses.expense_type')</label>
                            <select name="type" class="form-control" required>
                                <option value="salary">@lang('messages.expenses.salaries')</option>
                                <option value="rent">@lang('messages.expenses.rent')</option>
                                <option value="bills">@lang('messages.expenses.bills')</option>
                                <option value="other">@lang('messages.expenses.other')</option>
                            </select>
                        </div>

                        <div class="col-md-3 ">
                            <label class="mb-2">@lang('messages.expenses.description')</label>
                            <textarea name="description" class="form-control" rows="1"></textarea>
                        </div>

                        <div class="col-md-3 ">
                            <label class="mb-2">@lang('messages.expenses.amount') (₪)</label>
                            <input type="number" name="amount" step="0.01" class="form-control" required>
                        </div>

                        <div class="col-md-3 ">
                            <label class="mb-2">@lang('messages.expenses.expense_date')</label>
                            <input type="date" name="expense_date" class="form-control" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">@lang('messages.save')</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
