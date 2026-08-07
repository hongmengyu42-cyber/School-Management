@extends('layouts.auth')
@section('title', 'Sign in')
@section('content')
    <h1>Sign in</h1>
    <p class="subtitle">Enter your credentials to access your portal.</p>

    @if ($errors->any())
        <div class="flash-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="field">
            <label for="username">Username</label>
            <input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus>
        </div>
        <div class="field">
            <label for="password">Password</label>
            <input id="password" type="password" name="password" required>
        </div>
        <div class="checkbox-row">
            <input type="checkbox" id="remember" name="remember">
            <label for="remember" style="margin:0;">Remember me</label>
        </div>
        <button type="submit" class="btn-primary">Sign in</button>
    </form>

    <div class="links">
        <a href="{{ route('password.request') }}">Forgot your password?</a>
        &nbsp;·&nbsp;
        <a href="{{ route('register') }}">Create a student account</a>
    </div>
@endsection
