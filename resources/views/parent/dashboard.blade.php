@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
    <div class="card">
        <div class="card-header"><h2 style="margin:0; font-size:15px;">Your children</h2></div>
        @if ($summaries->isEmpty())
            <div class="empty-state">
                <h3>No children linked to your account yet</h3>
                <p>Contact the school office to have a student linked to your parent account.</p>
            </div>
        @else
            <table class="ledger">
                <thead><tr><th>Student</th><th>Subjects</th><th>Average grade</th><th>Attendance</th><th></th></tr></thead>
                <tbody>
                    @foreach ($summaries as $summary)
                        <tr>
                            <td>{{ $summary['student']->user->full_name }}</td>
                            <td>{{ $summary['subjectCount'] }}</td>
                            <td>{{ $summary['averageGrade'] ?? '—' }}@if($summary['averageGrade'])%@endif</td>
                            <td>
                                @if ($summary['attendancePercentage'] !== null)
                                    <span class="badge {{ $summary['attendancePercentage'] >= 75 ? 'badge-active' : 'badge-suspended' }}">{{ $summary['attendancePercentage'] }}%</span>
                                @else
                                    —
                                @endif
                            </td>
                            <td style="text-align:right;">
                                <a href="{{ route('parent.children.show', $summary['student']) }}" class="btn btn-primary btn-sm">View details</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
