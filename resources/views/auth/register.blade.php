<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8" />
    <title>{{ __('messages.register.register_title') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    @if (app()->getLocale() == 'ar')
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet" />
    @else
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    @endif

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />

    <style>
        body {
            background: linear-gradient(to bottom left, #e3f2fd, #ffffff);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .register-card {
            background: #ffffff;
            border: none;
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.05);
            padding: 40px;
            width: 100%;
            max-width: 500px;
            position: relative;
        }

        .register-card .logo {
            width: 70px;
            margin-bottom: 20px;
        }

        .register-card h4 {
            font-weight: bold;
            margin-bottom: 10px;
            color: #0d6efd;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #0d6efd;
        }

        .btn-primary {
            border-radius: 30px;
            font-weight: bold;
        }

        .language-toggle {
            position: absolute;
            top: 15px;
            {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 15px;
        }

        .form-check-label {
            user-select: none;
        }
    </style>
</head>
<body>

<div class="register-card">

    <!-- زر تغيير اللغة -->
    <div class="language-toggle">
        <a href="{{ route('toggle.language') }}" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-translate"></i> {{ __('messages.register.change_language') }}
        </a>
    </div>

    <div class="text-center">
        <img src="https://cdn-icons-png.flaticon.com/512/2965/2965567.png" class="logo" alt="Pharmacy Logo" />
        <h4>{{ __('messages.register.app_name') }}</h4>
        <p class="text-muted">{{ __('messages.register.register_subtitle') }}</p>
    </div>

    <!-- عرض الأخطاء -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}" class="mt-4">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">{{ __('messages.register.full_name') }}</label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                       class="form-control" placeholder="{{ __('messages.register.name_placeholder') }}" />
            </div>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">{{ __('messages.register.email') }}</label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                       class="form-control" placeholder="{{ __('messages.register.email_placeholder') }}" />
            </div>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">{{ __('messages.register.password') }}</label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                <input type="password" id="password" name="password" required
                       class="form-control" placeholder="{{ __('messages.register.password_placeholder') }}" />
            </div>
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">{{ __('messages.register.confirm_password') }}</label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="bi bi-lock-fill"></i></span>
                <input type="password" id="password_confirmation" name="password_confirmation" required
                       class="form-control" placeholder="{{ __('messages.register.confirm_password_placeholder') }}" />
            </div>
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-person-plus me-1"></i> {{ __('messages.register.create_account') }}
            </button>
        </div>

        <div class="text-center mt-3">
            <span>{{ __('messages.register.already_have_account') }}</span>
            <a href="{{ route('login') }}" class="text-decoration-none">{{ __('messages.register.login') }}</a>
        </div>

    </form>
</div>

</body>
</html>
