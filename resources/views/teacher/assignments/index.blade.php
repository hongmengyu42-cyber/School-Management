@extends('layouts.app')
@section('title', 'Assignments — ' . $subject->subject_name)
@section('content')
    <div class="card">
        <div class="card-header">
            <h2 style="margin:0; font-size:15px;">Assignments</h2>
            <a href="{{ route('teacher.subjects.assignments.create', $subject) }}" class="btn btn-primary btn-sm">Add assignment</a>
        </div>
        @if ($assignments->isEmpty())
            <div class="empty-state"><h3>No assignments yet</h3></div>
        @else
            <table class="ledger">
                <thead><tr><th>Title</th><th>Due</th><th>Max Points</th><th>Submissions</th><th></th></tr></thead>
                <tbody>
                    @foreach ($assignments as $assignment)
                        <tr>
                            <td>{{ $assignment->title }}</td>
                            <td>{{ $assignment->due_date?->format('M j, Y g:ia') ?? '—' }}</td>
                            <td>{{ $assignment->max_points }}</td>
                            <td>{{ $assignment->submissions_count }}</td>
                            <td style="text-align:right;">
                                <a href="{{ route('teacher.assignments.submissions.index', $assignment) }}" class="btn btn-ghost btn-sm">Submissions</a>
                                <a href="{{ route('teacher.subjects.assignments.edit', [$subject, $assignment]) }}" class="btn btn-ghost btn-sm">Edit</a>
                                <form method="POST" action="{{ route('teacher.subjects.assignments.destroy', [$subject, $assignment]) }}" style="display:inline;" onsubmit="return confirm('Delete this assignment?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
