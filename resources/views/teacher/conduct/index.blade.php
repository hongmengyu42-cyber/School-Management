@extends('layouts.app')
@section('title', 'Conduct — ' . $subject->subject_name)
@section('content')
    <div class="card" style="margin-bottom:20px;">
        <div class="card-header"><h2 style="margin:0; font-size:15px;">Record an incident</h2></div>
        <div class="card-body">
            <form method="POST" action="{{ route('teacher.subjects.conduct.store', $subject) }}" style="display:flex; gap:8px; align-items:flex-end; flex-wrap:wrap;">
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
                    <label for="type">Type</label>
                    <select id="type" name="type" required>
                        <option value="Positive">Positive</option>
                        <option value="Negative">Negative</option>
                    </select>
                </div>
                <div class="field" style="margin:0; flex:1;">
                    <label for="description">Description</label>
                    <input id="description" type="text" name="description" required>
                </div>
                <div class="field" style="margin:0;">
                    <label for="incident_date">Date</label>
                    <input id="incident_date" type="date" name="incident_date" value="{{ now()->toDateString() }}" required>
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h2 style="margin:0; font-size:15px;">Conduct records</h2></div>
        @if ($records->isEmpty())
            <div class="empty-state"><h3>No records yet</h3></div>
        @else
            <table class="ledger">
                <thead><tr><th>Date</th><th>Student</th><th>Type</th><th>Description</th><th></th></tr></thead>
                <tbody>
                    @foreach ($records as $record)
                        <tr>
                            <td>{{ $record->incident_date->format('M j, Y') }}</td>
                            <td>{{ $record->student->user->full_name }}</td>
                            <td><span class="badge {{ $record->type === 'Positive' ? 'badge-active' : 'badge-suspended' }}">{{ $record->type }}</span></td>
                            <td>{{ $record->description }}</td>
                            <td style="text-align:right;">
                                <form method="POST" action="{{ route('teacher.subjects.conduct.destroy', [$subject, $record]) }}" style="display:inline;" onsubmit="return confirm('Delete this record?');">
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
