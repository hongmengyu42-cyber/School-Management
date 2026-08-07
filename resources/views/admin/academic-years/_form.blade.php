<div class="field">
    <label for="year_label">Year label</label>
    <input id="year_label" type="text" name="year_label" value="{{ old('year_label', $academicYear->year_label ?? '') }}" placeholder="2026-2027" required>
</div>
<div class="field">
    <label style="display:flex; align-items:center; gap:8px; font-weight:400;">
        <input type="checkbox" name="is_current" value="1" style="width:auto;" {{ old('is_current', $academicYear->is_current ?? false) ? 'checked' : '' }}>
        Set as the current academic year
    </label>
    <div class="field-hint">Only one academic year can be current — setting this unmarks any other.</div>
</div>
