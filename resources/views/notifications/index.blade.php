@extends('layouts.app')
@section('title', 'Notifications')
@section('content')
    <div class="card">
        <div class="card-header">
            <h2 style="margin:0; font-size:15px;">Notifications</h2>
            <form method="POST" action="{{ route('notifications.read-all') }}">
                @csrf
                <button type="submit" class="btn btn-ghost btn-sm">Mark all read</button>
            </form>
        </div>
        @if ($notifications->isEmpty())
            <div class="empty-state"><h3>No notifications yet</h3></div>
        @else
            <table class="ledger">
                <thead><tr><th></th><th>Notification</th><th>When</th></tr></thead>
                <tbody>
                    @foreach ($notifications as $notification)
                        <tr style="{{ $notification->read_at ? '' : 'font-weight:600;' }}">
                            <td>@unless($notification->read_at)<span style="display:inline-block; width:7px; height:7px; border-radius:50%; background:var(--gold);"></span>@endunless</td>
                            <td>
                                <a href="{{ route('notifications.read', $notification->id) }}" style="text-decoration:none; color:inherit;">
                                    <div>
                                        {{ $notification->data['title'] ?? 'Notification' }}
                                        @if(!empty($notification->data['is_alert']))
                                            <span style="margin-left:6px; padding:1px 6px; border-radius:10px; font-size:11px; font-weight:600; background:#fde2e2; color:#b42318;">Alert</span>
                                        @endif
                                    </div>
                                    <div style="font-weight:400; color:var(--muted); font-size:12.5px;">{{ $notification->data['body'] ?? '' }}</div>
                                </a>
                            </td>
                            <td style="color:var(--muted); font-weight:400;">{{ $notification->created_at->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
    <div style="margin-top:16px;">{{ $notifications->links() }}</div>
@endsection
