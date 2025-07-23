@extends('layouts.master')
@section('title',__('messages.customer.edit_customer_data'))

@section('content')
@include('layouts.partials.sweet_alert')
<!--Start Main content container-->
<div class="main_content_container">
    <div class="main_container main_menu_open">
        <div class="page_content">
            <h1 class="heading_title" style="margin-top: 90px;">@lang('messages.customer.edit_customer_data')</h1>
            <div class="form">
                <form class="form-horizontal" method="Post" action="{{ route('customer.update', ['customer'=>$customer->id ,'page' => request()->get('page')]) }}">
                    @csrf
                    @method('put')

                    {{-- السطر الأول --}}
                    <div class="row">
                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.customer.name')</label>
                            <input type="text" class="form-control" name="name" value="{{old('name', $customer->name )}}" placeholder=@lang('messages.customer.name')>
                            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.customer.phone')</label>
                            <input type="number" class="form-control" name="phone" value="{{old('phone', $customer->phone )}}" placeholder=@lang('messages.customer.phone')>
                            @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.customer.email')</label>
                            <input type="email" class="form-control" name="email" value="{{old('email', $customer->email )}}" placeholder=@lang('messages.customer.email')>
                            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.customer.address')</label>
                            <input type="text" class="form-control" name="address" value="{{old('address', $customer->address )}}" placeholder=@lang('messages.customer.address')>
                            @error('address') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    {{-- السطر الثاني --}}
                    <div class="row mt-3">
                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.customer.id_card')</label>
                            <input type="number" class="form-control" name="id_card" value="{{old('id_card', $customer->id_card) }}" placeholder=@lang('messages.customer.id_card')>
                            @error('id_card') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.customer.address_details')</label>
                            <input type="text" class="form-control" name="address_details" value="{{old('address_details', $customer->address_details) }}" placeholder=@lang('messages.customer.address_details')>
                            @error('address_details') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                            {{-- @php
                                $descField = app()->getLocale() === 'ar' ? 'desc_ar' : 'desc_en';
                            @endphp
                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.customer.status')</label>
                            <select name="status_cd" class="form-control">
                                <option value="" disabled {{ old('status_cd', $customer->status_cd) ? '' : 'selected' }}>
                                    @lang('messages.customer.select_status')
                                </option>
                                @foreach ($status as $item)

                                    <option value="{{ $item->sub_cd }}"
                                        {{ old('status_cd', $customer->status_cd) == $item->sub_cd ? 'selected' : '' }}>
                                        {{ $item->$descField }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status_cd') <small class="text-danger">{{ $message }}</small> @enderror
                        </div> --}}

                        <div class="col-md-3">
                            <label class="mb-2 d-block">@lang('messages.customer.status')</label>
                            <button type="button" id="toggleActiveBtn"
                                class="btn {{ (old('status_cd', $customer->status_cd) == 0) ? 'btn-success' : 'btn-secondary' }}">
                                {{ (old('status_cd', $customer->status_cd) == 0) ? __('messages.active') : __('messages.inactive') }}
                            </button>
                            <input type="hidden" name="status_cd" id="is_active_input"
                                value="{{ old('status_cd', $customer->status_cd ?? 0) }}">
                            @error('status_cd') <small class="text-danger d-block">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3"></div>
                    </div>

                    {{-- الأزرار --}}
                    <div class="row mt-3">
                        <div class="col-md-12 text-dir">
                            <button type="submit" class="btn btn-danger me-2">@lang('messages.save')</button>
                            <a href="{{ route('customer.index') }}" class="btn btn-secondary">@lang('messages.back_to_list')</a>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
<!--/End Main content container-->
@endsection


@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const toggleBtn = document.getElementById('toggleActiveBtn');
            const input = document.getElementById('is_active_input');

            toggleBtn.addEventListener('click', function () {
                const isActive = input.value === '1';
                if (isActive) {
                    input.value = '0';
                    toggleBtn.classList.remove('btn-secondary');
                    toggleBtn.classList.add('btn-success');
                    toggleBtn.textContent = "@lang('messages.active')";
                } else {
                    input.value = '1';
                    toggleBtn.classList.remove('btn-success');
                    toggleBtn.classList.add('btn-secondary');
                    toggleBtn.textContent = "@lang('messages.inactive')";


                }
            });
        });
    </script>


@endpush
