<table class="table table-bordered table-striped table-hover text-center">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">@lang('messages.pos.pos_number')</th>
            <th scope="col">@lang('messages.customer.customer_name')</th>
            <th scope="col">@lang('messages.employee.employee_name')</th>
            <th scope="col">@lang('messages.pos.total_amount')</th>
            <th scope="col">@lang('messages.pos.discount')</th>
            <th scope="col">@lang('messages.pos.net_amount')</th>
            <th scope="col">@lang('messages.pos.payment_status')</th>
            <th scope="col">@lang('messages.pos.is_closed_with_cashbox')</th>
            <th scope="col"></th>
        </tr>
    </thead>
    <tbody class="table-group-divider">
        @php
            $id =1;
        @endphp
        @forelse ($posBills as $posBill)
            <tr>
                <th scope="row">{{ $id++ }}</th>
                <td>{{ $posBill->id }}</td>
                <td>
                    {{ $posBill->customer->name ?? '-' }}

                </td>
                <td>{{ $posBill->employee->name ?? '-' }}</td>
                <td>{{ $posBill->total_amount}}</td>
                <td>{{ $posBill->discount }}</td>
                <td>{{ $posBill->net_amount }}</td>
                <td>{{ $posBill->payment_status }}</td>
                <td>{{ $posBill->is_closed_with_cashbox }}</td>
                            {{-- {{ route('product.edit',['product'=>$product->id ,'page'=>request()->get('page')] ) }} --}}
                           {{-- {{ route('product.destroy', ['product'=>$product->id ,'page'=>request()->get('page')]) }} --}}
                <td>
                    <div class="d-flex justify-content-center align-items-center gap-2">
                        <a href="#" class="btn btn-info btn-sm">@lang('messages.view')</a>

                        <a href=""
                        class="btn btn-primary btn-sm">@lang('messages.edit')</a>

                        <form action=""
                            method="POST"
                            onsubmit="return confirm('Are you sure?')"
                            class="d-inline-block m-0 p-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">@lang('messages.delete')</button>
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
{{ $posBills->appends(request()->all())->links() }}
