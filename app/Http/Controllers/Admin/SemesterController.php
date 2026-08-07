<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\StoreSemesterRequest;
use App\Http\Requests\Admin\UpdateSemesterRequest;
use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class SemesterController extends AdminController
{
    public function index()
    {
        return view('admin.semesters.index', [
            'semesters' => Semester::with('academicYear')->orderByDesc('id')->paginate(20),
        ]);
    }

    public function create()
    {
        return view('admin.semesters.create', [
            'academicYears' => AcademicYear::orderByDesc('year_label')->get(),
        ]);
    }

    public function store(StoreSemesterRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            if ($request->boolean('is_current')) {
                Semester::where('is_current', true)->update(['is_current' => false]);
            }

            Semester::create($request->validated());
        });

        $this->logActivity($request, 'semester.created', "Created semester {$request->name}");

        return redirect()->route('admin.semesters.index')->with('status', 'Semester created.');
    }

    public function edit(Semester $semester)
    {
        return view('admin.semesters.edit', [
            'semester' => $semester,
            'academicYears' => AcademicYear::orderByDesc('year_label')->get(),
        ]);
    }

    public function update(UpdateSemesterRequest $request, Semester $semester): RedirectResponse
    {
        DB::transaction(function () use ($request, $semester) {
            if ($request->boolean('is_current')) {
                Semester::where('id', '!=', $semester->id)->update(['is_current' => false]);
            }

            $semester->update($request->validated());
        });

        $this->logActivity(
            $request,
            'semester.updated',
            "Updated semester {$semester->name}" . ($request->boolean('is_locked') ? ' (locked)' : '')
        );

        return redirect()->route('admin.semesters.index')->with('status', 'Semester updated.');
    }

    /**
     * Dedicated toggle so the Admin UI can lock/unlock a term with one click
     * without resubmitting the whole edit form. This is the switch that
     * flips Subject::isLocked() for every subject in the semester.
     */
    public function toggleLock(Semester $semester): RedirectResponse
    {
        $semester->update(['is_locked' => !$semester->is_locked]);

        $this->logActivity(
            request(),
            'semester.lock_toggled',
            "Semester {$semester->name} is now " . ($semester->is_locked ? 'locked' : 'unlocked')
        );

        return redirect()->route('admin.semesters.index')
            ->with('status', "Semester {$semester->name} is now " . ($semester->is_locked ? 'locked' : 'unlocked') . '.');
    }

    public function destroy(Semester $semester): RedirectResponse
    {
        $name = $semester->name;
        $semester->delete();

        $this->logActivity(request(), 'semester.deleted', "Deleted semester {$name}");

        return redirect()->route('admin.semesters.index')->with('status', 'Semester deleted.');
    }
}
