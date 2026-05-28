@extends('layout.format')

@section('title')
    Change Password
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
                <h3>Change Password</h3>
                <p class="login-subtext">You are logging in for the first time. Set a new password to continue.</p>
            </div>

            @if ($errors->any())
                <div class="error-message">{{ $errors->first() }}</div>
            @endif

            @if (session('success'))
                <div class="auth-flash success">{{ session('success') }}</div>
            @endif

            @if (session('info'))
                <div class="auth-flash info">{{ session('info') }}</div>
            @endif

            <form action="/change-password" method="POST" class="login-form">
                @csrf

                <div class="login-field">
                    <span class="login-field-icon left" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="5" y="11" width="14" height="10" rx="2"></rect>
                            <path d="M8 11V8a4 4 0 1 1 8 0v3"></path>
                            <circle cx="12" cy="16" r="1.6"></circle>
                        </svg>
                    </span>
                    <input type="password" id="current_password" name="current_password" placeholder="Current Password" required>
                </div>

                <div class="login-field">
                    <span class="login-field-icon left" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 5v14"></path>
                            <path d="M5 12h14"></path>
                            <rect x="4" y="4" width="16" height="16" rx="4"></rect>
                        </svg>
                    </span>
                    <input type="password" id="new_password" name="new_password" placeholder="New Password" required>
                </div>

                <div class="login-field">
                    <span class="login-field-icon left" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 12l2 2 4-4"></path>
                            <rect x="4" y="4" width="16" height="16" rx="4"></rect>
                        </svg>
                    </span>
                    <input type="password" id="new_password_confirmation" name="new_password_confirmation" placeholder="Confirm Password" required>
                </div>

                <button type="submit" class="login-submit">Update Password</button>
            </form>

            <p class="login-helper">Use at least 6 characters and remember your new password.</p>

            <a href="/logout" class="login-submit">Logout</a>
        </section>
    </div>
@endsection
