@extends('layouts.app')
@section('title', $student->user->full_name)
@section('content')
    <div class="card" style="margin-bottom:20px;">
        <div class="card-body">
            <h2 style="margin-top:0;">{{ $student->user->full_name }}</h2>
            <p style="color:var(--ink-soft); margin-bottom:0;">
                Student # {{ $student->student_number }}
                @if ($student->department) · {{ $student->department->department_name }} @endif
                @if ($student->year_level) · {{ $student->year_level }} @endif
            </p>
        </div>
    </div>

    <div class="card" style="margin-bottom:20px;">
        <div class="card-header"><h2 style="margin:0; font-size:15px;">Subjects &amp; grades</h2></div>
        @if ($subjects->isEmpty())
            <div class="empty-state"><h3>Not enrolled in any subjects yet</h3></div>
        @else
            <table class="ledger">
                <thead><tr><th>Subject</th><th>Teacher</th><th>Attendance</th><th>Grades</th></tr></thead>
                <tbody>
                    @foreach ($subjects as $subject)
                        <tr>
                            <td>{{ $subject->subject_name }}</td>
                            <td>{{ $subject->teacher?->full_name ?? '—' }}</td>
                            <td>
                                @php($att = $attendanceBySubject[$subject->id] ?? null)
                                @if ($att && $att['percentage'] !== null)
                                    <span class="badge {{ $att['percentage'] >= 75 ? 'badge-active' : 'badge-suspended' }}">{{ $att['percentage'] }}%</span>
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                @forelse ($grades[$subject->id] ?? [] as $grade)
                                    <span class="badge {{ $grade->status === 'Passed' ? 'badge-active' : 'badge-suspended' }}" style="margin-right:4px;">
                                        {{ $grade->category?->name ?? $grade->label ?? 'Grade' }}: {{ $grade->grade_value }}
                                    </span>
                                @empty
                                    —
                                @endforelse
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="card" style="margin-bottom:20px;">
        <div class="card-header"><h2 style="margin:0; font-size:15px;">Conduct records</h2></div>
        @if ($conductRecords->isEmpty())
            <div class="empty-state"><h3>No conduct records</h3></div>
        @else
            <table class="ledger">
                <thead><tr><th>Date</th><th>Type</th><th>Description</th><th>Recorded by</th></tr></thead>
                <tbody>
                    @foreach ($conductRecords as $record)
                        <tr>
                            <td>{{ $record->incident_date->format('M j, Y') }}</td>
                            <td><span class="badge {{ $record->type === 'Positive' ? 'badge-active' : 'badge-suspended' }}">{{ $record->type }}</span></td>
                            <td>{{ $record->description }}</td>
                            <td>{{ $record->recordedBy->full_name }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="card" style="margin-bottom:20px;">
        <div class="card-header"><h2 style="margin:0; font-size:15px;">Extracurricular</h2></div>
        @if ($extracurricular->isEmpty())
            <div class="empty-state"><h3>No activities recorded</h3></div>
        @else
            <table class="ledger">
                <thead><tr><th>Date</th><th>Activity</th><th>Role</th><th>Achievement</th></tr></thead>
                <tbody>
                    @foreach ($extracurricular as $activity)
                        <tr>
                            <td>{{ optional($activity->date_recorded)->format('M j, Y') ?? '—' }}</td>
                            <td>{{ $activity->activity_name }}</td>
                            <td>{{ $activity->role ?? '—' }}</td>
                            <td>{{ $activity->achievement ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="card">
        <div class="card-header"><h2 style="margin:0; font-size:15px;">Invoices</h2></div>
        @if ($invoices->isEmpty())
            <div class="empty-state"><h3>No invoices</h3></div>
        @else
            <table class="ledger">
                <thead><tr><th>Description</th><th>Amount</th><th>Due</th><th>Status</th></tr></thead>
                <tbody>
                    @foreach ($invoices as $invoice)
                        <tr>
                            <td>{{ $invoice->description }}</td>
                            <td>${{ number_format($invoice->amount, 2) }}</td>
                            <td>{{ optional($invoice->due_date)->format('M j, Y') ?? '—' }}</td>
                            <td>
                                <span class="badge {{ $invoice->status === 'Paid' ? 'badge-active' : ($invoice->status === 'Overdue' ? 'badge-suspended' : 'badge-pending') }}">{{ $invoice->status }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
