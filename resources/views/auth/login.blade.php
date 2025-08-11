<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Tree Plantation Monitor') }} - Login</title>
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="{{ asset('js/app.js') }}" defer></script>
</head>
<body>
<style>
    body, html {
        margin: 0;
        padding: 0;
        height: 100%;
        overflow-x: hidden;
    }
    
    .login-bg {
        background-image: url('{{ asset('images/login-bg.jpg') }}');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        height: 100vh;
        width: 100vw;
        position: relative;
    }
    
    .login-bg::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.4);
        z-index: 1;
    }
    
    .login-form-container {
        position: relative;
        z-index: 2;
    }
    
    .login-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: none;
        border-radius: 15px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    }
    
    @media (max-width: 768px) {
        .login-form-container {
            padding: 0 15px;
        }
    }
</style>

<div class="login-bg d-flex justify-content-center align-items-center">
    <div class="login-form-container col-12 col-sm-8 col-md-6 col-lg-4">
        <div class="card login-card shadow-lg">
            <div class="card-header bg-transparent border-0 text-center py-4">
                <img src="{{ asset('images/logo.png') }}" alt="Tree Plantation Monitor" class="mb-3" style="max-height: 60px; width: auto;">
                <h4 class="mb-0 text-dark fw-bold">Jalgaon</h4>
            </div>

            <div class="card-body p-4">
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">{{ __('Email Address') }}</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">{{ __('Password') }}</label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">
                                {{ __('Remember Me') }}
                            </label>
                        </div>
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary">
                            {{ __('Login') }}
                        </button>
                    </div>

                    {{-- @if (Route::has('password.request'))
                        <div class="text-center">
                            <a class="btn btn-link" href="{{ route('password.request') }}">
                                {{ __('Forgot Your Password?') }}
                            </a>
                        </div>
                    @endif --}}
                </form>

                {{-- volunteer@treeplantation.org password123 <br/>
                admin@treeplantation.org password123 --}}
            </div>
        </div>
    </div>
</div>
</body>
</html>
