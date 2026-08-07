<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

abstract class ParentController extends Controller
{
    /**
     * Aborts 403 unless this parent is actually linked to the given
     * student — every parent-facing read needs this, since a parent
     * account should never be able to view an arbitrary student by
     * guessing an ID in the URL.
     */
    protected function authorizeLinkedStudent(Request $request, Student $student): void
    {
        abort_unless(
            $request->user()->linkedStudents->contains('id', $student->id),
            403
        );
    }
}
