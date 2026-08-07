@extends('layouts.app')
@section('title', 'Assignments — ' . $subject->subject_name)
@section('content')
    <div class="card">
        <div class="card-header"><h2 style="margin:0; font-size:15px;">Assignments</h2></div>
        @if ($assignments->isEmpty())
            <div class="empty-state"><h3>No assignments posted yet</h3></div>
        @else
            <table class="ledger">
                <thead><tr><th>Title</th><th>Due</th><th>Status</th><th>Score</th><th></th></tr></thead>
                <tbody>
                    @foreach ($assignments as $assignment)
                        @php($submission = $assignment->submissions->first())
                        <tr>
                            <td>{{ $assignment->title }}</td>
                            <td>{{ $assignment->due_date?->format('M j, Y g:ia') ?? '—' }}</td>
                            <td>
                                @if ($submission)
                                    <span class="badge badge-active">Submitted</span>
                                @else
                                    <span class="badge badge-pending">Not submitted</span>
                                @endif
                            </td>
                            <td>{{ $submission?->score ?? '—' }} / {{ $assignment->max_points }}</td>
                            <td style="text-align:right;">
                                <a href="{{ route('student.subjects.assignments.show', [$subject, $assignment]) }}" class="btn btn-ghost btn-sm">Open</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
