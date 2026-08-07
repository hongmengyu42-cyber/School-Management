<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\StoreSubjectRequest;
use App\Http\Requests\Admin\UpdateSubjectRequest;
use App\Models\Department;
use App\Models\Semester;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class SubjectController extends AdminController
{
    public function index()
    {
        return view('admin.subjects.index', [
            'subjects' => Subject::with(['teacher', 'semester', 'department'])
                ->withCount('students')
                ->orderBy('subject_name')
                ->paginate(20),
        ]);
    }

    public function create()
    {
        return view('admin.subjects.create', $this->formOptions());
    }

    public function store(StoreSubjectRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['access_code'] ??= Str::upper(Str::random(8));

        $subject = Subject::create($data);

        $this->logActivity($request, 'subject.created', "Created subject {$subject->subject_name}");

        return redirect()->route('admin.subjects.index')->with('status', 'Subject created.');
    }

    public function edit(Subject $subject)
    {
        return view('admin.subjects.edit', array_merge(['subject' => $subject], $this->formOptions()));
    }

    public function update(UpdateSubjectRequest $request, Subject $subject): RedirectResponse
    {
        $subject->update($request->validated());

        $this->logActivity($request, 'subject.updated', "Updated subject {$subject->subject_name}");

        return redirect()->route('admin.subjects.index')->with('status', 'Subject updated.');
    }

    public function destroy(Subject $subject): RedirectResponse
    {
        $name = $subject->subject_name;
        $subject->delete();

        $this->logActivity(request(), 'subject.deleted', "Deleted subject {$name}");

        return redirect()->route('admin.subjects.index')->with('status', 'Subject deleted.');
    }

    private function formOptions(): array
    {
        return [
            'teachers' => User::where('role', 'Teacher')->orderBy('full_name')->get(),
            'semesters' => Semester::with('academicYear')->orderByDesc('id')->get(),
            'departments' => Department::orderBy('department_name')->get(),
        ];
    }
}
