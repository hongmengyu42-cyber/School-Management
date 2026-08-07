@extends('layouts.app')
@section('title', 'Questions — ' . $quiz->title)
@section('content')
    <div class="card" style="margin-bottom:20px;">
        <div class="card-header"><h2 style="margin:0; font-size:15px;">Add a question</h2></div>
        <div class="card-body">
            <form method="POST" action="{{ route('teacher.quizzes.questions.store', $quiz) }}">
                @csrf
                <div class="field">
                    <label for="question_text">Question</label>
                    <textarea id="question_text" name="question_text" rows="2" required></textarea>
                </div>
                <div class="field">
                    <label for="choices">Choices (one per line, optional for short-answer)</label>
                    <textarea id="choices_raw" name="choices_raw" rows="4" placeholder="Choice A&#10;Choice B&#10;Choice C" oninput="document.getElementById('choices_json').value = JSON.stringify(this.value.split('\n').map(s => s.trim()).filter(Boolean));"></textarea>
                    <input type="hidden" id="choices_json" name="choices">
                </div>
                <div class="field">
                    <label for="correct_answer">Correct answer</label>
                    <input id="correct_answer" type="text" name="correct_answer" required>
                    <div class="field-hint">Must exactly match one of the choices above (or the expected short answer).</div>
                </div>
                <div class="field">
                    <label for="points">Points</label>
                    <input id="points" type="number" min="1" name="points" value="1" required>
                </div>
                <button type="submit" class="btn btn-primary">Add question</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h2 style="margin:0; font-size:15px;">{{ $quiz->title }} — {{ $questions->count() }} question(s)</h2></div>
        @if ($questions->isEmpty())
            <div class="empty-state"><h3>No questions yet</h3></div>
        @else
            <table class="ledger">
                <thead><tr><th>#</th><th>Question</th><th>Correct answer</th><th>Points</th><th></th></tr></thead>
                <tbody>
                    @foreach ($questions as $i => $question)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $question->question_text }}</td>
                            <td>{{ $question->correct_answer }}</td>
                            <td>{{ $question->points }}</td>
                            <td style="text-align:right;">
                                <form method="POST" action="{{ route('teacher.quizzes.questions.destroy', [$quiz, $question]) }}" style="display:inline;" onsubmit="return confirm('Delete this question?');">
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
