@extends('layouts.app')
@section('title', $quiz->title)
@section('content')
    <div class="card" style="max-width:640px;">
        <div class="card-header">
            <h2 style="margin:0; font-size:15px;">{{ $quiz->title }}</h2>
            @if ($quiz->time_limit_minutes)<span class="badge badge-pending">{{ $quiz->time_limit_minutes }} min limit</span>@endif
        </div>
        <div class="card-body">
            <p style="color:var(--ink-soft); margin-top:0;">{{ $quiz->description }}</p>

            <form method="POST" action="{{ route('student.quizzes.attempt', $quiz) }}">
                @csrf
                @foreach ($quiz->questions as $i => $question)
                    <div class="field" style="padding-bottom:16px; border-bottom:1px solid var(--line); margin-bottom:16px;">
                        <label>{{ $i + 1 }}. {{ $question->question_text }} <span style="color:var(--muted); font-weight:400;">({{ $question->points }} pt{{ $question->points > 1 ? 's' : '' }})</span></label>
                        @if ($question->choices)
                            @foreach ($question->choices as $choice)
                                <label style="display:flex; align-items:center; gap:8px; font-weight:400; margin-top:6px;">
                                    <input type="radio" name="answers[{{ $question->id }}]" value="{{ $choice }}" required style="width:auto;">
                                    {{ $choice }}
                                </label>
                            @endforeach
                        @else
                            <input type="text" name="answers[{{ $question->id }}]" required>
                        @endif
                    </div>
                @endforeach
                <button type="submit" class="btn btn-primary">Submit quiz</button>
            </form>
        </div>
    </div>
@endsection
