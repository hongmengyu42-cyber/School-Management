<div class="field">
    <label for="full_name">Full name</label>
    <input id="full_name" type="text" name="full_name" value="{{ old('full_name', $user->full_name ?? '') }}" required>
</div>
<div class="field">
    <label for="username">Username</label>
    <input id="username" type="text" name="username" value="{{ old('username', $user->username ?? '') }}" required>
</div>
<div class="field">
    <label for="email">Email</label>
    <input id="email" type="email" name="email" value="{{ old('email', $user->email ?? '') }}" required>
</div>
<div class="field">
    <label for="password">Password{{ isset($user) ? ' (leave blank to keep current)' : '' }}</label>
    <input id="password" type="password" name="password" {{ isset($user) ? '' : 'required' }}>
</div>
<div class="field">
    <label for="role">Role</label>
    <select id="role" name="role" required>
        @foreach (['Admin', 'Teacher', 'Student', 'Parent'] as $role)
            <option value="{{ $role }}" {{ old('role', $user->role ?? '') === $role ? 'selected' : '' }}>{{ $role }}</option>
        @endforeach
    </select>
</div>
<div class="field">
    <label for="status">Status</label>
    <select id="status" name="status" required>
        @foreach (['Pending', 'Active', 'Suspended'] as $status)
            <option value="{{ $status }}" {{ old('status', $user->status ?? 'Pending') === $status ? 'selected' : '' }}>{{ $status }}</option>
        @endforeach
    </select>
</div>
<div class="field">
    <label for="department_id">Department</label>
    <select id="department_id" name="department_id">
        <option value="">None</option>
        @foreach ($departments as $department)
            <option value="{{ $department->id }}" {{ old('department_id', $user->department_id ?? '') == $department->id ? 'selected' : '' }}>{{ $department->department_name }}</option>
        @endforeach
    </select>
</div>
