@extends('layouts.app')
@section('title', 'My Attendance')
@section('content')
    @foreach ($summaries as $summary)
        <div class="card" style="margin-bottom:16px;">
            <div class="card-header">
                <h2 style="margin:0; font-size:15px;">{{ $summary['subject']->subject_name }}</h2>
                <span class="badge {{ ($summary['percentage'] ?? 100) >= 75 ? 'badge-active' : 'badge-suspended' }}">
                    {{ $summary['percentage'] ?? '—' }}@if($summary['percentage'] !== null)%@endif attendance
                </span>
            </div>
            @if ($summary['records']->isEmpty())
                <div class="empty-state"><h3>No attendance recorded yet</h3></div>
            @else
                <table class="ledger">
                    <thead><tr><th>Date</th><th>Status</th></tr></thead>
                    <tbody>
                        @foreach ($summary['records']->take(15) as $record)
                            <tr>
                                <td>{{ $record->date->format('M j, Y') }}</td>
                                <td>
                                    <span class="badge {{ $record->isPresentOrLate() ? 'badge-active' : 'badge-suspended' }}">{{ $record->status }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    @endforeach

    @if ($summaries->isEmpty())
        <div class="card"><div class="empty-state"><h3>Not enrolled in any subjects yet</h3></div></div>
    @endif
@endsection
