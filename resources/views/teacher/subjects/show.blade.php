@extends('layouts.app')
@section('title', $subject->subject_name)
@section('content')
    <div style="display:flex; gap:8px; margin-bottom:20px; flex-wrap:wrap;">
        <a href="{{ route('teacher.subjects.grades.index', $subject) }}" class="btn btn-ghost">Grades</a>
        <a href="{{ route('teacher.subjects.attendance.index', $subject) }}" class="btn btn-ghost">Attendance</a>
        <a href="{{ route('teacher.subjects.assignments.index', $subject) }}" class="btn btn-ghost">Assignments</a>
        <a href="{{ route('teacher.subjects.quizzes.index', $subject) }}" class="btn btn-ghost">Quizzes</a>
        <a href="{{ route('teacher.subjects.conduct.index', $subject) }}" class="btn btn-ghost">Conduct</a>
        <a href="{{ route('teacher.subjects.extracurricular.index', $subject) }}" class="btn btn-ghost">Extracurricular</a>
    </div>

    <div class="card">
        <div class="card-header">
            <h2 style="margin:0; font-size:15px;">Roster — {{ $subject->subject_name }}</h2>
            @if ($subject->isLocked())<span class="badge badge-locked">Term locked</span>@endif
        </div>
        @if ($students->isEmpty())
            <div class="empty-state"><h3>No students enrolled yet</h3></div>
        @else
            <table class="ledger">
                <thead><tr><th>Student #</th><th>Name</th><th>Year Level</th></tr></thead>
                <tbody>
                    @foreach ($students as $student)
                        <tr>
                            <td>{{ $student->student_number }}</td>
                            <td>{{ $student->user->full_name }}</td>
                            <td>{{ $student->year_level }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
