<?php

namespace App\Http\Controllers\Teacher;

use App\Models\Attendance;
use App\Models\Subject;
use App\Services\AttendanceAlertService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends TeacherController
{
    public function __construct(private AttendanceAlertService $attendanceAlerts)
    {
    }

    /** GET /teacher/subjects/{subject}/attendance?date=2026-08-02 */
    public function index(Request $request, Subject $subject)
    {
        $this->authorizeSubjectOwnership($subject);

        $date = $request->query('date', now()->toDateString());

        $students = $subject->students()->with('user')->orderBy('student_number')->get();
        $existing = $subject->attendance()->where('date', $date)->get()->keyBy('student_id');

        return view('teacher.attendance.index', compact('subject', 'students', 'existing', 'date'));
    }

    /**
     * POST /teacher/subjects/{subject}/attendance — saves the whole day's
     * roster in one submit (replaces the legacy per-row AJAX save calls).
     * Payload: date, statuses[student_id] => Present|Late|Absent|Excused
     */
    public function store(Request $request, Subject $subject): RedirectResponse
    {
        $this->authorizeSubjectMutable($subject);

        $validated = $request->validate([
            'date' => ['required', 'date'],
            'statuses' => ['required', 'array'],
            'statuses.*' => ['required', 'in:Present,Late,Absent,Excused'],
        ]);

        $students = $subject->students()->with('user')->get()->keyBy('id');

        foreach ($validated['statuses'] as $studentId => $status) {
            Attendance::updateOrCreate(
                ['subject_id' => $subject->id, 'student_id' => $studentId, 'date' => $validated['date']],
                ['status' => $status]
            );

            if ($status === 'Absent' && $students->has($studentId)) {
                $this->attendanceAlerts->checkStudent($subject, $students->get($studentId));
            }
        }

        return redirect()
            ->route('teacher.subjects.attendance.index', ['subject' => $subject, 'date' => $validated['date']])
            ->with('status', 'Attendance saved for ' . Carbon::parse($validated['date'])->format('M j, Y') . '.');
    }
}
