@extends('layouts.auth')
@section('title', 'Set new password')
@section('content')
    <h1>Set a new password</h1>
    <p class="subtitle">Choose a new password for your account.</p>

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <div class="field">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus>
        </div>
        <div class="field">
            <label for="password">New password</label>
            <input id="password" type="password" name="password" required>
        </div>
        <div class="field">
            <label for="password_confirmation">Confirm new password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required>
        </div>
        <button type="submit" class="btn-primary">Reset password</button>
    </form>
@endsection
