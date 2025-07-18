@extends('layouts.master')

@section('title', __('messages.product.edit_product'))

@section('content')
@include('layouts.partials.sweet_alert')

<!--Start Main content container-->
<div class="main_content_container">
    <div class="main_container main_menu_open">
        <div class="page_content">
            <h1 class="heading_title" style="margin-top: 90px;">
                @lang('messages.product.edit_product')
            </h1>

            <div class="form">
                <form method="POST"
                      action="{{ route('product.update', ['product' => $product->id, 'page' => request()->get('page')]) }}"
                      enctype="multipart/form-data">
                    @csrf
                    @method('put')

                    {{-- الصف الأول --}}
                    <div class="row">
                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.product.product_name')</label>
                            <input type="text" class="form-control" name="name"
                                   placeholder="{{ __('messages.product.product_name') }}"
                                   value="{{ old('name', $product->name) }}">
                            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.product.barcode')</label>
                            <input type="text" class="form-control" name="barcode"
                                   placeholder="{{ __('messages.product.barcode') }}"
                                   value="{{ old('barcode', $product->barcode) }}">
                            @error('barcode') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.product.manufacturer')</label>
                            <input type="text" class="form-control" name="manufacture_company"
                                   placeholder="{{ __('messages.product.manufacturer') }}"
                                   value="{{ old('manufacture_company', $product->manufacture_company) }}">
                            @error('manufacture_company') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.product.product_category')</label>
                            <select name="category_id" class="form-control">
                                <option value="" disabled {{ old('category_id') ? '' : 'selected' }}>
                                    @lang('messages.product.product_category')
                                </option>
                                @foreach ($productCategories as $category)
                                    <option value="{{ $category->id }}"
                                            {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    {{-- الصف الثاني --}}
                    <div class="row mt-3">
                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.product.unit_price')</label>
                            <input type="text" class="form-control" name="unit_price"
                                   placeholder="{{ __('messages.product.unit_price') }}"
                                   value="{{ old('unit_price', $product->unit_price) }}">
                            @error('unit_price') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.product.product_image')</label>
                            <input type="file" class="form-control" name="image_path">
                            @error('image_path') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    {{-- زر الحفظ --}}
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
