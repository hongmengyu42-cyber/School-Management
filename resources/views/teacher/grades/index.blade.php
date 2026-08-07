@extends('layouts.app')
@section('title', 'Grades — ' . $subject->subject_name)
@section('content')
    @if ($subject->isLocked())
        <div class="flash flash-error">This term is locked. Existing grades are shown but cannot be edited or added.</div>
    @endif

    <div class="card" style="margin-bottom:20px;">
        <div class="card-header"><h2 style="margin:0; font-size:15px;">Grade categories</h2></div>
        <div class="card-body">
            @if ($categories->isEmpty())
                <p style="color:var(--muted); margin-top:0;">No weighted categories yet — grades below will use a simple average.</p>
            @else
                <table class="ledger" style="margin-bottom:16px;">
                    <thead><tr><th>Name</th><th>Weight</th><th></th></tr></thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <tr>
                                <td>{{ $category->name }}</td>
                                <td>{{ $category->weight_percent }}%</td>
                                <td style="text-align:right;">
                                    @unless ($subject->isLocked())
                                        <form method="POST" action="{{ route('teacher.subjects.grade-categories.destroy', [$subject, $category]) }}" style="display:inline;" onsubmit="return confirm('Delete this category?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    @endunless
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            @unless ($subject->isLocked())
                <form method="POST" action="{{ route('teacher.subjects.grade-categories.store', $subject) }}" style="display:flex; gap:8px; align-items:flex-end;">
                    @csrf
                    <div class="field" style="margin:0; flex:1;">
                        <label for="cat_name">Category name</label>
                        <input id="cat_name" type="text" name="name" placeholder="Exams" required>
                    </div>
                    <div class="field" style="margin:0; width:120px;">
                        <label for="cat_weight">Weight %</label>
                        <input id="cat_weight" type="number" step="0.1" min="0" max="100" name="weight_percent" required>
                    </div>
                    <button type="submit" class="btn btn-ghost">Add category</button>
                </form>
            @endunless
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h2 style="margin:0; font-size:15px;">Record a grade</h2></div>
        @unless ($subject->isLocked())
            <div class="card-body" style="border-bottom:1px solid var(--line);">
                <form method="POST" action="{{ route('teacher.subjects.grades.store', $subject) }}" style="display:flex; gap:8px; align-items:flex-end; flex-wrap:wrap;">
                    @csrf
                    <div class="field" style="margin:0;">
                        <label for="student_id">Student</label>
                        <select id="student_id" name="student_id" required>
                            @foreach ($students as $student)
                                <option value="{{ $student->id }}">{{ $student->student_number }} — {{ $student->user->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field" style="margin:0;">
                        <label for="category_id">Category</label>
                        <select id="category_id" name="category_id">
                            <option value="">None (simple average)</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field" style="margin:0;">
                        <label for="label">Label</label>
                        <input id="label" type="text" name="label" placeholder="Midterm">
                    </div>
                    <div class="field" style="margin:0; width:100px;">
                        <label for="grade_value">Score</label>
                        <input id="grade_value" type="number" step="0.01" min="0" max="100" name="grade_value" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Save</button>
                </form>
            </div>
        @endunless

        @if ($students->isEmpty())
            <div class="empty-state"><h3>No students enrolled yet</h3></div>
        @else
            <table class="ledger">
                <thead><tr><th>Student</th><th>Category</th><th>Label</th><th>Score</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    @forelse ($grades as $studentId => $studentGrades)
                        @foreach ($studentGrades as $grade)
                            <tr>
                                <td>{{ $grade->student->user->full_name }}</td>
                                <td>{{ $grade->category?->name ?? '—' }}</td>
                                <td>{{ $grade->label ?? '—' }}</td>
                                <td>{{ $grade->grade_value }}</td>
                                <td>
                                    <span class="badge {{ $grade->status === 'Passed' ? 'badge-active' : 'badge-suspended' }}">{{ $grade->status }}</span>
                                </td>
                                <td style="text-align:right;">
                                    @unless ($subject->isLocked())
                                        <form method="POST" action="{{ route('teacher.subjects.grades.destroy', [$subject, $grade]) }}" style="display:inline;" onsubmit="return confirm('Delete this grade?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    @endunless
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr><td colspan="6" style="text-align:center; color:var(--muted);">No grades recorded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        @endif
    </div>
@endsection
