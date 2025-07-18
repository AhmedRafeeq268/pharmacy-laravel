<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('messages.login.login_title') }} - {{ __('messages.login.pharmacy_system') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 RTL أو LTR حسب اللغة -->
    @if(app()->getLocale() === 'ar')
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    @else
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @endif

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(to bottom left, #e3f2fd, #ffffff);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-card {
            background: #ffffff;
            border: none;
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.05);
            padding: 40px;
            width: 100%;
            max-width: 450px;
        }

        .login-card .logo {
            width: 70px;
            margin-bottom: 20px;
        }

        .login-card h4 {
            font-weight: bold;
            margin-bottom: 10px;
            color: #0d6efd;
        }

        .login-card .form-control:focus {
            box-shadow: none;
            border-color: #0d6efd;
        }

        .login-card .btn-primary {
            border-radius: 30px;
            font-weight: bold;
        }

        .form-check-label {
            user-select: none;
        }
    </style>
</head>
<body>

<div class="login-card">

    <!-- زر تبديل اللغة -->
    <div class="language-toggle">
        <a href="{{ route('toggle.language') }}" class="btn btn-outline-primary btn-sm">

            <i class="bi bi-translate"></i> {{ __('messages.login.change_language') }}
        </a>
    </div>

    <div class="text-center">
        <img src="https://cdn-icons-png.flaticon.com/512/2965/2965567.png" class="logo" alt="Pharmacy Logo">
        <h4>{{ __('messages.login.pharmacy_system') }}</h4>
        <p class="text-muted">{{ __('messages.login.enter_credentials') }}</p>
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-success text-center">
            {{ session('status') }}
        </div>
    @endif

    <!-- Validation Errors -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="mt-4">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">{{ __('messages.login.email') }}</label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                <input type="email" class="form-control" id="email" name="email"
                       value="{{ old('email') }}" required autofocus placeholder="example@pharmacy.com">
            </div>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">{{ __('messages.login.password') }}</label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                <input type="password" class="form-control" id="password" name="password"
                       required placeholder="••••••••">
            </div>
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" name="remember" id="remember">
            <label class="form-check-label" for="remember">{{ __('messages.login.remember_me') }}</label>
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-box-arrow-in-right me-1"></i> {{ __('messages.login.login') }}
            </button>
        </div>

        @if (Route::has('password.request'))
            <div class="text-center mt-3">
                <a href="{{ route('password.request') }}" class="text-decoration-none">
                    {{ __('messages.login.forgot_password') }}
                </a>
            </div>
        @endif

        <div class="text-center mt-3">
            <span>{{ __('messages.login.new_account_prompt') }}</span>
            <a href="{{ route('register') }}" class="text-decoration-none">
                {{ __('messages.login.create_account') }}
            </a>
        </div>
    </form>
</div>

</body>
</html>
