<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') · {{ \App\Models\Setting::schoolName() }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #F4F5F1;
            --surface: #FFFFFF;
            --ink: #1C2620;
            --ink-soft: #4B5A50;
            --muted: #7C8A80;
            --line: #DDE2D8;
            --primary: #2F5233;
            --primary-dark: #223D26;
            --primary-tint: #E8EEE7;
            --gold: #B08A34;
            --gold-tint: #F6EFDD;
            --danger: #A23B32;
            --danger-tint: #F7E8E5;
            --radius: 8px;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            background: var(--bg);
            color: var(--ink);
            font-family: 'Inter', -apple-system, sans-serif;
            font-size: 14.5px;
            line-height: 1.5;
        }
        h1, h2, h3, .display { font-family: 'Fraunces', Georgia, serif; font-weight: 600; letter-spacing: -0.01em; }

        a { color: inherit; }

        .app-shell { display: flex; min-height: 100vh; }

        /* ---- Sidebar ---- */
        .sidebar {
            width: 236px;
            flex-shrink: 0;
            background: var(--primary-dark);
            color: #E9EFE9;
            display: flex;
            flex-direction: column;
            padding: 20px 14px;
        }
        .seal {
            width: 38px; height: 38px;
            border-radius: 50%;
            border: 1.5px solid var(--gold);
            display: flex; align-items: center; justify-content: center;
            font-family: 'Fraunces', serif;
            font-weight: 600;
            font-size: 15px;
            color: var(--gold);
            flex-shrink: 0;
        }
        .brand { display: flex; align-items: center; gap: 10px; padding: 4px 8px 20px; }
        .brand-name { font-family: 'Fraunces', serif; font-size: 15px; line-height: 1.2; color: #fff; }
        .brand-sub { font-size: 10.5px; color: #A9BCA9; text-transform: uppercase; letter-spacing: 0.08em; }

        .nav-group { margin-bottom: 18px; }
        .nav-label {
            font-size: 10px; text-transform: uppercase; letter-spacing: 0.09em;
            color: #7E9480; padding: 0 10px; margin-bottom: 6px;
        }
        .nav-link {
            display: block; padding: 8px 10px; border-radius: 6px;
            font-size: 13.5px; color: #D3DED2; text-decoration: none;
            border-left: 2px solid transparent;
            margin-bottom: 2px;
        }
        .nav-link:hover { background: rgba(255,255,255,0.06); }
        .nav-link.active {
            background: rgba(255,255,255,0.08);
            border-left-color: var(--gold);
            color: #fff;
            font-weight: 500;
        }

        .sidebar-footer { margin-top: auto; padding-top: 12px; border-top: 1px solid rgba(255,255,255,0.1); }

        /* ---- Main column ---- */
        .main { flex: 1; display: flex; flex-direction: column; min-width: 0; }
        .topbar {
            height: 60px; background: var(--surface); border-bottom: 1px solid var(--line);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 24px;
        }
        .topbar-title { font-size: 17px; }
        .topbar-right { display: flex; align-items: center; gap: 14px; font-size: 13px; color: var(--ink-soft); }

        .content { padding: 28px 32px; max-width: 1180px; }

        /* ---- Shared components ---- */
        .card {
            background: var(--surface); border: 1px solid var(--line); border-radius: var(--radius);
        }
        .card-header {
            padding: 16px 20px; border-bottom: 1px solid var(--line);
            display: flex; align-items: center; justify-content: space-between;
        }
        .card-body { padding: 20px; }

        table.ledger { width: 100%; border-collapse: collapse; }
        table.ledger th {
            text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: 0.06em;
            color: var(--muted); padding: 10px 20px; border-bottom: 1px solid var(--line);
            font-weight: 600;
        }
        table.ledger td {
            padding: 12px 20px; border-bottom: 1px solid var(--line); font-size: 13.5px;
        }
        table.ledger tr:last-child td { border-bottom: none; }
        table.ledger tr:hover td { background: var(--bg); }

        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 16px; border-radius: 6px; font-size: 13.5px; font-weight: 500;
            border: 1px solid transparent; cursor: pointer; text-decoration: none;
        }
        .btn-primary { background: var(--primary); color: #fff; }
        .btn-primary:hover { background: var(--primary-dark); }
        .btn-ghost { background: transparent; color: var(--ink-soft); border-color: var(--line); }
        .btn-ghost:hover { background: var(--bg); }
        .btn-danger { background: var(--danger-tint); color: var(--danger); }
        .btn-sm { padding: 5px 10px; font-size: 12.5px; }

        .badge {
            display: inline-block; padding: 2px 9px; border-radius: 20px;
            font-size: 11px; font-weight: 600; letter-spacing: 0.02em;
        }
        .badge-active { background: var(--primary-tint); color: var(--primary-dark); }
        .badge-pending { background: var(--gold-tint); color: var(--gold); }
        .badge-suspended { background: var(--danger-tint); color: var(--danger); }
        .badge-locked { background: var(--danger-tint); color: var(--danger); }
        .badge-open { background: var(--primary-tint); color: var(--primary-dark); }

        .field { margin-bottom: 16px; }
        .field label { display: block; font-size: 12.5px; font-weight: 600; color: var(--ink-soft); margin-bottom: 5px; }
        .field input, .field select, .field textarea {
            width: 100%; padding: 9px 12px; border: 1px solid var(--line); border-radius: 6px;
            font-size: 13.5px; font-family: inherit; background: var(--surface); color: var(--ink);
        }
        .field input:focus, .field select:focus, .field textarea:focus {
            outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px var(--primary-tint);
        }
        .field-hint { font-size: 11.5px; color: var(--muted); margin-top: 4px; }
        .field-error { font-size: 11.5px; color: var(--danger); margin-top: 4px; }

        .flash {
            padding: 11px 16px; border-radius: 6px; font-size: 13px; margin-bottom: 18px;
            background: var(--primary-tint); color: var(--primary-dark); border: 1px solid #C9DBC9;
        }
        .flash-error { background: var(--danger-tint); color: var(--danger); border-color: #E8C6C0; }

        .empty-state {
            text-align: center; padding: 48px 20px; color: var(--muted);
        }
        .empty-state h3 { color: var(--ink-soft); margin-bottom: 6px; font-size: 15px; }
    </style>
    @stack('styles')
</head>
<body>
    <div class="app-shell">
        <x-sidebar />
        <div class="main">
            <x-topbar />
            <div class="content">
                <x-flash-messages />
                @yield('content')
            </div>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
