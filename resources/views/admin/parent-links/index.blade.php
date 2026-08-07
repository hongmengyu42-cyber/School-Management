@extends('layouts.app')
@section('title', 'Parent Links')
@section('content')
    <div class="card" style="margin-bottom:20px;">
        <div class="card-header"><h2 style="margin:0; font-size:15px;">Link a parent to a student</h2></div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.parent-links.store') }}" style="display:flex; gap:8px; align-items:flex-end; flex-wrap:wrap;">
                @csrf
                <div class="field" style="margin:0;">
                    <label for="parent_user_id">Parent</label>
                    <select id="parent_user_id" name="parent_user_id" required>
                        <option value="">Select a parent&hellip;</option>
                        @foreach ($parents as $parent)
                            <option value="{{ $parent->id }}">{{ $parent->full_name }} ({{ $parent->username }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="field" style="margin:0;">
                    <label for="student_id">Student</label>
                    <select id="student_id" name="student_id" required>
                        <option value="">Select a student&hellip;</option>
                        @foreach ($students as $student)
                            <option value="{{ $student->id }}">{{ $student->user->full_name }} ({{ $student->student_number }})</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Link</button>
            </form>
            @if ($parents->isEmpty())
                <p class="field-hint" style="margin-top:12px;">No Parent-role users exist yet — create one under Admin &rsaquo; Users first.</p>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h2 style="margin:0; font-size:15px;">Existing links</h2></div>
        @if ($links->isEmpty())
            <div class="empty-state"><h3>No links yet</h3></div>
        @else
            <table class="ledger">
                <thead><tr><th>Parent</th><th>Student</th><th></th></tr></thead>
                <tbody>
                    @foreach ($links as $link)
                        <tr>
                            <td>{{ $link->parentUser->full_name }}</td>
                            <td>{{ $link->student->user->full_name }} ({{ $link->student->student_number }})</td>
                            <td style="text-align:right;">
                                <form method="POST" action="{{ route('admin.parent-links.destroy', $link) }}" style="display:inline;" onsubmit="return confirm('Remove this link?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
    <div style="margin-top:16px;">{{ $links->links() }}</div>
@endsection
