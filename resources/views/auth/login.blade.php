<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Credai') }} - Login</title>
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Inter:400,500,600,700" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="{{ asset('js/app.js') }}" defer></script>
</head>
<body>
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    body, html {
        height: 100%;
        font-family: 'Inter', sans-serif;
        overflow-x: hidden;
    }
    
    .login-container {
        background-image: url('{{ asset('images/login-bg.jpg') }}');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        position: relative;
    }
    
    .login-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.3);
        z-index: 1;
    }
    
    .login-card {
        background: rgba(255, 255, 255, 0.65);
        backdrop-filter: blur(20px) saturate(150%);
        -webkit-backdrop-filter: blur(20px) saturate(150%);
        border-radius: 24px;
        padding: 32px;
        width: 100%;
        max-width: 420px;
        box-shadow: 
            0 8px 32px rgba(31, 38, 135, 0.25),
            inset 0 1px 0 rgba(255, 255, 255, 0.5);
        position: relative;
        z-index: 2;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }
    
    .brand-logo {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 24px;
    }
    
    .brand-logo img {
        height: 50px;
        width: auto;
    }
    
    .welcome-text {
        text-align: center;
        margin-bottom: 32px;
    }
    
    .welcome-title {
        font-size: 28px;
        font-weight: 700;
        color: #065f46;
        margin-bottom: 6px;
    }
    
    .welcome-subtitle {
        font-size: 16px;
        color: #047857;
        font-weight: 400;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-label {
        display: block;
        font-size: 16px;
        font-weight: 500;
        color: #065f46;
        margin-bottom: 8px;
    }
    
    .form-input {
        width: 100%;
        padding: 16px 20px;
        border: 1px solid rgba(255, 255, 255, 0.4);
        border-radius: 12px;
        font-size: 16px;
        background: rgba(255, 255, 255, 0.5);
        backdrop-filter: blur(12px) saturate(110%);
        -webkit-backdrop-filter: blur(12px) saturate(110%);
        transition: all 0.2s ease;
        color: #374151;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.4);
    }
    
    .form-input:focus {
        outline: none;
        border-color: rgba(16, 185, 129, 0.6);
        background: rgba(255, 255, 255, 0.65);
        box-shadow: 
            inset 0 1px 0 rgba(255, 255, 255, 0.5),
            0 0 0 3px rgba(16, 185, 129, 0.1);
    }
    
    .form-input.error {
        border-color: #ef4444;
    }
    
    .login-btn {
        width: 100%;
        padding: 16px;
        background: #10b981;
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 18px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 8px;
    }
    
    .login-btn:hover {
        background: #059669;
        transform: translateY(-1px);
        box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
    }
    
    .register-link {
        text-align: right;
        margin-top: 24px;
    }
    
    .register-link a {
        color: #6b7280;
        text-decoration: underline;
        font-size: 14px;
    }
    
    .product-by {
        text-align: center;
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1px solid rgba(0, 0, 0, 0.1);
    }
    
    .product-by-text {
        font-size: 12px;
        color: #9ca3af;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .product-by img {
        height: 32px;
        width: auto;
    }
    
    .error-message {
        color: #ef4444;
        font-size: 14px;
        margin-top: 4px;
    }
    
    .password-field {
        position: relative;
    }
    
    .password-toggle {
        position: absolute;
        right: 16px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #6b7280;
        font-size: 16px;
        cursor: pointer;
        padding: 4px;
        border-radius: 4px;
        transition: color 0.2s ease;
    }
    
    .password-toggle:hover {
        color: #065f46;
    }
    
    .password-field .form-input {
        padding-right: 50px;
    }
    
    /* Responsive Design */
    @media (max-width: 640px) {
        .login-container {
            padding: 16px;
        }
        
        .login-card {
            padding: 32px 24px;
            border-radius: 20px;
        }
        
        .welcome-title {
            font-size: 28px;
        }
        
        .welcome-subtitle {
            font-size: 16px;
        }
        
        .form-input {
            padding: 14px 16px;
        }
        
        .login-btn {
            padding: 14px;
            font-size: 16px;
        }
    }
    
    @media (max-width: 480px) {
        .login-card {
            padding: 24px 20px;
        }
        
        .welcome-title {
            font-size: 24px;
        }
    }
    
    @media (min-width: 1024px) {
        .login-card {
            max-width: 550px;
            padding: 48px 56px;
        }
    }
</style>

<div class="login-container">
    <div class="login-card">
        <div class="brand-logo">
            <img src="{{ asset('images/logo.png') }}" alt="Credai">
        </div>
        
        <div class="welcome-text">
            <h1 class="welcome-title">Welcome Back!</h1>
            <p class="welcome-subtitle">Let's Login to Your Account</p>
        </div>
        
        <form method="POST" action="{{ route('login') }}">
            @csrf
            
            <div class="form-group">
                <label for="email" class="form-label">Username</label>
                <input 
                    id="email" 
                    type="email" 
                    class="form-input @error('email') error @enderror" 
                    name="email" 
                    value="{{ old('email') }}" 
                    required 
                    autocomplete="email" 
                    autofocus
                >
                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <div class="password-field">
                    <input 
                        id="password" 
                        type="password" 
                        class="form-input @error('password') error @enderror" 
                        name="password" 
                        required 
                        autocomplete="current-password"
                    >
                    <button type="button" class="password-toggle" onclick="togglePassword()">
                        <i class="fas fa-eye" id="password-eye"></i>
                    </button>
                </div>
                @error('password')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <button type="submit" class="login-btn">
                LOGIN
            </button>
            
            {{-- <div class="register-link">
                <span style="color: #6b7280; font-size: 14px;">Don't have an account?</span>
                <a href="#" style="margin-left: 8px;">Register now</a>
            </div> --}}
        </form>
        
        <div class="product-by">
            <div class="product-by-text">PRODUCT BY</div>
            <img src="{{ asset('images/whennex.png') }}" alt="Whennex">
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const passwordField = document.getElementById('password');
    const passwordEye = document.getElementById('password-eye');
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        passwordEye.className = 'fas fa-eye-slash';
    } else {
        passwordField.type = 'password';
        passwordEye.className = 'fas fa-eye';
    }
}
</script>

</body>
</html>
