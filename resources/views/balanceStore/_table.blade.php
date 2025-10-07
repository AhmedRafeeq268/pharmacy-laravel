<div class="w-90 mt-3">
    <table class="table table-bordered table-striped table-hover text-center">
        <thead >
            <tr>
                <th scope="col">#</th>
                <th scope="col">product_id</th>
                <th scope="col">product_name</th>
                <th scope="col">production_date</th>
                <th scope="col">exp_date</th>
                <th scope="col">quantity</th>
                <th scope="col">manufacture</th>
                <th scope="col">unity_price</th>
                <th scope="col">remaining_quantity</th>
            </tr>
        </thead>
        <tbody class="table-group-divider">
            @forelse ($balances as $b)
                <tr>
                    <th scope="row">{{$b->id }}</th>
                    <td>{{ $b->product_id ?? '' }}</td>
                    <td>{{ $b->product_name ?? '' }}</td>
                    <td>{{ $b->production_date ?? '' }}</td>
                    <td>{{ $b->exp_date ?? ''}}</td>
                    <td>{{ $b->quantity ?? ''}}</td>
                    <td>{{ $b->manufacture ?? ''}}</td>
                    <td>{{ $b->unity_price ?? ''}}</td>
                    <td>{{ $b->remaining_quantity ?? ''}}</td>

                </tr>
                @empty
                    <tr>
                        <td colspan="10">@lang('messages.no_results_found')</td>
                    </tr>
            @endforelse
        </tbody>
    </table>

    {{ $balances->appends(request()->all())->links() }}
</div>
