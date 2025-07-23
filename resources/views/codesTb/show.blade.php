@extends('layouts.master')

@section('title', __('messages.codeTb_details'))

@section('content')

<div class="main_content_container py-5">
    <div class="main_container  main_menu_open">

        {{-- العنوان --}}
        <div class="mb-5 text-center mt-4">
            <h1 class="display-5 fw-bold text-primary">@lang('messages.codeTb_details')</h1>
            {{-- <hr class="mx-auto" style="width: 80px; border-top: 3px solid #0d6efd;"> --}}
        </div>

        {{-- جدول البيانات مع تباعد وحواف --}}
        <table class="table table-striped table-bordered align-middle" style="border-radius: 10px; overflow: hidden;">
            <tbody>
                <tr>
                    <th class="w-25 text-end bg-light">@lang('messages.codesTb.main_cd')</th>
                    <td>{{ $codeTb->main_cd }}</td>
                </tr>
                <tr>
                    <th class="text-end bg-light">@lang('messages.codesTb.sub_cd')</th>
                    <td>{{ $codeTb->sub_cd }}</td>
                </tr>
                <tr>
                    <th class="text-end bg-light">@lang('messages.codesTb.description_ar')</th>
                    <td>{{ $codeTb->desc_ar }}</td>
                </tr>
                <tr>
                    <th class="text-end bg-light">@lang('messages.codesTb.description_en')</th>
                    <td>{{ $codeTb->desc_en }}</td>
                </tr>
                <tr>
                    <th class="text-end bg-light">@lang('messages.codesTb.details')</th>
                    <td>{{ $codeTb->details }}</td>
                </tr>
                <tr>
                    <th class="text-end bg-light">@lang('messages.codesTb.is_active')</th>
                    <td>
                        @if($codeTb->is_active)
                            <span class="badge bg-success">@lang('messages.codesTb.active')</span>
                        @else
                            <span class="badge bg-danger">@lang('messages.codesTb.inactive')</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th class="text-end bg-light">@lang('messages.codesTb.editable')</th>
                    <td>
                        @if($codeTb->is_editables)
                            <span class="badge bg-success">@lang('messages.codesTb.yes')</span>
                        @else
                            <span class="badge bg-danger">@lang('messages.codesTb.no')</span>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- أزرار التحكم --}}
        <div class="d-flex justify-content-center gap-3 mt-4 flex-wrap">
            <a href="{{ route('codeTb.index') }}" class="btn btn-outline-primary btn-lg px-4">
                <i class="bi bi-arrow-left"></i> @lang('messages.back_to_list')
            </a>
            <a href="{{ route('codeTb.edit', $codeTb->id) }}" class="btn btn-primary btn-lg px-4">
                <i class="bi bi-pencil"></i> @lang('messages.edit')
            </a>
        </div>

    </div>
</div>
@endsection
