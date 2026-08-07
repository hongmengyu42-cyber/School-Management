@extends('layouts.app')
@section('title', 'Attendance — ' . $subject->subject_name)
@section('content')
    @if ($subject->isLocked())
        <div class="flash flash-error">This term is locked. Attendance is shown but cannot be edited.</div>
    @endif

    <div class="card" style="margin-bottom:20px;">
        <div class="card-body">
            <form method="GET" action="{{ route('teacher.subjects.attendance.index', $subject) }}" style="display:flex; gap:8px; align-items:flex-end;">
                <div class="field" style="margin:0;">
                    <label for="date">Date</label>
                    <input id="date" type="date" name="date" value="{{ $date }}" onchange="this.form.submit()">
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h2 style="margin:0; font-size:15px;">{{ \Illuminate\Support\Carbon::parse($date)->format('l, F j, Y') }}</h2></div>

        @if ($students->isEmpty())
            <div class="empty-state"><h3>No students enrolled yet</h3></div>
        @else
            <form method="POST" action="{{ route('teacher.subjects.attendance.store', $subject) }}">
                @csrf
                <input type="hidden" name="date" value="{{ $date }}">
                <table class="ledger">
                    <thead><tr><th>Student</th>
                        @foreach (['Present', 'Late', 'Absent', 'Excused'] as $status)
                            <th>{{ $status }}</th>
                        @endforeach
                    </tr></thead>
                    <tbody>
                        @foreach ($students as $student)
                            @php($current = $existing->get($student->id)?->status ?? 'Present')
                            <tr>
                                <td>{{ $student->student_number }} — {{ $student->user->full_name }}</td>
                                @foreach (['Present', 'Late', 'Absent', 'Excused'] as $status)
                                    <td>
                                        <input type="radio" name="statuses[{{ $student->id }}]" value="{{ $status }}"
                                            {{ $current === $status ? 'checked' : '' }}
                                            {{ $subject->isLocked() ? 'disabled' : '' }}>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @unless ($subject->isLocked())
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary">Save attendance</button>
                    </div>
                @endunless
            </form>
        @endif
    </div>
@endsection
