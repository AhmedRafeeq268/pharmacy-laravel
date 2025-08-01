<table class="table table-bordered table-striped table-hover text-center">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">@lang('messages.purchaseReturns.purchase_bill_id')</th>
            <th scope="col">@lang('messages.purchaseReturns.session_id')</th>
            <th scope="col">@lang('messages.purchaseReturns.supplier_name')</th>
            <th scope="col">@lang('messages.purchaseReturns.product_name')</th>
            <th scope="col">@lang('messages.purchaseReturns.quantity')</th>
            <th scope="col">@lang('messages.purchaseReturns.return_amount')</th>
            <th scope="col">@lang('messages.purchaseReturns.reason')</th>
            <th scope="col">@lang('messages.purchaseReturns.refunded_in_cash')</th>
            <th scope="col">@lang('messages.purchaseReturns.created_by')</th>
            <th scope="col">@lang('messages.purchaseReturns.edited_by')</th>
            <th scope="col"></th>
        </tr>
    </thead>
    <tbody class="table-group-divider">
        @forelse ($purchaseReturns as $purchaseReturn)
            <tr>
                <th scope="row">{{ $purchaseReturn->id }}</th>
                <td>{{ $purchaseReturn->purchase_bill_id }}</td>
                <td>{{ $purchaseReturn->session_id }}</td>
                <td>{{ $purchaseReturn->supplier->name }}</td>
                <td>{{ $purchaseReturn->product->name }}</td>
                <td>{{ $purchaseReturn->quantity }}</td>
                <td>{{ $purchaseReturn->return_amount }}</td>
                <td>{{ $purchaseReturn->reason }}</td>
                <td>{{ $purchaseReturn->refunded_in_cash? 'yes' : 'no' }}</td>
                <td>{{ $purchaseReturn->creator ? $purchaseReturn->creator->name : '-' }}</td>
                <td>{{ $purchaseReturn->editor ? $purchaseReturn->editor->name : '-'}}</td>


                <td>
                    <div class="d-flex justify-content-center align-items-center gap-2 flex-wrap">
                        <a href="{{ route('purchaseReturns.show',['purchaseReturn' =>$purchaseReturn->id]) }}" class="btn btn-info btn-sm px-3 btn-sm">@lang('messages.view')</a>

                        <a href="{{ route('purchaseReturns.edit', ['purchaseReturn' => $purchaseReturn->id, 'page' => request()->get('page')]) }}"
                        class="btn btn-primary btn-sm px-3">@lang('messages.edit')</a>

                        <form action="{{ route('purchaseReturns.destroy', ['purchaseReturn' => $purchaseReturn->id, 'page' => request()->get('page')]) }}"
                            method="POST"
                            onsubmit="return confirm('Are you sure?')"
                            class="d-inline m-0 p-0 btn-sm">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm px-3">@lang('messages.delete')</button>
                        </form>
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

{{ $purchaseReturns->appends(request()->all())->links() }}
