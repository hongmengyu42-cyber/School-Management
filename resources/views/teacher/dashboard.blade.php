@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
    <div class="card"><div class="card-body">
        <h2 style="margin-top:0;">Welcome back, {{ auth()->user()->full_name }}.</h2>
        <p style="color:var(--ink-soft);">Teacher tools (grading, attendance, assignments, quizzes) land in a later step.</p>
    </div></div>
@endsection
