<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Requests\Teacher\StoreQuizRequest;
use App\Models\Quiz;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;

class QuizController extends TeacherController
{
    public function index(Subject $subject)
    {
        $this->authorizeSubjectOwnership($subject);

        return view('teacher.quizzes.index', [
            'subject' => $subject,
            'quizzes' => $subject->quizzes()->withCount('questions', 'attempts')->latest()->get(),
        ]);
    }

    public function create(Subject $subject)
    {
        $this->authorizeSubjectOwnership($subject);

        return view('teacher.quizzes.create', compact('subject'));
    }

    public function store(StoreQuizRequest $request, Subject $subject): RedirectResponse
    {
        $quiz = $subject->quizzes()->create($request->validated());

        return redirect()->route('teacher.quizzes.questions.index', $quiz)->with('status', 'Quiz created — now add questions.');
    }

    public function destroy(Subject $subject, Quiz $quiz): RedirectResponse
    {
        $this->authorizeSubjectOwnership($subject);
        abort_unless($quiz->subject_id === $subject->id, 404);

        $quiz->delete();

        return redirect()->route('teacher.subjects.quizzes.index', $subject)->with('status', 'Quiz deleted.');
    }
}
