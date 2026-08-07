<?php

namespace App\Http\Controllers\Student;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QuizAttemptController extends StudentController
{
    /** GET /student/quizzes/{quiz}/take — the question form. One attempt per quiz. */
    public function create(Request $request, Quiz $quiz)
    {
        $this->authorizeEnrollment($quiz->subject);
        $student = $this->currentStudent($request);

        $existing = QuizAttempt::where('quiz_id', $quiz->id)->where('student_id', $student->id)->first();
        if ($existing) {
            return redirect()->route('student.quizzes.attempts.show', $existing);
        }

        return view('student.quizzes.take', [
            'quiz' => $quiz->load('questions'),
        ]);
    }

    /**
     * POST /student/quizzes/{quiz}/take — auto-grades on submit, matching
     * the legacy system's immediate-feedback behavior. Answers are compared
     * case-insensitively with whitespace trimmed.
     */
    public function store(Request $request, Quiz $quiz): RedirectResponse
    {
        $this->authorizeEnrollment($quiz->subject);
        $student = $this->currentStudent($request);

        if (QuizAttempt::where('quiz_id', $quiz->id)->where('student_id', $student->id)->exists()) {
            return redirect()->route('student.subjects.quizzes.index', $quiz->subject)
                ->with('status', 'You have already attempted this quiz.');
        }

        $validated = $request->validate(['answers' => ['required', 'array']]);

        $questions = $quiz->questions;
        $totalPoints = $questions->sum('points');
        $earnedPoints = 0;

        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'student_id' => $student->id,
            'started_at' => now(),
        ]);

        foreach ($questions as $question) {
            $given = trim((string) ($validated['answers'][$question->id] ?? ''));
            $isCorrect = Str::lower($given) === Str::lower(trim($question->correct_answer));

            if ($isCorrect) {
                $earnedPoints += $question->points;
            }

            $attempt->answers()->create([
                'quiz_question_id' => $question->id,
                'given_answer' => $given,
                'is_correct' => $isCorrect,
            ]);
        }

        $attempt->update([
            'score' => $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100, 2) : 0,
            'submitted_at' => now(),
        ]);

        return redirect()->route('student.quizzes.attempts.show', $attempt)->with('status', 'Quiz submitted.');
    }

    /** GET /student/quiz-attempts/{attempt} — results with per-question breakdown. */
    public function show(Request $request, QuizAttempt $attempt)
    {
        abort_unless($attempt->student_id === $this->currentStudent($request)->id, 403);

        return view('student.quizzes.results', [
            'attempt' => $attempt->load('quiz', 'answers.question'),
        ]);
    }
}
