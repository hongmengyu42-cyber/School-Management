@extends('layouts.app')
@section('title', 'Report Cards')
@section('content')
    <div class="card">
        <div class="card-header"><h2 style="margin:0; font-size:15px;">My report cards</h2></div>
        @if ($semesters->isEmpty())
            <div class="empty-state">
                <h3>No report cards yet</h3>
                <p>These appear once you're enrolled in a subject for a semester.</p>
            </div>
        @else
            <table class="ledger">
                <thead><tr><th>Semester</th><th>Academic year</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    @foreach ($semesters as $semester)
                        <tr>
                            <td>{{ $semester->name }}</td>
                            <td>{{ $semester->academicYear->year_label ?? '—' }}</td>
                            <td><span class="badge {{ $semester->is_locked ? 'badge-active' : 'badge-pending' }}">{{ $semester->is_locked ? 'Official' : 'In progress' }}</span></td>
                            <td style="text-align:right;">
                                <a href="{{ route('student.report-card.show', $semester) }}" class="btn btn-ghost btn-sm" target="_blank">Download PDF</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
