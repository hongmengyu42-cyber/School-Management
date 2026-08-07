@extends('layouts.app')
@section('title', 'Subjects')
@section('content')
    <div class="card">
        <div class="card-header">
            <h2 style="margin:0; font-size:15px;">All subjects</h2>
            <a href="{{ route('admin.subjects.create') }}" class="btn btn-primary btn-sm">Add subject</a>
        </div>
        @if ($subjects->isEmpty())
            <div class="empty-state">
                <h3>No subjects yet</h3>
                <p>Subjects are what students enroll in and teachers teach.</p>
            </div>
        @else
            <table class="ledger">
                <thead><tr><th>Code</th><th>Name</th><th>Teacher</th><th>Semester</th><th>Enrolled</th><th></th></tr></thead>
                <tbody>
                    @foreach ($subjects as $subject)
                        <tr>
                            <td>{{ $subject->subject_code }}</td>
                            <td>{{ $subject->subject_name }}</td>
                            <td>{{ $subject->teacher?->full_name ?? '—' }}</td>
                            <td>
                                {{ $subject->semester?->name ?? '—' }}
                                @if ($subject->isLocked())<span class="badge badge-locked" style="margin-left:6px;">Locked</span>@endif
                            </td>
                            <td>{{ $subject->students_count }}</td>
                            <td style="text-align:right;">
                                <a href="{{ route('admin.subjects.edit', $subject) }}" class="btn btn-ghost btn-sm">Edit</a>
                                <form method="POST" action="{{ route('admin.subjects.destroy', $subject) }}" style="display:inline;" onsubmit="return confirm('Delete this subject?');">
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
    <div style="margin-top:16px;">{{ $subjects->links() }}</div>
@endsection
