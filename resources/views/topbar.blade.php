<header class="topbar">
    <div class="topbar-title display">@yield('title', 'Dashboard')</div>
    <div class="topbar-right">
        @auth
            @php($unreadCount = auth()->user()->unreadNotifications()->count())
            <div style="position:relative;">
                <a href="{{ route('notifications.index') }}" style="text-decoration:none; color:var(--ink-soft); display:flex; align-items:center; gap:4px;">
                    Notifications
                    @if ($unreadCount > 0)
                        <span style="background:var(--gold); color:#fff; border-radius:20px; font-size:10.5px; padding:1px 6px; font-weight:700;">{{ $unreadCount }}</span>
                    @endif
                </a>
            </div>
        @endauth
        <span>{{ now()->format('l, M j') }}</span>
    </div>
</header>
