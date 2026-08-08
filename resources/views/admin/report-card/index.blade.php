@extends('layouts.app')
@section('title', $student->user->full_name . ' — Report Cards')
@section('content')
    <div class="card">
        <div class="card-header">
            <h2 style="margin:0; font-size:15px;">Report cards — {{ $student->user->full_name }} ({{ $student->student_number }})</h2>
            <a href="{{ route('admin.users.index') }}" class="btn btn-ghost btn-sm">Back to users</a>
        </div>
        @if ($semesters->isEmpty())
            <div class="empty-state">
                <h3>No report cards yet</h3>
                <p>These appear once this student is enrolled in a subject for a semester.</p>
            </div>
        @else
            <table class="ledger">
                <thead><tr><th>Semester</th><th>Academic year</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    @foreach ($semesters as $semester)
                        <tr>
                            <td>{{ $semester->name }}</td>
                            <td>{{ $semester->academicYear->year_label ?? '—' }}</td>
                            <td><span class="badge {{ $semester->is_locked ? 'badge-active' : 'badge-pending' }}">{{ $semester->is_locked ? 'Official' : 'In progress' }}</span></td>
                            <td style="text-align:right;">
                                <a href="{{ route('admin.students.report-card.show', [$student, $semester]) }}" class="btn btn-ghost btn-sm" target="_blank">Download PDF</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
