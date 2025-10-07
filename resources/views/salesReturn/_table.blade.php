<table class="table table-bordered table-striped table-hover text-center">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">@lang('messages.salesReturn.pos_bill_number')</th>
            <th scope="col">@lang('messages.salesReturn.customer_name')</th>
            <th scope="col">@lang('messages.salesReturn.total')</th>
            <th scope="col">@lang('messages.salesReturn.refund_method')</th>
            <th scope="col">@lang('messages.salesReturn.product_name')</th>
            <th scope="col">@lang('messages.salesReturn.price')</th>
            <th scope="col">@lang('messages.salesReturn.quantity')</th>
            <th scope="col">@lang('messages.salesReturn.subtotal')</th>
            <th scope="col"></th>
        </tr>
    </thead>
    <tbody class="table-group-divider">
        @forelse ($salesReturns as $salesReturn)

            @foreach($salesReturn->details as $detail)
                <tr>
                    <th scope="row">{{ $salesReturn->id }}</th>
                    <td>{{ $salesReturn->pos_bill_id }}</td>
                    <td>{{ $salesReturn->customer ? $salesReturn->customer->name : '-' }}</td>
                    <td>{{ $salesReturn->total }}</td>
                    <td>{{ $salesReturn->refund_method }}</td>

                    {{-- اسم المنتج بدل ID --}}
                    <td>{{ $detail->product ? $detail->product->name : '-' }}</td>
                    <td>{{ $detail->price }}</td>
                    <td>{{ $detail->quantity }}</td>
                    <td>{{ $detail->subtotal }}</td>
                    <td>
                        <div class="d-flex justify-content-center align-items-center gap-2">
                            <a href="{{ route('salesReturn.show',['id' => $salesReturn->id]) }}" class="btn btn-info btn-sm">@lang('messages.view')</a>
                        </div>
                    </td>
                </tr>
            @endforeach

        @empty
            <tr>
                <td colspan="10">@lang('messages.no_results_found')</td>
            </tr>
        @endforelse


    </tbody>
</table>

{{ $salesReturns->appends(request()->all())->links() }}
