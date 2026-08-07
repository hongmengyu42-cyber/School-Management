@extends('layouts.app')
@section('title', 'Quizzes — ' . $subject->subject_name)
@section('content')
    <div class="card">
        <div class="card-header">
            <h2 style="margin:0; font-size:15px;">Quizzes</h2>
            <a href="{{ route('teacher.subjects.quizzes.create', $subject) }}" class="btn btn-primary btn-sm">Add quiz</a>
        </div>
        @if ($quizzes->isEmpty())
            <div class="empty-state"><h3>No quizzes yet</h3></div>
        @else
            <table class="ledger">
                <thead><tr><th>Title</th><th>Questions</th><th>Attempts</th><th></th></tr></thead>
                <tbody>
                    @foreach ($quizzes as $quiz)
                        <tr>
                            <td>{{ $quiz->title }}</td>
                            <td>{{ $quiz->questions_count }}</td>
                            <td>{{ $quiz->attempts_count }}</td>
                            <td style="text-align:right;">
                                <a href="{{ route('teacher.quizzes.questions.index', $quiz) }}" class="btn btn-ghost btn-sm">Manage questions</a>
                                <form method="POST" action="{{ route('teacher.subjects.quizzes.destroy', [$subject, $quiz]) }}" style="display:inline;" onsubmit="return confirm('Delete this quiz?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
