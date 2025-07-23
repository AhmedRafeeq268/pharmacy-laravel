<table class="table table-bordered table-striped table-hover text-center">
    <thead >
        <tr>
            <th scope="col">#</th>
            <th scope="col">@lang('messages.suppliers.name')</th>
            <th scope="col">@lang('messages.suppliers.phone')</th>
            <th scope="col">@lang('messages.suppliers.email')</th>
            <th scope="col">@lang('messages.suppliers.bank_account')</th>
            <th scope="col">@lang('messages.suppliers.bank_name')</th>
            <th scope="col">@lang('messages.suppliers.wallet_phone')</th>
            <th scope="col">@lang('messages.suppliers.wallet_type')</th>
            <th scope="col"></th>
        </tr>
    </thead>
    <tbody class="table-group-divider">
        @forelse ($suppliers as $supplier)
            <tr>
                <th scope="row">{{$supplier->id }}</th>
                <td>{{ $supplier->name }}</td>
                <td>{{ $supplier->phone }}</td>
                <td>{{ $supplier->email }}</td>
                <td>{{ $supplier->bankAccount->IPAN ?? '-' }}</td>
                <td>{{ $supplier->bankAccount->bank->desc_ar ?? '-' }}</td>
                <td>{{ $supplier->bankAccount->wallet_phone_number ?? '-' }}</td>
                <td>{{ $supplier->bankAccount->wallet->desc_ar ?? '-' }}</td>
                <td>
                    <div class="d-flex justify-content-center align-items-center gap-2">
                        <a href="{{ route('supplier.show',['supplier'=>$supplier->id]) }}" class="btn btn-info btn-sm">@lang('messages.view')</a>
                        <a href="{{ route('supplier.edit',['supplier'=>$supplier->id , 'page'=>request()->get('page')] ) }}" class="btn btn-primary btn-sm">@lang('messages.edit')</a>
                        <form action="{{ route('supplier.destroy',['supplier'=>$supplier->id , 'page'=>request()->get('page')]) }}" method="POST" onsubmit="return confirm('Are you sure?')" style="display:inline;">
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
{{ $suppliers->appends(request()->all())->links() }}
