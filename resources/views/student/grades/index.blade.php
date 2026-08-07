@extends('layouts.app')
@section('title', 'My Grades')
@section('content')
    <div class="card" style="margin-bottom:20px;">
        <div class="card-body" style="display:flex; gap:32px;">
            <div>
                <div style="font-size:11px; text-transform:uppercase; letter-spacing:0.06em; color:var(--muted);">Overall average</div>
                <div class="display" style="font-size:28px;">{{ $overallAverage ?? '—' }}@if($overallAverage)%@endif</div>
            </div>
            <div>
                <div style="font-size:11px; text-transform:uppercase; letter-spacing:0.06em; color:var(--muted);">GPA (4.0 scale)</div>
                <div class="display" style="font-size:28px;">{{ $overallGpa ?? '—' }}</div>
            </div>
        </div>
    </div>

    @foreach ($subjectSummaries as $summary)
        <div class="card" style="margin-bottom:16px;">
            <div class="card-header">
                <h2 style="margin:0; font-size:15px;">{{ $summary['subject']->subject_name }}</h2>
                <span class="display" style="font-size:16px;">{{ $summary['average'] ?? '—' }}@if($summary['average'])%@endif</span>
            </div>
            @if ($summary['grades']->isEmpty())
                <div class="empty-state"><h3>No grades yet</h3></div>
            @else
                <table class="ledger">
                    <thead><tr><th>Category</th><th>Label</th><th>Score</th><th>Status</th></tr></thead>
                    <tbody>
                        @foreach ($summary['grades'] as $grade)
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
    @endforeach
@endsection
