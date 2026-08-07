@extends('layouts.app')
@section('title', 'My Subjects')
@section('content')
    <div class="card">
        <div class="card-header"><h2 style="margin:0; font-size:15px;">Subjects you teach</h2></div>
        @if ($subjects->isEmpty())
            <div class="empty-state">
                <h3>No subjects assigned yet</h3>
                <p>An admin needs to assign you to a subject before you can enter grades or attendance.</p>
            </div>
        @else
            <table class="ledger">
                <thead><tr><th>Code</th><th>Name</th><th>Semester</th><th>Enrolled</th><th></th></tr></thead>
                <tbody>
                    @foreach ($subjects as $subject)
                        <tr>
                            <td>{{ $subject->subject_code }}</td>
                            <td>{{ $subject->subject_name }}</td>
                            <td>
                                {{ $subject->semester?->academicYear?->year_label }} — {{ $subject->semester?->name }}
                                @if ($subject->isLocked())<span class="badge badge-locked" style="margin-left:6px;">Locked</span>@endif
                            </td>
                            <td>{{ $subject->students_count }}</td>
                            <td style="text-align:right;">
                                <a href="{{ route('teacher.subjects.show', $subject) }}" class="btn btn-primary btn-sm">Open</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
    <div style="margin-top:16px;">{{ $subjects->links() }}</div>
@endsection
