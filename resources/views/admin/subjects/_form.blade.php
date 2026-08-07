<div class="field">
    <label for="subject_code">Subject code</label>
    <input id="subject_code" type="text" name="subject_code" value="{{ old('subject_code', $subject->subject_code ?? '') }}" required>
</div>
<div class="field">
    <label for="subject_name">Subject name</label>
    <input id="subject_name" type="text" name="subject_name" value="{{ old('subject_name', $subject->subject_name ?? '') }}" required>
</div>
<div class="field">
    <label for="access_code">Access code</label>
    <input id="access_code" type="text" name="access_code" value="{{ old('access_code', $subject->access_code ?? '') }}">
    <div class="field-hint">Students use this to self-enroll. Leave blank to auto-generate.</div>
</div>
<div class="field">
    <label for="teacher_id">Teacher</label>
    <select id="teacher_id" name="teacher_id">
        <option value="">Unassigned</option>
        @foreach ($teachers as $teacher)
            <option value="{{ $teacher->id }}" {{ old('teacher_id', $subject->teacher_id ?? '') == $teacher->id ? 'selected' : '' }}>{{ $teacher->full_name }}</option>
        @endforeach
    </select>
</div>
<div class="field">
    <label for="semester_id">Semester</label>
    <select id="semester_id" name="semester_id">
        <option value="">Unassigned</option>
        @foreach ($semesters as $semester)
            <option value="{{ $semester->id }}" {{ old('semester_id', $subject->semester_id ?? '') == $semester->id ? 'selected' : '' }}>{{ $semester->academicYear->year_label }} — {{ $semester->name }}</option>
        @endforeach
    </select>
</div>
<div class="field">
    <label for="department_id">Department</label>
    <select id="department_id" name="department_id">
        <option value="">Unassigned</option>
        @foreach ($departments as $department)
            <option value="{{ $department->id }}" {{ old('department_id', $subject->department_id ?? '') == $department->id ? 'selected' : '' }}>{{ $department->department_name }}</option>
        @endforeach
    </select>
</div>
<div class="field">
    <label for="room_number">Room</label>
    <input id="room_number" type="text" name="room_number" value="{{ old('room_number', $subject->room_number ?? '') }}">
</div>
<div class="field">
    <label for="days_of_week">Days</label>
    <input id="days_of_week" type="text" name="days_of_week" value="{{ old('days_of_week', $subject->days_of_week ?? '') }}" placeholder="Mon, Wed, Fri">
</div>
<div class="field">
    <label for="time_slot">Time slot</label>
    <input id="time_slot" type="text" name="time_slot" value="{{ old('time_slot', $subject->time_slot ?? '') }}" placeholder="9:00–10:15 AM">
</div>
