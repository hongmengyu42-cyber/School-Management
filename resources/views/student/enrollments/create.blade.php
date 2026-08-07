@extends('layouts.app')
@section('title', 'Enroll in a subject')
@section('content')
    <div class="card" style="max-width:420px;">
        <div class="card-header"><h2 style="margin:0; font-size:15px;">Enroll with an access code</h2></div>
        <div class="card-body">
            <p style="color:var(--ink-soft); margin-top:0;">Ask your teacher for the subject's access code.</p>
            <form method="POST" action="{{ route('student.enrollments.store') }}">
                @csrf
                <div class="field">
                    <label for="access_code">Access code</label>
                    <input id="access_code" type="text" name="access_code" required autofocus>
                </div>
                <button type="submit" class="btn btn-primary">Enroll</button>
            </form>
        </div>
    </div>
@endsection
