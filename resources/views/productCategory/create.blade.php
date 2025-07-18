@extends('layouts.master')

@section('title', __('messages.productCategory.add_new_category'))

@section('content')
@include('layouts.partials.sweet_alert')
<!--Start Main content container-->
<div class="main_content_container">
    <div class="main_container main_menu_open">
        <div class="page_content">
            <h1 class="heading_title" style="margin-top: 90px;">
                @lang('messages.productCategory.add_new_category')
            </h1>

            <div class="form">
                <form method="POST" action="{{ route('productCategory.store') }}" enctype="multipart/form-data">
                    @csrf

                    {{-- السطر الأول --}}
                    <div class="row">
                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.productCategory.name')</label>
                            <input type="text" class="form-control" name="name"
                                   placeholder=@lang('messages.productCategory.name')
                                   value="{{ old('name') }}">
                            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.productCategory.description')</label>
                            <input type="text" class="form-control" name="description"
                                   placeholder=@lang('messages.productCategory.description')
                                   value="{{ old('description') }}">
                            @error('description') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.productCategory.parent_id')</label>
                            <input type="text" class="form-control" name="parent_id"
                                   placeholder=@lang('messages.productCategory.parent_id')
                                   value="{{ old('parent_id') }}">
                            @error('parent_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.productCategory.image')</label>
                            <input type="file" class="form-control" name="image_path">
                            @error('image_path') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    {{-- الأزرار --}}
                    <div class="row mt-4">
                        <div class="col-md-12 text-dir">
                            <button type="submit" class="btn btn-success me-2">
                                @lang('messages.save')
                            </button>
                            <a href="{{ route('productCategory.index') }}" class="btn btn-info">
                                @lang('messages.productCategory.view_categories')
                            </a>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
<!--/End Main content container-->
@endsection
