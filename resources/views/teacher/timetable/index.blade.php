@extends('layouts.app')
@section('title', 'Timetable')
@section('content')
    <div class="card">
        <div class="card-header"><h2 style="margin:0; font-size:15px;">Your weekly schedule</h2></div>
        @if ($subjects->isEmpty())
            <div class="empty-state"><h3>No subjects assigned yet</h3></div>
        @else
            <table class="ledger">
                <thead><tr><th>Subject</th><th>Days</th><th>Time</th><th>Room</th></tr></thead>
                <tbody>
                    @foreach ($subjects as $subject)
                        <tr>
                            <td>{{ $subject->subject_name }}</td>
                            <td>{{ $subject->days_of_week ?? '—' }}</td>
                            <td>{{ $subject->time_slot ?? '—' }}</td>
                            <td>{{ $subject->room_number ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
