<table class="table table-bordered table-striped table-hover text-center">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">@lang('messages.productCategory.name')</th>
            <th scope="col">@lang('messages.productCategory.description')</th>
            <th scope="col">@lang('messages.productCategory.parent_id')</th>
            <th scope="col">@lang('messages.productCategory.image')</th>
            <th scope="col"></th>
        </tr>
    </thead>
    <tbody class="table-group-divider">
        @forelse ($productCategorys as $productCategory)
            <tr>
                <th scope="row">{{ $productCategory->id }}</th>
                <td>{{ $productCategory->name }}</td>
                <td>{{ $productCategory->description }}</td>
                <td>{{ $productCategory->parent_id ?? '-' }}</td>
                <td>
                    @if($productCategory->image_path)
                        <img src="{{ asset('storage/' . $productCategory->image_path) }}" alt="{{ $productCategory->name }}" width="50" height="50">
                    @else
                        -
                    @endif
                </td>
                <td>
                    <div class="d-flex justify-content-center align-items-center gap-2">
                        <a href="#" class="btn btn-info btn-sm">@lang('messages.view')</a>
                        <a href="{{ route('productCategory.edit', ['productCategory'=>$productCategory->id , 'page'=>request()->get('page')] ) }}" class="btn btn-primary btn-sm">@lang('messages.edit')</a>
                        <form action="{{ route('productCategory.destroy', ['productCategory'=>$productCategory->id , 'page'=>request()->get('page')]) }}" method="POST" onsubmit="return confirm('@lang('messages.are_you_sure')')" style="display:inline;">
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
{{ $productCategorys->appends(request()->all())->links() }}
