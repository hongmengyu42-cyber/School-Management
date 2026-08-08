@extends('layouts.app')
@section('title', 'Users')
@section('content')
    <div class="card" style="margin-bottom:20px;">
        <div class="card-header"><h2 style="margin:0; font-size:15px;">Bulk import</h2></div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.users.bulk-import') }}" enctype="multipart/form-data" style="display:flex; gap:10px; align-items:flex-end;">
                @csrf
                <div class="field" style="margin:0; flex:1;">
                    <label for="csv_file">CSV file</label>
                    <input id="csv_file" type="file" name="csv_file" accept=".csv,.txt" required>
                    <div class="field-hint">Header row: full_name,username,email,password,role,department_code</div>
                </div>
                <button type="submit" class="btn btn-ghost">Import</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2 style="margin:0; font-size:15px;">All users</h2>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">Add user</a>
        </div>

        <div class="card-body" style="border-bottom:1px solid var(--border);">
            <form method="POST" action="{{ route('admin.parent-links.store') }}" style="display:flex; gap:10px; align-items:flex-end; flex-wrap:wrap;">
                @csrf
                <div class="field" style="margin:0; min-width:220px;">
                    <label for="parent_user_id">Parent user</label>
                    <select id="parent_user_id" name="parent_user_id" required>
                        <option value="">Select parent</option>
                        @foreach (\App\Models\User::where('role', 'Parent')->orderBy('full_name')->get() as $parent)
                            <option value="{{ $parent->id }}">{{ $parent->full_name }} ({{ $parent->username }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="field" style="margin:0; min-width:220px;">
                    <label for="student_id">Student</label>
                    <select id="student_id" name="student_id" required>
                        <option value="">Select student</option>
                        @foreach (\App\Models\Student::with('user')->orderBy('id')->get() as $student)
                            <option value="{{ $student->id }}">{{ $student->user?->full_name ?? 'Student #' . $student->id }} ({{ $student->student_number }})</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-ghost">Link parent</button>
            </form>
        </div>

        <table class="ledger">
            <thead><tr><th>Name</th><th>Username</th><th>Role</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->full_name }}</td>
                        <td>{{ $user->username }}</td>
                        <td>{{ $user->role }}</td>
                        <td>
                            <span class="badge badge-{{ strtolower($user->status) }}">{{ $user->status }}</span>
                        </td>
                        <td style="text-align:right;">
                            @if ($user->status === 'Pending')
                                <form method="POST" action="{{ route('admin.users.approve', $user) }}" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-ghost btn-sm">Approve</button>
                                </form>
                            @endif
                            @if ($user->role === 'Student' && $user->student)
                                <a href="{{ route('admin.students.report-card.index', $user->student) }}" class="btn btn-ghost btn-sm">Report cards</a>
                            @endif
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-ghost btn-sm">Edit</a>
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" style="display:inline;" onsubmit="return confirm('Delete this user?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div style="margin-top:16px;">{{ $users->links() }}</div>
@endsection
