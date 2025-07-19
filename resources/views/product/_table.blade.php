<table class="table table-bordered table-striped table-hover text-center">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">@lang('messages.product.product_name')</th>
            <th scope="col">@lang('messages.product.manufacturer')</th>
            <th scope="col">@lang('messages.product.product_category')</th>
            <th scope="col">@lang('messages.product.unit_price')</th>
            <th scope="col">@lang('messages.product.product_image')</th>
            <th scope="col"></th>
        </tr>
    </thead>
    <tbody class="table-group-divider">
        @forelse ($products as $product)
            <tr>
                <th scope="row">{{ $product->id }}</th>
                <td>{{ $product->name ?? '-' }}</td>
                <td>{{ $product->manufacture_company ?? '-' }}</td>
                <td>{{ $product->ProductCategory->name ?? '-' }}</td>
                <td>{{ $product->unit_price ?? '-' }}</td>
                <td>
                    @if($product->image_path)
                        <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" width="50" height="50" class="rounded">
                    @else
                        -
                    @endif
                </td>
                <td>
                    <div class="d-flex justify-content-center align-items-center gap-2 flex-wrap">
                        <a href="#" class="btn btn-info btn-sm px-3 btn-sm">@lang('messages.view')</a>

                        <a href="{{ route('product.edit', ['product' => $product->id, 'page' => request()->get('page')]) }}"
                        class="btn btn-primary btn-sm px-3">@lang('messages.edit')</a>

                        <form action="{{ route('product.destroy', ['product' => $product->id, 'page' => request()->get('page')]) }}"
                            method="POST"
                            onsubmit="return confirm('@lang('messages.are_you_need_print_bill')')"
                            class="d-inline m-0 p-0 btn-sm">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm px-3">@lang('messages.delete')</button>
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

{{ $products->appends(request()->all())->links() }}
