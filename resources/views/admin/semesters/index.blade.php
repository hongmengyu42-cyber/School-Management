@extends('layouts.app')
@section('title', 'Semesters')
@section('content')
    <div class="card">
        <div class="card-header">
            <h2 style="margin:0; font-size:15px;">All semesters</h2>
            <a href="{{ route('admin.semesters.create') }}" class="btn btn-primary btn-sm">Add semester</a>
        </div>
        @if ($semesters->isEmpty())
            <div class="empty-state">
                <h3>No semesters yet</h3>
                <p>Semesters belong to an academic year and hold subjects and their term-lock status.</p>
            </div>
        @else
            <table class="ledger">
                <thead><tr><th>Name</th><th>Academic Year</th><th>Status</th><th>Term Lock</th><th></th></tr></thead>
                <tbody>
                    @foreach ($semesters as $semester)
                        <tr>
                            <td>{{ $semester->name }}</td>
                            <td>{{ $semester->academicYear->year_label }}</td>
                            <td>@if ($semester->is_current)<span class="badge badge-active">Current</span>@endif</td>
                            <td>
                                @if ($semester->is_locked)
                                    <span class="badge badge-locked">Locked</span>
                                @else
                                    <span class="badge badge-active">Open</span>
                                @endif
                            </td>
                            <td style="text-align:right;">
                                <form method="POST" action="{{ route('admin.semesters.toggle-lock', $semester) }}" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-ghost btn-sm">{{ $semester->is_locked ? 'Unlock' : 'Lock' }}</button>
                                </form>
                                <a href="{{ route('admin.semesters.edit', $semester) }}" class="btn btn-ghost btn-sm">Edit</a>
                                <form method="POST" action="{{ route('admin.semesters.destroy', $semester) }}" style="display:inline;" onsubmit="return confirm('Delete this semester?');">
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
    <div style="margin-top:16px;">{{ $semesters->links() }}</div>
@endsection
