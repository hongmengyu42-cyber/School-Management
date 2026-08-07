@extends('layouts.app')
@section('title', 'Extracurricular — ' . $subject->subject_name)
@section('content')
    <div class="card" style="margin-bottom:20px;">
        <div class="card-header"><h2 style="margin:0; font-size:15px;">Record an activity</h2></div>
        <div class="card-body">
            <form method="POST" action="{{ route('teacher.subjects.extracurricular.store', $subject) }}" style="display:flex; gap:8px; align-items:flex-end; flex-wrap:wrap;">
                @csrf
                <div class="field" style="margin:0;">
                    <label for="student_id">Student</label>
                    <select id="student_id" name="student_id" required>
                        @foreach ($students as $student)
                            <option value="{{ $student->id }}">{{ $student->user->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field" style="margin:0;">
                    <label for="activity_name">Activity</label>
                    <input id="activity_name" type="text" name="activity_name" required>
                </div>
                <div class="field" style="margin:0;">
                    <label for="role">Role</label>
                    <input id="role" type="text" name="role">
                </div>
                <div class="field" style="margin:0;">
                    <label for="achievement">Achievement</label>
                    <input id="achievement" type="text" name="achievement">
                </div>
                <div class="field" style="margin:0;">
                    <label for="date_recorded">Date</label>
                    <input id="date_recorded" type="date" name="date_recorded" value="{{ now()->toDateString() }}">
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h2 style="margin:0; font-size:15px;">Activities</h2></div>
        @if ($activities->isEmpty())
            <div class="empty-state"><h3>No activities recorded yet</h3></div>
        @else
            <table class="ledger">
                <thead><tr><th>Date</th><th>Student</th><th>Activity</th><th>Role</th><th>Achievement</th><th></th></tr></thead>
                <tbody>
                    @foreach ($activities as $activity)
                        <tr>
                            <td>{{ optional($activity->date_recorded)->format('M j, Y') ?? '—' }}</td>
                            <td>{{ $activity->student->user->full_name }}</td>
                            <td>{{ $activity->activity_name }}</td>
                            <td>{{ $activity->role ?? '—' }}</td>
                            <td>{{ $activity->achievement ?? '—' }}</td>
                            <td style="text-align:right;">
                                <form method="POST" action="{{ route('teacher.subjects.extracurricular.destroy', [$subject, $activity]) }}" style="display:inline;" onsubmit="return confirm('Delete this record?');">
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
