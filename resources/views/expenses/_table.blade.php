<table class="table table-bordered table-striped table-hover text-center">
    <thead >
        <tr>
            <th scope="col">#</th>
            <th scope="col">@lang('messages.expenses.type')</th>
            <th scope="col">@lang('messages.expenses.description')</th>
            <th scope="col">@lang('messages.expenses.amount')</th>
            <th scope="col">@lang('messages.expenses.expense_date')</th>
            <th scope="col">@lang('messages.expenses.created_by')</th>
            <th scope="col"></th>
        </tr>
    </thead>
    <tbody class="table-group-divider">
        @forelse ($expenses as $expense)
            <tr>
                <th scope="row">{{$expense->id }}</th>
                <td>{{ $expense->type }}</td>
                <td>{{ $expense->description }}</td>
                <td>{{ $expense->amount }}</td>
                <td>{{ $expense->expense_date }}</td>
                <td>{{ $expense->user? $expense->user->name : '-' }}</td>

                <td>
                    <div class="d-flex justify-content-center align-items-center gap-2">
                        <a href="{{ route('expenses.show',['expense' => $expense->id, 'page' => request()->get('page')]) }}" class="btn btn-info btn-sm">@lang('messages.view')</a>

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
{{ $expenses->appends(request()->all())->links() }}
