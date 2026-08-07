<?php

namespace App\Http\Controllers\Student;

use App\Models\Quiz;
use App\Models\Subject;
use Illuminate\Http\Request;

class QuizController extends StudentController
{
    public function index(Request $request, Subject $subject)
    {
        $this->authorizeEnrollment($subject);
        $student = $this->currentStudent($request);

        $quizzes = $subject->quizzes()->withCount('questions')->with([
            'attempts' => fn ($q) => $q->where('student_id', $student->id),
        ])->get();

        return view('student.quizzes.index', compact('subject', 'quizzes'));
    }
}
