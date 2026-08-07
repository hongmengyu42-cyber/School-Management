@extends('layouts.app')
@section('title', 'Results — ' . $attempt->quiz->title)
@section('content')
    <div class="card" style="max-width:640px;">
        <div class="card-header">
            <h2 style="margin:0; font-size:15px;">{{ $attempt->quiz->title }}</h2>
            <span class="badge {{ $attempt->score >= 60 ? 'badge-active' : 'badge-suspended' }}">Score: {{ $attempt->score }}%</span>
        </div>
        <table class="ledger">
            <thead><tr><th>Question</th><th>Your answer</th><th>Correct answer</th><th></th></tr></thead>
            <tbody>
                @foreach ($attempt->answers as $answer)
                    <tr>
                        <td>{{ $answer->question->question_text }}</td>
                        <td>{{ $answer->given_answer ?: '—' }}</td>
                        <td>{{ $answer->question->correct_answer }}</td>
                        <td>
                            <span class="badge {{ $answer->is_correct ? 'badge-active' : 'badge-suspended' }}">{{ $answer->is_correct ? 'Correct' : 'Incorrect' }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
