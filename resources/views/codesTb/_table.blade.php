<div class="w-90 mt-3">
    <table class="table table-bordered table-striped table-hover text-center">
        <thead >
            <tr>
                <th scope="col">#</th>
                <th scope="col">main_cd</th>
                <th scope="col">sub_cd</th>
                <th scope="col">desc_ar</th>
                <th scope="col">desc_en</th>
                <th scope="col">details</th>
                <th scope="col">is_active</th>
                <th scope="col">is_editables</th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody class="table-group-divider">
            @forelse ($codesTb as $codeTb)
                <tr>
                    <th scope="row">{{$codeTb->id }}</th>
                    <td>{{ $codeTb->main_cd }}</td>
                    <td>{{ $codeTb->sub_cd }}</td>
                    <td>{{ $codeTb->desc_ar }}</td>
                    <td>{{ $codeTb->desc_en }}</td>
                    <td>{{ $codeTb->details }}</td>
                    <td>{{ $codeTb->is_active }}</td>
                    <td>{{ $codeTb->is_editables }}</td>
                    <td>
                        <div class="d-flex justify-content-center align-items-center gap-2 flex-wrap">


                            <a href="{{ route('codeTb.show',['codeTb' => $codeTb->id]) }}" class="btn btn-info btn-sm px-3"> @lang('messages.view')</a>

                            <a href="{{ route('codeTb.edit', ['codeTb' => $codeTb->id, 'page' => request()->get('page')]) }}"class="btn btn-primary btn-sm px-3">@lang('messages.edit')</a>

                            <form action="{{ route('codeTb.destroy', ['codeTb' => $codeTb->id, 'page' => request()->get('page')]) }}"
                                method="POST"
                                onsubmit="return confirm('Are you sure?')"
                                class="d-inline-block m-0 p-0">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-danger btn-sm px-3">
                                    @lang('messages.delete')
                                </button>
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

    {{ $codesTb->appends(request()->all())->links() }}
</div>
