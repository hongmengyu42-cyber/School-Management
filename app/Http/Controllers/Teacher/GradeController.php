<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Requests\Teacher\StoreGradeRequest;
use App\Models\Grade;
use App\Models\GradeCategory;
use App\Models\Subject;
use App\Notifications\GradePosted;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class GradeController extends TeacherController
{
    /** GET /teacher/subjects/{subject}/grades */
    public function index(Subject $subject)
    {
        $this->authorizeSubjectOwnership($subject);

        return view('teacher.grades.index', [
            'subject' => $subject,
            'students' => $subject->students()->with('user')->orderBy('student_number')->get(),
            'categories' => $subject->gradeCategories,
            'grades' => $subject->grades()->get()->groupBy('student_id'),
        ]);
    }

    /** POST /teacher/subjects/{subject}/grades — record a grade for one student. */
    public function store(StoreGradeRequest $request, Subject $subject): RedirectResponse
    {
        $grade = $subject->grades()->create($request->validated());

        // Notifies the student and every linked parent in one go — a
        // failing grade is exactly the kind of thing parents shouldn't
        // have to log in to discover.
        Notification::send($grade->student->notifiableUsers(), new GradePosted($grade));

        return redirect()->route('teacher.subjects.grades.index', $subject)->with('status', 'Grade recorded.');
    }

    /** PUT /teacher/subjects/{subject}/grades/{grade} */
    public function update(StoreGradeRequest $request, Subject $subject, Grade $grade): RedirectResponse
    {
        $this->authorizeSubjectMutable($subject);
        abort_unless($grade->subject_id === $subject->id, 404);

        $grade->update($request->validated());

        return redirect()->route('teacher.subjects.grades.index', $subject)->with('status', 'Grade updated.');
    }

    /** DELETE /teacher/subjects/{subject}/grades/{grade} */
    public function destroy(Subject $subject, Grade $grade): RedirectResponse
    {
        $this->authorizeSubjectMutable($subject);
        abort_unless($grade->subject_id === $subject->id, 404);

        $grade->delete();

        return redirect()->route('teacher.subjects.grades.index', $subject)->with('status', 'Grade deleted.');
    }

    /**
     * POST /teacher/subjects/{subject}/grade-categories — quick-add a
     * weighted category (e.g. "Exams", 60%) without leaving the grades page.
     */
    public function storeCategory(Request $request, Subject $subject): RedirectResponse
    {
        $this->authorizeSubjectMutable($subject);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'weight_percent' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        $subject->gradeCategories()->create($validated);

        return redirect()->route('teacher.subjects.grades.index', $subject)->with('status', 'Category added.');
    }

    public function destroyCategory(Subject $subject, GradeCategory $category): RedirectResponse
    {
        $this->authorizeSubjectMutable($subject);
        abort_unless($category->subject_id === $subject->id, 404);

        $category->delete();

        return redirect()->route('teacher.subjects.grades.index', $subject)->with('status', 'Category deleted.');
    }
}
