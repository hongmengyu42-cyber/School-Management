use App\Models\Student;
use Illuminate\Support\Facades\Gate;
@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
    <div class="card" style="margin-bottom:20px;">
        <div class="card-body">
            <h2 style="margin-top:0;">Welcome back, {{ $student->user->full_name }}.</h2>
            <p style="color:var(--ink-soft); margin-bottom:0;">
                Student # {{ $student->student_number }} · Enrolled in {{ $subjects->count() }} subject(s)
                @if ($unreadThreadCount > 0)
                    · <a href="{{ route('student.messages.index') }}">{{ $unreadThreadCount }} unread message(s)</a>
                @endif
            </p>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h2 style="margin:0; font-size:15px;">Upcoming assignments</h2></div>
        @if ($upcomingAssignments->isEmpty())
            <div class="empty-state"><h3>Nothing due soon</h3></div>
        @else
            <table class="ledger">
                <thead><tr><th>Assignment</th><th>Subject</th><th>Due</th></tr></thead>
                <tbody>
                    @foreach ($upcomingAssignments as $assignment)
                        <tr>
                            <td>{{ $assignment->title }}</td>
                            <td>{{ $assignment->subject->subject_name }}</td>
                            <td>{{ $assignment->due_date->format('M j, g:ia') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div style="margin-top:16px; display:flex; gap:8px;">
        <a href="{{ route('student.subjects.index') }}" class="btn btn-ghost">My Subjects</a>
        <a href="{{ route('student.grades.index') }}" class="btn btn-ghost">My Grades</a>
        <a href="{{ route('student.enrollments.create') }}" class="btn btn-ghost">Enroll in a subject</a>
    </div>
@endsection
