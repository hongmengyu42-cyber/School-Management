@extends('layouts.app')
@section('title', 'My Subjects')
@section('content')
    <div class="card">
        <div class="card-header">
            <h2 style="margin:0; font-size:15px;">Enrolled subjects</h2>
            <a href="{{ route('student.enrollments.create') }}" class="btn btn-primary btn-sm">Enroll in a subject</a>
        </div>
        @if ($subjects->isEmpty())
            <div class="empty-state">
                <h3>You're not enrolled in any subjects yet</h3>
                <p>Use an access code from your teacher to enroll.</p>
            </div>
        @else
            <table class="ledger">
                <thead><tr><th>Code</th><th>Name</th><th>Teacher</th><th>Semester</th><th></th></tr></thead>
                <tbody>
                    @foreach ($subjects as $subject)
                        <tr>
                            <td>{{ $subject->subject_code }}</td>
                            <td>{{ $subject->subject_name }}</td>
                            <td>{{ $subject->teacher?->full_name ?? '—' }}</td>
                            <td>{{ $subject->semester?->academicYear?->year_label }} — {{ $subject->semester?->name }}</td>
                            <td style="text-align:right;">
                                <a href="{{ route('student.subjects.show', $subject) }}" class="btn btn-primary btn-sm">Open</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
    <div style="margin-top:16px;">{{ $subjects->links() }}</div>
@endsection
