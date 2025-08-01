@extends('layouts.master')

@section('title', __('messages.purchaseReturns.edit_purchaseReturn'))

@section('content')
@include('layouts.partials.sweet_alert')

<!--Start Main content container-->
<div class="main_content_container">
    <div class="main_container main_menu_open">
        <div class="page_content">
            <h1 class="heading_title" style="margin-top: 90px;">
                @lang('messages.purchaseReturns.edit_purchaseReturn')
            </h1>

            <div class="form">
                <form method="POST"
                      action="{{ route('purchaseReturns.update', ['purchaseReturn' => $purchaseReturn->id, 'page' => request()->get('page')]) }}"
                      enctype="multipart/form-data">
                    @csrf
                    @method('put')

                    {{-- الصف الأول --}}
                    <div class="row">
                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.purchaseReturns.quantity')</label>
                            <input type="number" class="form-control" name="quantity"
                                   placeholder="{{ __('messages.purchaseReturns.quantity') }}"
                                   value="{{ old('quantity', $purchaseReturn->quantity) }}">
                            @error('quantity') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.purchaseReturns.return_amount')</label>
                            <input type="number" class="form-control" name="return_amount"
                                   placeholder="{{ __('messages.purchaseReturns.return_amount') }}"
                                   value="{{ old('return_amount', $purchaseReturn->return_amount) }}">
                            @error('return_amount') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.purchaseReturns.reason')</label>
                            <input type="text" class="form-control" name="reason"
                                   placeholder="{{ __('messages.purchaseReturns.reason') }}"
                                   value="{{ old('reason', $purchaseReturn->reason) }}">
                            @error('reason') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="mb-2">@lang('messages.purchaseReturns.refunded_in_cash')</label>
                            <select name="refunded_in_cash" class="form-control">
                                <option value="0" {{ old('refunded_in_cash', $purchaseReturn->refunded_in_cash) == '0' ? 'selected' : '' }}>@lang('messages.no')</option>
                                <option value="1" {{ old('refunded_in_cash', $purchaseReturn->refunded_in_cash) == '1' ? 'selected' : '' }}>@lang('messages.yes')</option>
                            </select>

                            @error('refunded_in_cash') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>


                    </div>

                    {{-- زر الحفظ --}}
                    <div class="row mt-4">
                        <div class="col-md-12 text-dir">
                            <button type="submit" class="btn btn-danger  px-3">
                                @lang('messages.save')
                            </button>

                            <a href="{{ route('purchaseReturns.index') }}" class="btn btn-outline-primary px-3">
                                <i class="bi bi-arrow-left"></i> @lang('messages.back_to_list')
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
