@extends('layouts.master')
@section('title', __('messages.bill.view_bills'))

@section('content')
@include('layouts.partials.sweet_alert')
<div class="main_content_container">
    <div class="main_container main_menu_open">

        <div class="home_pass hidden-xs">
            <ul>
                <li class="bring_right"><span class="glyphicon glyphicon-home"></span></li>
                <li class="bring_right"><a href="">@lang('messages.home')</a></li>
            </ul>
        </div>

        <div class="w-100 mt-5">

            {{-- @if(session('success'))
                <div class="alert alert-success text-center">
                    {{ session('success') }}
                </div>
            @endif --}}

            <input type="text" id="searchInput" class="form-control mb-3 w-50 mx-auto"  placeholder="@lang('messages.bill.search_bill_number')">

            <div id="billsTable">
                @include('bill._table', ['bills' => $bills])
            </div>

            {{-- <div class="d-flex justify-content-center">
                {{ $bills->appends(request()->all())->links() }}
            </div> --}}
        </div>

        <div class="quick_links text-center">
            <a href="{{ route('bill.create') }}" class="btn text-white py-3" style="background-color: #d35400">
                <h6 class="mb-0 text-white">@lang('messages.bill.add_new_bill')</h6>
            </a>
        </div>
    </div>
</div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchInput');

        searchInput.addEventListener('keyup', function () {
            let search = this.value;

            fetch(`{{ route('bill.index') }}?search=${search}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(data => {
                document.getElementById('billsTable').innerHTML = data;
            });
        });
    });
</script>
