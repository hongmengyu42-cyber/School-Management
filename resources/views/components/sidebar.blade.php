@php($user = auth()->user())
<aside class="sidebar">
    <div class="brand">
        <div class="seal">{{ \Illuminate\Support\Str::of(\App\Models\Setting::schoolName())->explode(' ')->map(fn($w) => $w[0] ?? '')->take(2)->implode('') }}</div>
        <div>
            <div class="brand-name">{{ \App\Models\Setting::schoolName() }}</div>
            <div class="brand-sub">{{ $user?->role }} Portal</div>
        </div>
    </div>

    @if ($user?->isAdmin())
        <div class="nav-group">
            <div class="nav-label">Overview</div>
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a>
        </div>
        <div class="nav-group">
            <div class="nav-label">Academic Structure</div>
            <a href="{{ route('admin.departments.index') }}" class="nav-link {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}">Departments</a>
            <a href="{{ route('admin.academic-years.index') }}" class="nav-link {{ request()->routeIs('admin.academic-years.*') ? 'active' : '' }}">Academic Years</a>
            <a href="{{ route('admin.semesters.index') }}" class="nav-link {{ request()->routeIs('admin.semesters.*') ? 'active' : '' }}">Semesters</a>
            <a href="{{ route('admin.subjects.index') }}" class="nav-link {{ request()->routeIs('admin.subjects.*') ? 'active' : '' }}">Subjects</a>
        </div>
        <div class="nav-group">
            <div class="nav-label">People</div>
            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">Users</a>
        </div>
        <div class="nav-group">
            <div class="nav-label">System</div>
            <a href="{{ route('admin.activity-logs.index') }}" class="nav-link {{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}">Activity Log</a>
            <a href="{{ route('admin.system-settings.edit') }}" class="nav-link {{ request()->routeIs('admin.system-settings.*') ? 'active' : '' }}">Settings</a>
        </div>
    @elseif ($user?->isTeacher())
        <div class="nav-group">
            <div class="nav-label">Overview</div>
            <a href="{{ route('teacher.dashboard') }}" class="nav-link {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}">Dashboard</a>
            <a href="{{ route('teacher.timetable') }}" class="nav-link {{ request()->routeIs('teacher.timetable') ? 'active' : '' }}">Timetable</a>
            <a href="{{ route('teacher.inbox.index') }}" class="nav-link {{ request()->routeIs('teacher.inbox.*') ? 'active' : '' }}">Inbox</a>
        </div>
        <div class="nav-group">
            <div class="nav-label">Teaching Workspace</div>
            <a href="{{ route('teacher.subjects.index') }}" class="nav-link {{ request()->routeIs('teacher.subjects.*') ? 'active' : '' }}">Subjects</a>
        </div>
    @elseif ($user?->isStudent())
        <div class="nav-group">
            <div class="nav-label">Overview</div>
            <a href="{{ route('student.dashboard') }}" class="nav-link active">Dashboard</a>
        </div>
    @elseif ($user?->isParent())
        <div class="nav-group">
            <div class="nav-label">Overview</div>
            <a href="{{ route('parent.dashboard') }}" class="nav-link active">Dashboard</a>
        </div>
    @endif

    <div class="sidebar-footer">
        <div class="nav-link" style="color:#C7D4C7; font-size:12.5px;">{{ $user?->full_name }}</div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-link" style="width:100%; text-align:left; background:none; border:none; border-left:2px solid transparent; cursor:pointer; font-family:inherit;">Sign out</button>
        </form>
    </div>
</aside>
