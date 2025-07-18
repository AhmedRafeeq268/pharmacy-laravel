{{-- ✅ رسالة نجاح --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        <strong><i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- ❌ رسالة خطأ --}}
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
        <strong><i class="bi bi-x-circle-fill me-2"></i> {{ session('error') }}</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- ⚠️ أخطاء التحقق --}}
@if($errors->any())
    <div class="alert alert-warning alert-dismissible fade show mt-3" role="alert">
        <strong><i class="bi bi-exclamation-triangle-fill me-2"></i> @lang('messages.error')</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
