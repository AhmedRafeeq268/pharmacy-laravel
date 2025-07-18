@extends('layouts.master')

@section('title', __('messages.changePassword'))

@section('content')
@include('layouts.partials.sweet_alert')

<div class="main_content_container">
    <div class="main_container main_menu_open">
        <div class="page_content d-flex justify-content-center align-items-center" style="min-height: 100vh;">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow rounded-4">
                    <div class="card-header bg-primary text-white text-center rounded-top-4">
                        <h4 class="mb-0">@lang('messages.changePassword')</h4>
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf
                            @method('PATCH')

                            <!-- كلمة المرور الحالية -->
                            <div class="mb-3">
                                <label for="current_password" class="form-label">
                                    @lang('messages.current_password')
                                </label>
                                <input type="password" name="current_password" class="form-control" required>
                                @error('current_password')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- كلمة المرور الجديدة -->
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    @lang('messages.new_password')
                                </label>
                                <input type="password" name="password" class="form-control" required>
                                @error('password')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- تأكيد كلمة المرور -->
                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label">
                                    @lang('messages.password_confirmation')
                                </label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-success">
                                    @lang('messages.update')
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
