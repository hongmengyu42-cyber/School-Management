@extends('layouts.app')
@section('title', 'Submissions — ' . $assignment->title)
@section('content')
    <div class="card">
        <div class="card-header"><h2 style="margin:0; font-size:15px;">Submissions for "{{ $assignment->title }}"</h2></div>
        @if ($submissions->isEmpty())
            <div class="empty-state"><h3>No submissions yet</h3></div>
        @else
            <table class="ledger">
                <thead><tr><th>Student</th><th>Submitted</th><th>File</th><th>Score</th><th>Feedback</th><th></th></tr></thead>
                <tbody>
                    @foreach ($submissions as $submission)
                        <tr>
                            <td>{{ $submission->student->user->full_name }}</td>
                            <td>{{ $submission->submitted_at?->format('M j, g:ia') ?? 'Not submitted' }}</td>
                            <td>
                                @if ($submission->file_path)
                                    <a href="{{ Storage::url($submission->file_path) }}" target="_blank">View file</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td colspan="3">
                                <form method="POST" action="{{ route('teacher.submissions.update', $submission) }}" style="display:flex; gap:8px; align-items:center;">
                                    @csrf @method('PUT')
                                    <input type="number" step="0.01" min="0" max="{{ $assignment->max_points }}" name="score" value="{{ $submission->score }}" style="width:80px; padding:6px 8px; border:1px solid var(--line); border-radius:6px;" placeholder="Score">
                                    <input type="text" name="feedback" value="{{ $submission->feedback }}" style="flex:1; padding:6px 8px; border:1px solid var(--line); border-radius:6px;" placeholder="Feedback">
                                    <button type="submit" class="btn btn-primary btn-sm">Save</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
