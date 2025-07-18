@if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'success',
                title: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 2500,
                position: 'center',
            });
        });
    </script>
@endif

@if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'error',
                title: '{{ session('error') }}',
                showConfirmButton: false,
                timer: 3000,
                position: 'center',
            });
        });
    </script>
@endif

@if($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'warning',
                title: '{{ __("messages.error") }}',
                html: `{!! implode('<br>', $errors->all()) !!}`,
                showConfirmButton: true,
                position: 'center',
            });
        });
    </script>
@endif
