@extends('layouts.app')
@section('title', 'Add assignment')
@section('content')
    <div class="card" style="max-width:560px;">
        <div class="card-header"><h2 style="margin:0; font-size:15px;">Add assignment — {{ $subject->subject_name }}</h2></div>
        <div class="card-body">
            <form method="POST" action="{{ route('teacher.subjects.assignments.store', $subject) }}">
                @csrf
                @include('teacher.assignments._form')
                <div style="display:flex; gap:8px;">
                    <button type="submit" class="btn btn-primary">Save assignment</button>
                    <a href="{{ route('teacher.subjects.assignments.index', $subject) }}" class="btn btn-ghost">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
