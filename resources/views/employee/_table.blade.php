<table class="table table-bordered table-striped table-hover text-center">
    <thead >
        <tr>
            <th scope="col">#</th>
            <th scope="col">@lang('messages.employee.employee_name')</th>
            <th scope="col">@lang('messages.employee.phone_number')</th>
            <th scope="col">@lang('messages.employee.email')</th>
            <th scope="col">@lang('messages.employee.id_card')</th>
            <th scope="col">@lang('messages.employee.bank_account_number')</th>
            <th scope="col">@lang('messages.employee.bank_name')</th>
            <th scope="col">@lang('messages.employee.wallet_phone_number')</th>
            <th scope="col">@lang('messages.employee.wallet_type')</th>
            <th scope="col"></th>
        </tr>
    </thead>
    <tbody class="table-group-divider">
        @forelse ($employees as $employee)
            <tr>
                <th scope="row">{{$employee->id }}</th>
                <td>{{ $employee->name }}</td>
                <td>{{ $employee->phone }}</td>
                <td>{{ $employee->email }}</td>
                <td>{{ $employee->id_card }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>
                    <div class="d-flex justify-content-center align-items-center gap-2">
                        <a href="#" class="btn btn-info btn-sm">@lang('messages.view')</a>

                        <a href="{{ route('employee.edit', ['employee' => $employee->id, 'page' => request()->get('page')]) }}"
                        class="btn btn-primary btn-sm">@lang('messages.edit')</a>

                        <form action="{{ route('employee.destroy', ['employee' => $employee->id, 'page' => request()->get('page')]) }}"
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
{{ $employees->appends(request()->all())->links() }}
