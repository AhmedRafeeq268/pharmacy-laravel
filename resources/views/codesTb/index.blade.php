
@extends('layouts.master')
@section('title','Codes Tb')

@section('content')
@include('layouts.partials.sweet_alert')
    <!--Start Main content container-->
    <div class="main_content_container">
        <div class="main_container  main_menu_open">
            <!--Start system bath-->
            <div class="home_pass hidden-xs">
                <ul>
                    <li class="bring_right"><span class="glyphicon glyphicon-home "></span></li>
                    <li class="bring_right"><a href="">الصفحة الرئيسية للوحة تحكم الموقع</a></li>
                </ul>
            </div>
            <div class="w-100 mt-5">

                <input type="text" id="searchInput" class="form-control w-50 mx-auto"  placeholder="@lang('messages.codesTb.search_desc')">

                <div id="codesTbTable">
                    @include('codesTb._table', ['codesTb'=>$codesTb])
                </div>

            </div>
            <!--/End system bath-->
            {{-- <div class="w-90 mt-5">
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
                        @foreach ($codesTb as $codeTb)
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
                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                        <a href="#" class="btn btn-info btn-sm">@lang('messages.view')</a>
                                        <a href="{{ route('codeTb.edit',['codeTb'=>$codeTb->id ,'page'=>request()->get('page')]) }}" class="btn btn-primary btn-sm">@lang('messages.edit')</a>
                                        <form action="{{ route('codeTb.destroy',['codeTb'=>$codeTb->id ,'page'=>request()->get('page')]) }}" method="POST" onsubmit="return confirm('Are you sure?')" style="display:inline;">

                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">@lang('messages.delete')</button>
                                        </form>
                                    </div>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{ $codesTb->appends(request()->all())->links() }}
            </div> --}}
            <div class="quick_links text-center">
                <a href="{{ route('codeTb.create') }}" class="btn text-white  py-3" style="background-color: #d35400">
                    <h5 class="mb-0 text-white">@lang('messages.codesTb.add_new_constant')</h5>
                </a>
            </div>

        </div>
        <!--/End Main content container-->


    </div>
    <!--/End body container section-->
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchInput');

        searchInput.addEventListener('keyup', function () {
            let search = this.value;

            fetch(`{{ route('codeTb.index') }}?search=${search}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(data => {
                document.getElementById('codesTbTable').innerHTML = data;
            });
        });
    });
</script>

