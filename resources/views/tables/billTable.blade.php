<table class="table table-bordered table-striped table-hover text-center">
    <thead>
        <tr>
            <th>#</th>
            <th>@lang('messages.bill.total_amount')</th>
            <th>@lang('messages.bill.currency_type')</th>
            <th>@lang('messages.bill.bill_number')</th>
            <th>@lang('messages.bill.bill_date')</th>
            <th>@lang('messages.bill.receiving_employee')</th>
            <th>@lang('messages.bill.adopt_bill')</th>
            <th>@lang('messages.bill.authorized_employee')</th>
            <th>@lang('messages.bill.certified_or_not')</th>
            <th></th>
        </tr>
    </thead>
    <tbody class="table-group-divider">
        @forelse ($bills as $bill)
            <tr>
                <th>{{ $bill->id }}</th>
                <td>{{ $bill->total_amount }}</td>
                <td>{{ $bill->currancy_type }}</td>
                <td>{{ $bill->bill_number }}</td>
                <td>{{ date('d-m-Y', strtotime($bill->bill_date)) }}</td>
                <td>{{ $bill->employee_receipt }}</td>
                <td>{{ $bill->adopt_bill }}</td>
                <td>{{ $bill->authorized_employee }}</td>
                <td>{{ $bill->certified_or_not }}</td>
                <td>
                    {{-- أزرار العمليات --}}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="10">@lang('messages.no_results_found')</td>
            </tr>
        @endforelse
    </tbody>
</table>

{{-- الباجينيشن --}}
<div class="d-flex justify-content-center">
    {{ $bills->appends(request()->all())->links() }}
</div>
