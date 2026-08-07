<div class="field">
    <label for="academic_year_id">Academic year</label>
    <select id="academic_year_id" name="academic_year_id" required>
        <option value="">Select an academic year&hellip;</option>
        @foreach ($academicYears as $year)
            <option value="{{ $year->id }}" {{ old('academic_year_id', $semester->academic_year_id ?? '') == $year->id ? 'selected' : '' }}>{{ $year->year_label }}</option>
        @endforeach
    </select>
</div>
<div class="field">
    <label for="name">Semester name</label>
    <input id="name" type="text" name="name" value="{{ old('name', $semester->name ?? '') }}" placeholder="Fall Semester" required>
</div>
<div class="field">
    <label for="start_date">Start date</label>
    <input id="start_date" type="date" name="start_date" value="{{ old('start_date', optional($semester->start_date ?? null)->format('Y-m-d')) }}">
</div>
<div class="field">
    <label for="end_date">End date</label>
    <input id="end_date" type="date" name="end_date" value="{{ old('end_date', optional($semester->end_date ?? null)->format('Y-m-d')) }}">
</div>
<div class="field">
    <label style="display:flex; align-items:center; gap:8px; font-weight:400;">
        <input type="checkbox" name="is_current" value="1" style="width:auto;" {{ old('is_current', $semester->is_current ?? false) ? 'checked' : '' }}>
        Set as the current semester
    </label>
</div>
<div class="field">
    <label style="display:flex; align-items:center; gap:8px; font-weight:400;">
        <input type="checkbox" name="is_locked" value="1" style="width:auto;" {{ old('is_locked', $semester->is_locked ?? false) ? 'checked' : '' }}>
        Term-locked (grades and attendance become read-only)
    </label>
</div>
