<table class="table table-bordered table-striped table-hover text-center">
    <thead >
        <tr>
            <th scope="col">#</th>
            <th scope="col">@lang('messages.damaged.prodact_name')</th>
            <th scope="col">@lang('messages.damaged.damaged_quantity')</th>
            <th scope="col">@lang('messages.damaged.damage_reason')</th>
            <th scope="col">@lang('messages.damaged.reported_by')</th>
            <th scope="col"></th>
        </tr>
    </thead>
    <tbody class="table-group-divider">
        @forelse ($damagedItems as $damagedItem)
            <tr>
                <th scope="row">{{$damagedItem->id }}</th>
                <td>{{ $damagedItem->product->name?? '-' }}</td>
                <td>{{ $damagedItem->quantity }}</td>
                <td>{{ $damagedItem->reason }}</td>
                <td>{{ $damagedItem->user->name }}</td>
                <td>
                    <div class="d-flex justify-content-center align-items-center gap-2">
                        <a href="{{ route('damaged.show',['damagedItemId' => $damagedItem->id]) }}" class="btn btn-info btn-sm">@lang('messages.view')</a>

                        <a href="{{ route('damaged.edit', ['damagedItem' => $damagedItem->id, 'page' => request()->get('page')]) }}"
                        class="btn btn-primary btn-sm">@lang('messages.edit')</a>

                        <form action="{{ route('damaged.destroy', ['damagedItem' => $damagedItem->id, 'page' => request()->get('page')]) }}"
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
{{ $damagedItems->appends(request()->query())->links() }}

