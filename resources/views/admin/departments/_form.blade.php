<div class="field">
    <label for="department_code">Department code</label>
    <input id="department_code" type="text" name="department_code" value="{{ old('department_code', $department->department_code ?? '') }}" required>
    <div class="field-hint">Short unique identifier, e.g. CS, MATH, ENG.</div>
</div>
<div class="field">
    <label for="department_name">Department name</label>
    <input id="department_name" type="text" name="department_name" value="{{ old('department_name', $department->department_name ?? '') }}" required>
</div>
