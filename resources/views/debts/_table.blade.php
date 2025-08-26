<table class="table table-bordered table-striped table-hover text-center">
    <thead >
        <tr>
            <th scope="col">#</th>
            <th scope="col">@lang('messages.customer.customer_name')</th>
            <th scope="col">@lang('messages.debts.total_amount')</th>
            <th scope="col">@lang('messages.debts.remaining_amount')</th>
            {{-- <th scope="col">@lang('messages.debts.status')</th> --}}
            <th scope="col">@lang('messages.debts.is_paid')</th>
            <th scope="col"></th>
        </tr>
    </thead>
    <tbody class="table-group-divider">
        @php
            $id=1;
        @endphp
        @forelse ($debts as $debt)
            <tr>
                <th scope="row">{{$id++ }}</th>
                <td>{{ $debt->customer->name }}</td>
                <td>{{ number_format($debt->total_debt,2) }}</td>
                <td>{{ number_format($debt->total_remaining,2) }}</td>
                {{-- <td>{{ $debt->status }}</td> --}}
                <td>{{ $debt->is_paid? __('messages.yes'):__('messages.no') }}</td>
                <td>
                    <div class="d-flex justify-content-center align-items-center gap-2">
                        <a href="{{ route('debts.show', $debt->customer_id) }}" class="btn btn-info btn-sm">@lang('messages.view')</a>
                        {{-- {{ route('customer.show',['customer' => $customer->id]) }} --}}
                        {{-- <a href="" --}}
                        {{-- {{ route('customer.edit', ['customer' => $customer->id, 'page' => request()->get('page')]) }} --}}
                        {{-- class="btn btn-primary btn-sm">@lang('messages.edit')</a> --}}

                        {{-- <form action="{{ route('debts.destroy', ['debt' => $debt->id, 'page' => request()->get('page')]) }}
                        "
                            method="POST"
                            onsubmit="return confirm('Are you sure?')"
                            class="d-inline-block m-0 p-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">@lang('messages.delete')</button>
                        </form> --}}
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
{{ $debts->appends(request()->query())->links() }}

