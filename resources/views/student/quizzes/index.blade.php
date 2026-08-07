@extends('layouts.app')
@section('title', 'Quizzes — ' . $subject->subject_name)
@section('content')
    <div class="card">
        <div class="card-header"><h2 style="margin:0; font-size:15px;">Quizzes</h2></div>
        @if ($quizzes->isEmpty())
            <div class="empty-state"><h3>No quizzes posted yet</h3></div>
        @else
            <table class="ledger">
                <thead><tr><th>Title</th><th>Questions</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    @foreach ($quizzes as $quiz)
                        @php($attempt = $quiz->attempts->first())
                        <tr>
                            <td>{{ $quiz->title }}</td>
                            <td>{{ $quiz->questions_count }}</td>
                            <td>
                                @if ($attempt)
                                    <span class="badge badge-active">Completed — {{ $attempt->score }}%</span>
                                @else
                                    <span class="badge badge-pending">Not attempted</span>
                                @endif
                            </td>
                            <td style="text-align:right;">
                                @if ($attempt)
                                    <a href="{{ route('student.quizzes.attempts.show', $attempt) }}" class="btn btn-ghost btn-sm">View results</a>
                                @else
                                    <a href="{{ route('student.quizzes.take', $quiz) }}" class="btn btn-primary btn-sm">Take quiz</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
