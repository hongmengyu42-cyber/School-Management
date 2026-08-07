@extends('layouts.app')
@section('title', $subject->subject_name)
@section('content')
    <div style="display:flex; gap:8px; margin-bottom:20px; flex-wrap:wrap;">
        <a href="{{ route('student.subjects.assignments.index', $subject) }}" class="btn btn-ghost">Assignments</a>
        <a href="{{ route('student.subjects.quizzes.index', $subject) }}" class="btn btn-ghost">Quizzes</a>
        <form method="POST" action="{{ route('student.subjects.message-teacher', $subject) }}">
            @csrf
            <button type="submit" class="btn btn-ghost">Message Teacher</button>
        </form>
    </div>

    <div class="card" style="margin-bottom:20px;">
        <div class="card-header"><h2 style="margin:0; font-size:15px;">Your grades</h2></div>
        @if ($grades->isEmpty())
            <div class="empty-state"><h3>No grades recorded yet</h3></div>
        @else
            <table class="ledger">
                <thead><tr><th>Category</th><th>Label</th><th>Score</th><th>Status</th></tr></thead>
                <tbody>
                    @foreach ($grades as $grade)
                        <tr>
                            <td>{{ $grade->category?->name ?? '—' }}</td>
                            <td>{{ $grade->label ?? '—' }}</td>
                            <td>{{ $grade->grade_value }}</td>
                            <td><span class="badge {{ $grade->status === 'Passed' ? 'badge-active' : 'badge-suspended' }}">{{ $grade->status }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="card">
        <div class="card-header"><h2 style="margin:0; font-size:15px;">Recent attendance</h2></div>
        @if ($attendance->isEmpty())
            <div class="empty-state"><h3>No attendance recorded yet</h3></div>
        @else
            <table class="ledger">
                <thead><tr><th>Date</th><th>Status</th></tr></thead>
                <tbody>
                    @foreach ($attendance as $record)
                        <tr>
                            <td>{{ $record->date->format('M j, Y') }}</td>
                            <td>{{ $record->status }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
