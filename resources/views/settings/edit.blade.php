@extends('layouts.master')
@section('title','إعدادات النظام')

@section('content')
@include('layouts.partials.sweet_alert')

<div class="main_content_container">
    <div class="main_container main_menu_open">
        <div class="page_content">
            <h1 class="heading_title" style="margin-top: 90px;">إعدادات النظام</h1>

            <div class="form">
                {{-- نموذج تعديل الإعدادات --}}
                @can('settings.edit')
                <form method="POST" action="{{ route('settings.update') }}">
                    @csrf
                    {{-- مثال: اسم الصيدلية --}}
                    <div class="row">
                        <div class="col-md-6">
                            <label class="mb-2">اسم الصيدلية</label>
                            <input type="text" class="form-control" name="pharmacy_name"
                                value="{{ old('pharmacy_name', $settings->pharmacy_name ?? '') }}">
                            @error('pharmacy_name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="mb-2">عنوان الصيدلية</label>
                            <input type="text" class="form-control" name="pharmacy_address"
                                value="{{ old('pharmacy_address', $settings->pharmacy_address ?? '') }}">
                            @error('pharmacy_address') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12 text-dir">
                            <button type="submit" class="btn btn-danger">حفظ التغييرات</button>
                        </div>
                    </div>
                </form>
                @endcan

                {{-- أزرار تفعيل / إلغاء --}}
                <div class="row mt-3">
                    <div class="col-md-12 text-dir">
                        @can('settings.activate')
                        <form action="{{ route('settings.activate') }}" method="POST" style="display:inline-block;">
                            @csrf
                            <button type="submit" class="btn btn-success">تفعيل النظام</button>
                        </form>
                        @endcan

                        @can('settings.deactivate')
                        <form action="{{ route('settings.deactivate') }}" method="POST" style="display:inline-block;">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger">إلغاء التفعيل</button>
                        </form>
                        @endcan
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
