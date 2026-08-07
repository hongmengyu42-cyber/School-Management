<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\StoreAcademicYearRequest;
use App\Http\Requests\Admin\UpdateAcademicYearRequest;
use App\Models\AcademicYear;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class AcademicYearController extends AdminController
{
    public function index()
    {
        return view('admin.academic-years.index', [
            'academicYears' => AcademicYear::withCount('semesters')->orderByDesc('year_label')->paginate(20),
        ]);
    }

    public function create()
    {
        return view('admin.academic-years.create');
    }

    public function store(StoreAcademicYearRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            if ($request->boolean('is_current')) {
                AcademicYear::where('is_current', true)->update(['is_current' => false]);
            }

            AcademicYear::create($request->validated());
        });

        $this->logActivity($request, 'academic_year.created', "Created academic year {$request->year_label}");

        return redirect()->route('admin.academic-years.index')->with('status', 'Academic year created.');
    }

    public function edit(AcademicYear $academicYear)
    {
        return view('admin.academic-years.edit', compact('academicYear'));
    }

    public function update(UpdateAcademicYearRequest $request, AcademicYear $academicYear): RedirectResponse
    {
        DB::transaction(function () use ($request, $academicYear) {
            if ($request->boolean('is_current')) {
                AcademicYear::where('id', '!=', $academicYear->id)->update(['is_current' => false]);
            }

            $academicYear->update($request->validated());
        });

        $this->logActivity($request, 'academic_year.updated', "Updated academic year {$academicYear->year_label}");

        return redirect()->route('admin.academic-years.index')->with('status', 'Academic year updated.');
    }

    public function destroy(AcademicYear $academicYear): RedirectResponse
    {
        $label = $academicYear->year_label;
        $academicYear->delete();

        $this->logActivity(request(), 'academic_year.deleted', "Deleted academic year {$label}");

        return redirect()->route('admin.academic-years.index')->with('status', 'Academic year deleted.');
    }
}
