<table class="table table-bordered table-striped table-hover text-center">
    <thead >
        <tr>
            <th scope="col">#</th>
            <th scope="col">@lang('messages.customer.name')</th>
            <th scope="col">@lang('messages.customer.phone')</th>
            <th scope="col">@lang('messages.customer.email')</th>
            <th scope="col">@lang('messages.customer.id_card')</th>
            <th scope="col">@lang('messages.customer.status')</th>
            <th scope="col">@lang('messages.customer.address')</th>
            <th scope="col"></th>
        </tr>
    </thead>
    <tbody class="table-group-divider">
        @forelse ($customers as $customer)
            <tr>
                <th scope="row">{{$customer->id }}</th>
                <td>{{ $customer->name }}</td>
                <td>{{ $customer->phone }}</td>
                <td>{{ $customer->email }}</td>
                <td>{{ $customer->id_card }}</td>
                <td>{{ $customer->status_cd }}</td>
                <td>{{ $customer->address_details }}</td>
                <td>
                    <div class="d-flex justify-content-center align-items-center gap-2">
                        <a href="#" class="btn btn-info btn-sm">@lang('messages.view')</a>

                        <a href="{{ route('customer.edit', ['customer' => $customer->id, 'page' => request()->get('page')]) }}"
                        class="btn btn-primary btn-sm">@lang('messages.edit')</a>

                        <form action="{{ route('customer.destroy', ['customer' => $customer->id, 'page' => request()->get('page')]) }}"
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
{{ $customers->appends(request()->all())->links() }}
