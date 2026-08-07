<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\StoreDepartmentRequest;
use App\Http\Requests\Admin\UpdateDepartmentRequest;
use App\Models\Department;
use Illuminate\Http\RedirectResponse;

class DepartmentController extends AdminController
{
    public function index()
    {
        return view('admin.departments.index', [
            'departments' => Department::withCount('students', 'subjects')->orderBy('department_name')->paginate(20),
        ]);
    }

    public function create()
    {
        return view('admin.departments.create');
    }

    public function store(StoreDepartmentRequest $request): RedirectResponse
    {
        $department = Department::create($request->validated());

        $this->logActivity($request, 'department.created', "Created department {$department->department_name}");

        return redirect()->route('admin.departments.index')->with('status', 'Department created.');
    }

    public function edit(Department $department)
    {
        return view('admin.departments.edit', compact('department'));
    }

    public function update(UpdateDepartmentRequest $request, Department $department): RedirectResponse
    {
        $department->update($request->validated());

        $this->logActivity($request, 'department.updated', "Updated department {$department->department_name}");

        return redirect()->route('admin.departments.index')->with('status', 'Department updated.');
    }

    public function destroy(Department $department): RedirectResponse
    {
        $name = $department->department_name;
        $department->delete();

        $this->logActivity(request(), 'department.deleted', "Deleted department {$name}");

        return redirect()->route('admin.departments.index')->with('status', 'Department deleted.');
    }
}
