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
                    <div class="d-flex justify-content-center align-items-center flex-wrap gap-1">

                        {{-- زر العرض --}}
                        <a href="#" class="btn btn-info btn-sm">@lang('messages.view')</a>

                        {{-- زر التعديل --}}
                        <a href="{{ route('bill.edit', ['bill' => $bill->id, 'page' => request()->get('page')]) }}"
                        class="btn btn-primary btn-sm">@lang('messages.edit')</a>

                        {{-- زر الحذف --}}
                        <form action="{{ route('bill.destroy', ['bill' => $bill->id, 'page' => request()->get('page')]) }}"
                            method="POST"
                            onsubmit="return confirm('@lang('messages.confirm_delete')')"
                            class="d-inline-block m-0 p-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">@lang('messages.delete')</button>
                        </form>

                        {{-- زر اعتماد الفاتورة --}}
                        <a href="{{ route('billCertified.index', ['billId' => $bill->id, 'page' => request()->get('page')]) }}"
                        class="btn btn-success btn-sm">
                        @lang('messages.bill.certified')
                        </a>

                    </div>
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
