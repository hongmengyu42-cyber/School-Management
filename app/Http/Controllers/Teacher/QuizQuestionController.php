<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Requests\Teacher\StoreQuizQuestionRequest;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class QuizQuestionController extends TeacherController
{
    public function index(Quiz $quiz)
    {
        Gate::authorize('update', $quiz->subject);

        return view('teacher.quizzes.questions', [
            'quiz' => $quiz->load('subject'),
            'questions' => $quiz->questions,
        ]);
    }

    public function store(StoreQuizQuestionRequest $request, Quiz $quiz): RedirectResponse
    {
        $quiz->questions()->create($request->validated());

        return redirect()->route('teacher.quizzes.questions.index', $quiz)->with('status', 'Question added.');
    }

    public function destroy(Quiz $quiz, QuizQuestion $question): RedirectResponse
    {
        Gate::authorize('update', $quiz->subject);
        abort_unless($question->quiz_id === $quiz->id, 404);

        $question->delete();

        return redirect()->route('teacher.quizzes.questions.index', $quiz)->with('status', 'Question removed.');
    }
}
