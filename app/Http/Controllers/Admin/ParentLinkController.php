<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\LinkParentRequest;
use App\Models\ParentStudentLink;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class ParentLinkController extends AdminController
{
    public function index()
    {
        return view('admin.parent-links.index', [
            'links' => ParentStudentLink::with('parentUser', 'student.user')->latest()->paginate(30),
            'parents' => User::where('role', 'Parent')->orderBy('full_name')->get(),
            'students' => Student::with('user')->get()->sortBy('user.full_name'),
        ]);
    }

    public function store(LinkParentRequest $request): RedirectResponse
    {
        ParentStudentLink::firstOrCreate([
            'parent_user_id' => $request->parent_user_id,
            'student_id' => $request->student_id,
        ]);

        $this->logActivity(
            $request,
            'parent.linked',
            "Linked parent #{$request->parent_user_id} to student #{$request->student_id}"
        );

        return back()->with('status', 'Parent linked to student.');
    }

    public function destroy(ParentStudentLink $parentLink): RedirectResponse
    {
        $parentLink->delete();

        $this->logActivity(request(), 'parent.unlinked', "Removed parent link #{$parentLink->id}");

        return back()->with('status', 'Link removed.');
    }
}
