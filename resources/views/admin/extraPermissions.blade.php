@extends('layouts.master')
@section('title', __('messages.admin.extra_permissions'))

@section('content')
@include('layouts.partials.sweet_alert')

<div class="main_content_container">
    <div class="main_container main_menu_open">
        <div class="page_content" > {{-- تقليل الفراغ فوق المحتوى --}}

            <h1 class="heading_title mt-4 mb-4">@lang('messages.admin.extra_permissions')</h1>

            <div class="card shadow-sm mb-4">
                <div class="card-body">

                    <form method="POST" action="{{ route('admin.extra_permissions.update', $user->id) }}">
                        @csrf
                        @method('PUT')

                        {{-- معلومات المستخدم --}}
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">@lang('messages.admin.name')</label>
                                <input type="text" class="form-control" value="{{ $user->name }}" disabled>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">@lang('messages.admin.email')</label>
                                <input type="email" class="form-control" value="{{ $user->email }}" disabled>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">@lang('messages.admin.role')</label>
                                <input type="text" class="form-control" value="{{ $user->role }}" disabled>
                            </div>
                        </div>

                        {{-- قائمة الصلاحيات --}}
                        <h5 class="mb-3 fw-semibold">@lang('messages.admin.select_permissions')</h5>
                        <div class="row g-3">

                            @foreach($allPermissions as $perm)
                                @php
                                    $isRole = in_array($perm, $rolePermissions);
                                    $isExtra = in_array($perm, $extraPermissions);
                                    $isDenied = in_array($perm, $deniedPermissions);
                                @endphp

                                <div class="col-md-4">
                                    <div class="card p-3 h-100 shadow-sm">
                                        <div class="d-flex flex-column">

                                            {{-- اسم الصلاحية --}}
                                            <div class="mb-2 d-flex align-items-center justify-content-between">
                                                <span class="fw-medium">{{ $perm }}</span>
                                                @if($isRole && !$isDenied)
                                                    <span class="badge bg-primary">Role</span>
                                                @endif
                                            </div>

                                            {{-- صلاحية إضافية --}}
                                            <div class="form-check mb-1">
                                                <input type="checkbox" class="form-check-input"
                                                       name="permissions[{{ $perm }}][extra]"
                                                       id="extra_{{ $loop->index }}"
                                                       {{ $isExtra ? 'checked' : '' }}
                                                       {{ $isRole && !$isDenied ? 'disabled' : '' }}>
                                                <label class="form-check-label" for="extra_{{ $loop->index }}">
                                                    إضافية
                                                </label>
                                            </div>

                                            {{-- صلاحية ممنوعة --}}
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input"
                                                       name="permissions[{{ $perm }}][denied]"
                                                       id="denied_{{ $loop->index }}"
                                                       {{ $isDenied ? 'checked' : '' }}>
                                                <label class="form-check-label text-danger" for="denied_{{ $loop->index }}">
                                                    ممنوعة
                                                </label>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            @endforeach

                        </div>

                        {{-- الأزرار --}}
                        <div class="mt-4 d-flex gap-2">
                            <button type="submit" class="btn btn-success">@lang('messages.save')</button>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-info">@lang('messages.admin.back_to_users')</a>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
