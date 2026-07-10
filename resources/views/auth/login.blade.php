@extends('layouts.guest')

@section('content')
<div class="login-card fade-up">
    <div class="login-logo">
        <span class="material-symbols-outlined">public</span>
        <h4>G-SCRI Platform</h4>
        <p>Enterprise Decision Support System</p>
    </div>

    <form method="POST" action="/api/login">
        @csrf
        <div class="mb-4">
            <label class="form-label" style="font-weight: 500; font-size: 0.85rem; margin-left: 10px; color: var(--text-main);">Work Email</label>
            <input type="email" name="email" class="form-control form-control-glass" placeholder="Enter your corporate email" required autofocus>
        </div>

        <div class="mb-4">
            <label class="form-label d-flex justify-content-between" style="font-weight: 500; font-size: 0.85rem; margin-left: 10px; color: var(--text-main);">
                <span>Password</span>
                <a href="#" style="color: var(--primary); text-decoration: none;">Forgot?</a>
            </label>
            <input type="password" name="password" class="form-control form-control-glass" placeholder="••••••••" required>
        </div>

        <div class="mb-4 form-check" style="margin-left: 10px;">
            <input type="checkbox" class="form-check-input" id="remember" name="remember" style="border-color: var(--primary);">
            <label class="form-check-label" for="remember" style="font-size: 0.85rem; color: var(--text-muted);">Stay signed in</label>
        </div>

        <button type="button" class="btn-login" onclick="window.location.href='/'">
            Secure Login
        </button>

        <div class="text-center mt-4">
            <p style="font-size: 0.85rem; color: var(--text-muted);">
                Is your company new here? <a href="#" style="color: var(--primary); text-decoration: none; font-weight: 600;">Register Company</a>
            </p>
        </div>
    </form>
</div>
@endsection
