<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sign in') · {{ \App\Models\Setting::schoolName() }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #F4F5F1; --surface: #FFFFFF; --ink: #1C2620; --ink-soft: #4B5A50;
            --muted: #7C8A80; --line: #DDE2D8; --primary: #2F5233; --primary-dark: #223D26;
            --primary-tint: #E8EEE7; --gold: #B08A34; --danger: #A23B32; --danger-tint: #F7E8E5;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center;
            background: var(--primary-dark);
            background-image: radial-gradient(circle at 20% 20%, rgba(255,255,255,0.04), transparent 40%);
            font-family: 'Inter', sans-serif; color: var(--ink);
        }
        .auth-card {
            width: 380px; background: var(--surface); border-radius: 10px; padding: 34px 32px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.25);
        }
        .seal {
            width: 44px; height: 44px; border-radius: 50%; border: 1.5px solid var(--gold);
            display: flex; align-items: center; justify-content: center;
            font-family: 'Fraunces', serif; font-weight: 600; color: var(--gold); margin-bottom: 14px;
        }
        h1 { font-family: 'Fraunces', serif; font-size: 20px; margin: 0 0 4px; font-weight: 600; }
        .subtitle { font-size: 13px; color: var(--muted); margin-bottom: 22px; }
        .field { margin-bottom: 15px; }
        .field label { display: block; font-size: 12.5px; font-weight: 600; color: var(--ink-soft); margin-bottom: 5px; }
        .field input {
            width: 100%; padding: 10px 12px; border: 1px solid var(--line); border-radius: 6px;
            font-size: 13.5px; font-family: inherit;
        }
        .field input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px var(--primary-tint); }
        .btn-primary {
            width: 100%; padding: 10px; background: var(--primary); color: #fff; border: none;
            border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer; margin-top: 4px;
        }
        .btn-primary:hover { background: var(--primary-dark); }
        .links { margin-top: 16px; font-size: 12.5px; color: var(--muted); text-align: center; }
        .links a { color: var(--primary); text-decoration: none; font-weight: 500; }
        .flash-error { background: var(--danger-tint); color: var(--danger); padding: 10px 12px; border-radius: 6px; font-size: 12.5px; margin-bottom: 16px; }
        .checkbox-row { display: flex; align-items: center; gap: 7px; font-size: 12.5px; color: var(--ink-soft); margin-bottom: 16px; }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="seal">{{ \Illuminate\Support\Str::of(\App\Models\Setting::schoolName())->explode(' ')->map(fn($w) => $w[0] ?? '')->take(2)->implode('') }}</div>
        @yield('content')
    </div>
</body>
</html>
