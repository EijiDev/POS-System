<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login – BrewPOS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>

<div class="login-wrapper">

    {{-- Left: Form Panel --}}
    <div class="form-panel">
        <div class="form-content">
            <div class="brand">
                <span class="brand-name">BrewPOS</span>
            </div>
            <h1 class="heading">Welcome back</h1>
            <p class="subheading">Sign in to your account to continue</p>

            @if(session('error') || $errors->any())
                <div class="error-message">
                    {{ session('error') ?? 'Invalid email or password.' }}
                </div>
            @endif

            <form id="loginForm" method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label for="email">Email</label>
                    <div class="input-wrapper">
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="admin@brewpos.com"
                            required
                            autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <div class="label-row">
                        <label for="password">Password</label>
                    </div>
                    <div class="input-wrapper">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Enter your password"
                            required>
                        <button type="button" class="toggle-password" id="togglePassword">
                            <svg id="eye-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-signin" id="signInBtn">
                    <span class="btn-text">Sign In</span>
                    <span class="btn-loader hidden"></span>
                </button>
            </form>

        </div>
    </div>

    {{-- Right: Image Panel --}}
    <div class="image-panel">
        <div class="image-overlay"></div>
        <div class="image-caption">
            <h2>Brew smarter,<br>serve faster.</h2>
            <p>Your all-in-one coffee shop<br>point of sale system.</p>
        </div>
    </div>

</div>

<script src="{{ asset('js/login.js') }}"></script>
</body>
</html>
