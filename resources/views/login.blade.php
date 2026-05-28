@extends('layout.format')

@section('title')
    Login
@endsection

@section('body-class', 'auth-page')

@section('Header')
@endsection

@section('Footer')
@endsection

@section('Content')
    <div class="login-shell">
        <section class="login-panel">
            <div class="login-panel-top">
                <h3>User Login</h3>
                <p class="login-subtext">Enter your account details to continue.</p>
            </div>

            @if (session('success'))
                <div class="auth-flash success">{{ session('success') }}</div>
            @endif

            @if (session('info'))
                <div class="auth-flash info">{{ session('info') }}</div>
            @endif

            @if ($errors->any())
                <div class="error-message">{{ $errors->first() }}</div>
            @endif

            <form action="/login" method="POST" class="login-form">
                @csrf

                <div class="login-field">
                    <span class="login-field-icon left" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21a8 8 0 0 0-16 0"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </span>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" placeholder="Username" required>
                </div>

                <div class="login-field">
                    <span class="login-field-icon right" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="5" y="11" width="14" height="10" rx="2"></rect>
                            <path d="M8 11V8a4 4 0 1 1 8 0v3"></path>
                            <circle cx="12" cy="16" r="1.6"></circle>
                        </svg>
                    </span>
                    <input type="password" id="password" name="password" placeholder="Password" required>
                </div>

                <button type="submit" class="login-submit">Login</button>
            </form>

            <p class="login-helper">Use the account provided by the administrator.</p>
        </section>
    </div>
@endsection
