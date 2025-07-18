<div class="table-responsive mt-4">
    <table class="table table-bordered table-striped table-hover text-center">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>@lang('messages.pos.product_name')</th>
                <th>@lang('messages.pos.unit_price')</th>
                <th>@lang('messages.pos.quantity')</th>
                <th>@lang('messages.pos.total_amount')</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @php
                $index =1;
            @endphp
            @forelse ($posBillsDetails as $posBillDetails)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $posBillDetails->product->name }}</td>
                    <td>{{ $posBillDetails->unit_price }}</td>
                    <td>{{ $posBillDetails->quantity }}</td>
                    <td>{{ $posBillDetails->price }}</td>
                    <td>
                        <div class="d-flex justify-content-center gap-2">
                            {{-- <a href="#" class="btn btn-info btn-sm">@lang('messages.view')</a> --}}
                            {{-- زر تعديل (اختياري) --}}
                            {{-- <a href="{{ route('billDetails.edit', $posBillDetails->id) }}" class="btn btn-primary btn-sm">@lang('messages.edit')</a> --}}

                            {{-- زر حذف --}}
                            {{-- <form action="{{ route('billDetails.destroy', $posBillDetails->id) }}" method="POST" onsubmit="return confirm('@lang('messages.confirm_delete')')" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">@lang('messages.delete')</button>
                            </form> --}}
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">@lang('messages.no_results_found')</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
