<table class="table table-bordered table-striped table-hover text-center">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">@lang('messages.bill.total_amount')</th>
            <th scope="col">@lang('messages.bill.currency_type')</th>
            <th scope="col">@lang('messages.bill.bill_number')</th>
            <th scope="col">@lang('messages.bill.bill_date')</th>
            <th scope="col">@lang('messages.bill.receiving_employee')</th>
            <th scope="col">@lang('messages.bill.adopt_bill')</th>
            <th scope="col">@lang('messages.bill.authorized_employee')</th>
            <th scope="col">@lang('messages.bill.certified_or_not')</th>
            <th scope="col"></th>
        </tr>
    </thead>
    <tbody class="table-group-divider">
        @foreach ($bills as $bill)
            <tr>
                <th scope="row">{{ $bill->id }}</th>
                <td>{{ $bill->total_amount }}</td>
                <td>{{ $bill->currancy_type }}</td>
                <td>{{ $bill->bill_number }}</td>
                <td>{{ date('d-m-Y', strtotime($bill->bill_date)) }}</td>
                <td>{{ $bill->employee_receipt }}</td>
                <td>{{ $bill->adopt_bill }}</td>
                <td>{{ $bill->authorized_employee }}</td>
                <td>{{ $bill->certified_or_not }}</td>
                <td>
                    <div class="d-flex justify-content-center align-items-center gap-2">
                        <a href="#" class="btn btn-info btn-sm">@lang('messages.view')</a>
                        <a href="{{ route('bill.edit', $bill->id) }}" class="btn btn-primary btn-sm">@lang('messages.edit')</a>
                        <form action="{{ route('bill.destroy', $bill->id) }}" method="POST" onsubmit="return confirm('@lang('messages.confirm_delete')')" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">@lang('messages.delete')</button>
                        </form>
                    </div>
                    <a href="{{ route('billCertified.index', $bill->id) }}" class="btn btn-success btn-sm mt-2">@lang('messages.bill.certified')</a>
                    {{-- <a href="" class="btn btn-info btn-sm mt-2">bill details</a> --}}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
