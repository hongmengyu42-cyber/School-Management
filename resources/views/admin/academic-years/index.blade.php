@extends('layouts.app')
@section('title', 'Academic Years')
@section('content')
    <div class="card">
        <div class="card-header">
            <h2 style="margin:0; font-size:15px;">All academic years</h2>
            <a href="{{ route('admin.academic-years.create') }}" class="btn btn-primary btn-sm">Add academic year</a>
        </div>
        @if ($academicYears->isEmpty())
            <div class="empty-state">
                <h3>No academic years yet</h3>
                <p>Add one to start creating semesters underneath it.</p>
            </div>
        @else
            <table class="ledger">
                <thead><tr><th>Year</th><th>Semesters</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    @foreach ($academicYears as $year)
                        <tr>
                            <td>{{ $year->year_label }}</td>
                            <td>{{ $year->semesters_count }}</td>
                            <td>@if ($year->is_current)<span class="badge badge-active">Current</span>@endif</td>
                            <td style="text-align:right;">
                                <a href="{{ route('admin.academic-years.edit', $year) }}" class="btn btn-ghost btn-sm">Edit</a>
                                <form method="POST" action="{{ route('admin.academic-years.destroy', $year) }}" style="display:inline;" onsubmit="return confirm('Delete this academic year?');">
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
    <div style="margin-top:16px;">{{ $academicYears->links() }}</div>
@endsection
