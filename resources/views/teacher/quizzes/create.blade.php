@extends('layouts.app')
@section('title', 'Add quiz')
@section('content')
    <div class="card" style="max-width:520px;">
        <div class="card-header"><h2 style="margin:0; font-size:15px;">Add quiz — {{ $subject->subject_name }}</h2></div>
        <div class="card-body">
            <form method="POST" action="{{ route('teacher.subjects.quizzes.store', $subject) }}">
                @csrf
                <div class="field">
                    <label for="title">Title</label>
                    <input id="title" type="text" name="title" required>
                </div>
                <div class="field">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="3"></textarea>
                </div>
                <div class="field">
                    <label for="time_limit_minutes">Time limit (minutes)</label>
                    <input id="time_limit_minutes" type="number" min="1" name="time_limit_minutes">
                    <div class="field-hint">Leave blank for no time limit.</div>
                </div>
                <div style="display:flex; gap:8px;">
                    <button type="submit" class="btn btn-primary">Create quiz</button>
                    <a href="{{ route('teacher.subjects.quizzes.index', $subject) }}" class="btn btn-ghost">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
