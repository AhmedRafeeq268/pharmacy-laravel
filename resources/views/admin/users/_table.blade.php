<div class="w-90 mt-3">
    <table class="table table-bordered table-striped table-hover text-center">
        <thead >
            <tr>
                <th scope="col">#</th>
                <th scope="col">@lang('messages.admin.name')</th>
                <th scope="col">@lang('messages.admin.email')</th>
                <th scope="col">@lang('messages.admin.role')</th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody class="table-group-divider">
            @php
                $i=1;
            @endphp
            @forelse ($users as $user)
                <tr>
                    <th scope="row">{{$i++ }}</th>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role }}</td>
                    <td>
                        <button type="button"
                            class="btn toggle-status {{ $user->status ? 'btn-success' : 'btn-danger' }}"
                            data-id="{{ $user->id }}">
                            {{ $user->status ? 'مفعل' : 'غير مفعل' }}
                        </button>
                    </td>


                    <td>
                        <div class="d-flex justify-content-center align-items-center gap-2 flex-wrap">
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-info btn-sm">
                                @lang('messages.admin.permissions_management')
                            </a>
                            <form action="{{ route('admin.users.destroy', ['user' => $user->id, 'page' => request()->get('page')]) }}"
                                method="POST"
                                onsubmit="return confirm('Are you sure?')"
                                class="d-inline-block m-0 p-0">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-danger btn-sm px-3">
                                    @lang('messages.delete')
                                </button>
                            </form>

                            <a href="{{ route('admin.extra_permissions.edit', $user->id) }}" class="btn btn-warning btn-sm">
                                @lang('messages.admin.edit_permission')
                            </a>

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

</div>



