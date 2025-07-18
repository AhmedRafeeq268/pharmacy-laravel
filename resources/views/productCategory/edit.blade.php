@extends('layouts.master')

@section('title', __('messages.productCategory.edit_category'))

@section('content')
@include('layouts.partials.sweet_alert')
<!--Start Main content container-->
<div class="main_content_container">
    <div class="main_container main_menu_open">
        <div class="page_content">
            <h1 class="heading_title" style="margin-top: 90px;">
                @lang('messages.productCategory.edit_category')
            </h1>

            <div class="form">
                <form method="POST" action="{{ route('productCategory.update', ['productCategory'=>$productCategory->id ,'page'=>request()->get('page')]) }}" enctype="multipart/form-data">
                    @csrf
                    @method('put')

                    {{-- الصف الأول --}}
                    <div class="row">
                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.productCategory.name')</label>
                            <input type="text" class="form-control" name="name"
                                   placeholder="{{ __('messages.productCategory.name') }}"
                                   value="{{ old('name', $productCategory->name) }}">
                            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.productCategory.description')</label>
                            <input type="text" class="form-control" name="description"
                                   placeholder="{{ __('messages.productCategory.description') }}"
                                   value="{{ old('description', $productCategory->description) }}">
                            @error('description') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.productCategory.parent_id')</label>
                            <input type="text" class="form-control" name="parent_id"
                                   placeholder="{{ __('messages.productCategory.parent_id') }}"
                                   value="{{ old('parent_id', $productCategory->parent_id) }}">
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
                            <button type="submit" class="btn btn-danger">
                                @lang('messages.save')
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
<!--/End Main content container-->
@endsection
