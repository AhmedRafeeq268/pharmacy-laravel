
@extends('layouts.master')
@section('title',__('messages.admin.users'))

@section('content')
@include('layouts.partials.sweet_alert')
    <!--Start Main content container-->
    <div class="main_content_container">
        <div class="main_container  main_menu_open">

            <div class="w-100 mt-5">

                <input type="text" id="searchInput" class="form-control w-50 mx-auto"  placeholder="@lang('messages.admin.search_user')">

                <div id="usersTable">
                    @include('admin.users._table', ['users'=>$users])
                </div>

            </div>
            <div class="quick_links text-center">
                <a href="{{ route('admin.users.create') }}" class="btn text-white  py-3" style="background-color: #d35400">
                    <h5 class="mb-0 text-white">@lang('messages.admin.add_new_users')</h5>
                </a>
            </div>

        </div>
        <!--/End Main content container-->
    </div>
    <!--/End body container section-->
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchInput');

        searchInput.addEventListener('keyup', function () {
            let search = this.value;

            fetch(`{{ route('admin.users.index') }}?search=${search}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(data => {
                document.getElementById('usersTable').innerHTML = data;
            });
        });
    });


    $(document).ready(function() {
        $('.toggle-status').click(function() {
            var button = $(this);
            var user_id = button.data('id');
            var status = button.hasClass('btn-success') ? 0 : 1; // عكس الحالة الحالية

            $.ajax({
                url: '{{ route("admin.users.updateStatus") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: user_id,
                    status: status
                },
                success: function(response) {
                    if(status) {
                        button.removeClass('btn-danger').addClass('btn-success');
                        button.text('مفعل');
                        Swal.fire('تم التفعيل!', '', 'success');
                    } else {
                        button.removeClass('btn-success').addClass('btn-danger');
                        button.text('غير مفعل');
                        Swal.fire('تم التعطيل!', '', 'warning');
                    }
                },
                error: function() {
                    Swal.fire('حدث خطأ ما', '', 'error');
                }
            });
        });
    });



</script>

@endpush





