@extends('layouts.app')
@section('title', $assignment->title)
@section('content')
    <div class="card" style="max-width:600px;">
        <div class="card-header"><h2 style="margin:0; font-size:15px;">{{ $assignment->title }}</h2></div>
        <div class="card-body">
            <p style="color:var(--ink-soft);">{{ $assignment->description ?? 'No description provided.' }}</p>
            <p style="font-size:12.5px; color:var(--muted);">
                Due {{ $assignment->due_date?->format('M j, Y g:ia') ?? 'No due date' }} · Worth {{ $assignment->max_points }} points
            </p>

            @if ($submission)
                <div class="flash">
                    Submitted {{ $submission->submitted_at->format('M j, g:ia') }}.
                    @if ($submission->score !== null)
                        Score: {{ $submission->score }} / {{ $assignment->max_points }}.
                    @else
                        Awaiting grading.
                    @endif
                    @if ($submission->feedback)
                        <br>Feedback: {{ $submission->feedback }}
                    @endif
                </div>
            @endif

            <form method="POST" action="{{ route('student.subjects.assignments.store', [$subject, $assignment]) }}" enctype="multipart/form-data">
                @csrf
                <div class="field">
                    <label for="file">{{ $submission ? 'Re-submit file' : 'Upload your submission' }}</label>
                    <input id="file" type="file" name="file" required>
                    <div class="field-hint">PDF, Word, text, zip, or image — max 10MB.</div>
                </div>
                <button type="submit" class="btn btn-primary">{{ $submission ? 'Re-submit' : 'Submit' }}</button>
            </form>
        </div>
    </div>
@endsection
