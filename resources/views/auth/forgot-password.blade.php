@extends('layouts.auth')
@section('title', 'Reset password')
@section('content')
    <h1>Reset your password</h1>
    <p class="subtitle">Enter your email and we'll send you a reset link.</p>

    @if (session('status'))
        <div class="flash-error" style="background:var(--primary-tint); color:var(--primary-dark);">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="field">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
        </div>
        <button type="submit" class="btn-primary">Email password reset link</button>
    </form>

    <div class="links"><a href="{{ route('login') }}">Back to sign in</a></div>
@endsection
