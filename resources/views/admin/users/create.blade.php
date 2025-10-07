@extends('layouts.master')
@section('title', __('messages.admin.add_new_users'))

@section('content')
    @include('layouts.partials.sweet_alert')

    <!--Start Main content container-->
    <div class="main_content_container">
        <div class="main_container main_menu_open">
            <div class="page_content">
                <h1 class="heading_title" style="margin-top: 90px;">
                    @lang('messages.admin.add_new_users')
                </h1>

                <div class="form">
                    <form method="POST" action="{{ route('admin.users.store') }}">
                        @csrf

                        {{-- السطر الأول --}}
                        <div class="row">
                            <div class="col-md-4">
                                <label class="mb-2">@lang('messages.admin.name')</label>
                                <input type="text" class="form-control" name="name"
                                       value="{{ old('name') }}">
                                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="mb-2">@lang('messages.admin.email')</label>
                                <input type="email" class="form-control" name="email"
                                       value="{{ old('email') }}">
                                @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="mb-2">@lang('messages.admin.password')</label>
                                <input type="password" class="form-control" name="password"
                                       placeholder="@lang('messages.admin.password')">
                                @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        {{-- السطر الثاني --}}
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <label class="mb-2">@lang('messages.admin.password_confirmation')</label>
                                <input type="password" class="form-control" name="password_confirmation"
                                       placeholder="@lang('messages.admin.password_confirmation')">
                                @error('password_confirmation') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="mb-2">@lang('messages.admin.role')</label>
                                <select name="role_id" class="form-control">
                                    <option value="" disabled selected>@lang('messages.admin.select_role')</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}"
                                            {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role_id') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        {{-- الأزرار --}}
                        <div class="row mt-4">
                            <div class="col-md-12 text-dir">
                                <button type="submit" class="btn btn-success me-2">@lang('messages.save')</button>
                                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                                    @lang('messages.back_to_list')
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
