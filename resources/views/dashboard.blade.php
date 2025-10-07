@php
    $user = auth()->user();
@endphp

@if($user->hasRole('admin'))
    <p>مرحباً أيها المدير!</p>
@endif

@if($user->hasPermission('edit articles'))
    <button>تعديل المقال</button>
@endif
