@extends('layouts.app')
@section('title', 'System Settings')
@section('content')
    <div class="card" style="max-width:480px;">
        <div class="card-header"><h2 style="margin:0; font-size:15px;">System settings</h2></div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.system-settings.update') }}">
                @csrf @method('PUT')
                <div class="field">
                    <label for="school_name">School name</label>
                    <input id="school_name" type="text" name="school_name" value="{{ old('school_name', $schoolName) }}" required>
                </div>
                <div class="field">
                    <label for="passing_grade_threshold">Passing grade threshold (%)</label>
                    <input id="passing_grade_threshold" type="number" step="0.1" min="0" max="100" name="passing_grade_threshold" value="{{ old('passing_grade_threshold', $passingThreshold) }}" required>
                    <div class="field-hint">Grades at or above this percentage are marked "Passed" system-wide.</div>
                </div>
                <div class="field">
                    <label for="consecutive_absence_alert_threshold">Consecutive absences before alert</label>
                    <input id="consecutive_absence_alert_threshold" type="number" step="1" min="1" max="30" name="consecutive_absence_alert_threshold" value="{{ old('consecutive_absence_alert_threshold', $consecutiveAbsenceAlertThreshold) }}" required>
                    <div class="field-hint">Students and linked parents get an email/notification once a student reaches this many consecutive "Absent" marks in a subject.</div>
                </div>
                <button type="submit" class="btn btn-primary">Save settings</button>
            </form>
        </div>
    </div>
@endsection
