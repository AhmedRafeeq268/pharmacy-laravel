@extends('layouts.master')

@section('title', __('messages.reports.profit_loss_report'))

@section('content')
@include('layouts.partials.sweet_alert')

<div class="main_content_container">
    <div class="main_container main_menu_open">
        <div class="page_content">
            <h1 class="heading_title" style="margin-top: 90px;">
                {{ __('messages.reports.profit_loss_report') }}
            </h1>

            <div class="form">
                <form id="reportForm" class="form-horizontal" action="{{ route('reports.profitLoss') }}" method="GET" target="_blank">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="mb-2">{{ __('messages.reports.from') }}</label>
                            <input type="date" name="from" id="from" class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label class="mb-2">{{ __('messages.reports.to') }}</label>
                            <input type="date" name="to" id="to" class="form-control" required>
                        </div>
                    </div>

                    <div class="d-flex gap-3 mt-1 flex-wrap">
                        <button type="button" class="btn btn-primary px-4 mt-3" onclick="confirmPrint()">
                            {{ __('messages.reports.show_report') }}
                        </button>
                        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary px-4 mt-3">
                            <i class="bi bi-arrow-left"></i> {{ __('messages.back_to_list') }}
                        </a>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

{{-- SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmPrint() {
    Swal.fire({
        title: '{{ __("messages.reports.confirmation") }}',
        text: "{{ __('messages.reports.print_question') }}",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '{{ __("messages.reports.yes") }}',
        cancelButtonText: '{{ __("messages.reports.cancel") }}',
        reverseButtons: {{ app()->getLocale() == "ar" ? "true" : "false" }}
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('reportForm').submit();
        }
    })
}
</script>
@endsection
