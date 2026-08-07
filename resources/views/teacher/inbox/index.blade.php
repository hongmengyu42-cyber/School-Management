@extends('layouts.app')
@section('title', 'Inbox')
@section('content')
    <div class="card">
        <div class="card-header"><h2 style="margin:0; font-size:15px;">Message threads</h2></div>
        @if ($threads->isEmpty())
            <div class="empty-state"><h3>No messages yet</h3></div>
        @else
            <table class="ledger">
                <thead><tr><th>Student</th><th>Subject</th><th>Last message</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    @foreach ($threads as $thread)
                        <tr>
                            <td>{{ $thread->student->user->full_name }}</td>
                            <td>{{ $thread->subject->subject_name }}</td>
                            <td style="color:var(--muted);">{{ \Illuminate\Support\Str::limit($thread->messages->first()?->message, 60) }}</td>
                            <td><span class="badge {{ $thread->status === 'Open' ? 'badge-open' : 'badge-suspended' }}">{{ $thread->status }}</span></td>
                            <td style="text-align:right;">
                                <a href="{{ route('teacher.inbox.show', $thread) }}" class="btn btn-ghost btn-sm">Open</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
