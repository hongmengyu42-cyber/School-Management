@extends('layouts.app')
@section('title', 'Activity Log')
@section('content')
    <div class="card">
        <div class="card-header"><h2 style="margin:0; font-size:15px;">Recent activity</h2></div>
        @if ($logs->isEmpty())
            <div class="empty-state"><h3>No activity recorded yet</h3></div>
        @else
            <table class="ledger">
                <thead><tr><th>When</th><th>User</th><th>Action</th><th>Details</th><th>IP</th></tr></thead>
                <tbody>
                    @foreach ($logs as $log)
                        <tr>
                            <td>{{ $log->created_at->format('M j, g:ia') }}</td>
                            <td>{{ $log->user?->full_name ?? 'System' }}</td>
                            <td>{{ $log->action }}</td>
                            <td>{{ $log->description }}</td>
                            <td style="color:var(--muted);">{{ $log->ip_address }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
    <div style="margin-top:16px;">{{ $logs->links() }}</div>
@endsection
