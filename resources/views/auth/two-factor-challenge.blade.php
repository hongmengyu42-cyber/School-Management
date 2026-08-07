@extends('layouts.auth')
@section('title', 'Two-factor verification')
@section('content')
    <h1>Two-factor verification</h1>
    <p class="subtitle">Enter the 6-digit code from your authenticator app.</p>

    @if ($errors->any())
        <div class="flash-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('two-factor.login') }}">
        @csrf
        <div class="field">
            <label for="code">Authentication code</label>
            <input id="code" type="text" inputmode="numeric" name="code" autofocus autocomplete="one-time-code">
        </div>
        <button type="submit" class="btn-primary">Verify</button>
    </form>

    <div class="links">Lost your device? Use one of your recovery codes instead.</div>
@endsection
