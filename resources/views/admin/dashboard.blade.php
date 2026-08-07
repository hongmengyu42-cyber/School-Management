@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
    <div class="card">
        <div class="card-body">
            <h2 style="margin-top:0;">Welcome back, {{ auth()->user()->full_name }}.</h2>
            <p style="color:var(--ink-soft);">
                This is a placeholder dashboard. Summary widgets (pending approvals,
                active enrollment counts, at-risk students) land once the reporting
                queries are built in a later step.
            </p>
            <div style="display:flex; gap:10px; margin-top:16px;">
                <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">Manage users</a>
                <a href="{{ route('admin.subjects.index') }}" class="btn btn-ghost">Manage subjects</a>
                <a href="{{ route('admin.semesters.index') }}" class="btn btn-ghost">Manage semesters</a>
            </div>
        </div>
    </div>
@endsection
