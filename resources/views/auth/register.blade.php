@extends('layouts.auth')
@section('title', 'Create account')
@section('content')
    <h1>Create your account</h1>
    <p class="subtitle">Student self-registration. Your account needs admin approval before you can sign in.</p>

    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="field">
            <label for="full_name">Full name</label>
            <input id="full_name" type="text" name="full_name" value="{{ old('full_name') }}" required autofocus>
        </div>
        <div class="field">
            <label for="username">Username</label>
            <input id="username" type="text" name="username" value="{{ old('username') }}" required>
        </div>
        <div class="field">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required>
        </div>
        <div class="field">
            <label for="password">Password</label>
            <input id="password" type="password" name="password" required>
        </div>
        <div class="field">
            <label for="password_confirmation">Confirm password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required>
        </div>
        <button type="submit" class="btn-primary">Create account</button>
    </form>

    <div class="links"><a href="{{ route('login') }}">Already have an account? Sign in</a></div>
@endsection
