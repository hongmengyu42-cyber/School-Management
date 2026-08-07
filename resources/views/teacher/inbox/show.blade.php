@extends('layouts.app')
@section('title', 'Thread — ' . $thread->student->user->full_name)
@section('content')
    <div class="card" style="max-width:640px;">
        <div class="card-header">
            <h2 style="margin:0; font-size:15px;">{{ $thread->student->user->full_name }} — {{ $thread->subject->subject_name }}</h2>
        </div>
        <div class="card-body" style="display:flex; flex-direction:column; gap:12px;">
            @foreach ($thread->messages as $message)
                <div style="padding:10px 14px; border-radius:8px; background: {{ $message->sender_id === auth()->id() ? 'var(--primary-tint)' : 'var(--bg)' }};">
                    <div style="font-size:11.5px; color:var(--muted); margin-bottom:3px;">{{ $message->sender->full_name }} · {{ $message->created_at->format('M j, g:ia') }}</div>
                    <div>{{ $message->message }}</div>
                </div>
            @endforeach
        </div>
        @if ($thread->status === 'Open')
            <div class="card-body" style="border-top:1px solid var(--line);">
                <form method="POST" action="{{ route('teacher.inbox.reply', $thread) }}">
                    @csrf
                    <div class="field">
                        <textarea name="message" rows="3" placeholder="Write a reply&hellip;" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Send</button>
                </form>
            </div>
        @else
            <div class="card-body" style="border-top:1px solid var(--line); color:var(--muted);">This thread is closed.</div>
        @endif
    </div>
@endsection
