<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Requests\Teacher\StoreConductRecordRequest;
use App\Models\ConductRecord;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;

class ConductController extends TeacherController
{
    public function index(Subject $subject)
    {
        $this->authorizeSubjectOwnership($subject);

        $studentIds = $subject->students()->pluck('students.id');

        return view('teacher.conduct.index', [
            'subject' => $subject,
            'students' => $subject->students()->with('user')->orderBy('student_number')->get(),
            'records' => ConductRecord::whereIn('student_id', $studentIds)->with('student.user')->latest('incident_date')->get(),
        ]);
    }

    public function store(StoreConductRecordRequest $request, Subject $subject): RedirectResponse
    {
        ConductRecord::create($request->validated() + ['recorded_by' => $request->user()->id]);

        return redirect()->route('teacher.subjects.conduct.index', $subject)->with('status', 'Conduct record added.');
    }

    public function destroy(Subject $subject, ConductRecord $record): RedirectResponse
    {
        $this->authorizeSubjectOwnership($subject);

        $record->delete();

        return redirect()->route('teacher.subjects.conduct.index', $subject)->with('status', 'Conduct record removed.');
    }
}
