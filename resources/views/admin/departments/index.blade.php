@extends('layouts.app')
@section('title', 'Departments')
@section('content')
    <div class="card">
        <div class="card-header">
            <h2 style="margin:0; font-size:15px;">All departments</h2>
            <a href="{{ route('admin.departments.create') }}" class="btn btn-primary btn-sm">Add department</a>
        </div>
        @if ($departments->isEmpty())
            <div class="empty-state">
                <h3>No departments yet</h3>
                <p>Departments organize subjects and students. Add the first one to get started.</p>
            </div>
        @else
            <table class="ledger">
                <thead>
                    <tr><th>Code</th><th>Name</th><th>Students</th><th>Subjects</th><th></th></tr>
                </thead>
                <tbody>
                    @foreach ($departments as $department)
                        <tr>
                            <td>{{ $department->department_code }}</td>
                            <td>{{ $department->department_name }}</td>
                            <td>{{ $department->students_count }}</td>
                            <td>{{ $department->subjects_count }}</td>
                            <td style="text-align:right;">
                                <a href="{{ route('admin.departments.edit', $department) }}" class="btn btn-ghost btn-sm">Edit</a>
                                <form method="POST" action="{{ route('admin.departments.destroy', $department) }}" style="display:inline;" onsubmit="return confirm('Delete this department?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
    <div style="margin-top:16px;">{{ $departments->links() }}</div>
@endsection
