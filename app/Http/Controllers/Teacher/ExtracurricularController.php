<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Requests\Teacher\StoreExtracurricularRequest;
use App\Models\ExtracurricularActivity;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;

class ExtracurricularController extends TeacherController
{
    public function index(Subject $subject)
    {
        $this->authorizeSubjectOwnership($subject);

        $studentIds = $subject->students()->pluck('students.id');

        return view('teacher.extracurricular.index', [
            'subject' => $subject,
            'students' => $subject->students()->with('user')->orderBy('student_number')->get(),
            'activities' => ExtracurricularActivity::whereIn('student_id', $studentIds)->with('student.user')->latest('date_recorded')->get(),
        ]);
    }

    public function store(StoreExtracurricularRequest $request, Subject $subject): RedirectResponse
    {
        ExtracurricularActivity::create($request->validated());

        return redirect()->route('teacher.subjects.extracurricular.index', $subject)->with('status', 'Activity recorded.');
    }

    public function destroy(Subject $subject, ExtracurricularActivity $activity): RedirectResponse
    {
        $this->authorizeSubjectOwnership($subject);

        $activity->delete();

        return redirect()->route('teacher.subjects.extracurricular.index', $subject)->with('status', 'Activity removed.');
    }
}
