<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'BrewPOS')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --brown-dark:    #4a2c1a;
            --brown-primary: #6b4423;
            --brown-hover:   #5a3a1f;
            --brown-light:   #c8a882;
            --text-dark:     #2c1810;
            --text-muted:    #a09080;
            --sidebar-width: 240px;
            --font-display:  'Plus Jakarta Sans', sans-serif;
            --font-body:     'Plus Jakarta Sans', sans-serif;
        }

        html, body { height: 100%; font-family: var(--font-body); background: #fafafa; }

        .app-layout {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* ── Sidebar ── */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--brown-dark);
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            overflow-y: auto;
        }

        .sidebar-brand {
            padding: 28px 24px 20px;
            font-family: var(--font-display);
            font-size: 20px;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.02em;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-nav {
            padding: 16px 12px;
            display: flex;
            flex-direction: column;
            gap: 4px;
            flex: 1;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            border-radius: 10px;
            color: rgba(255,255,255,0.65);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: background 0.18s, color 0.18s;
        }

        .nav-item:hover, .nav-item.active {
            background: rgba(255,255,255,0.12);
            color: #fff;
        }

        .nav-item svg { width: 18px; height: 18px; flex-shrink: 0; }

        .sidebar-footer {
            padding: 16px 12px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-footer form button {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            border-radius: 10px;
            background: none;
            border: none;
            color: rgba(255,255,255,0.65);
            font-family: var(--font-body);
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.18s, color 0.18s;
            text-align: left;
            line-height: 1;
        }

        .sidebar-footer form button svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
        }

        .sidebar-footer form button:hover {
            background: rgba(255,255,255,0.12);
            color: #fff;
        }

        /* ── Main ── */
        .main-wrapper {
            flex: 1;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }
    </style>
    @stack('styles')
</head>
<body>

<div class="app-layout">

    @include('partials.sidebar')

    <div class="main-wrapper">
        @yield('content')
    </div>

</div>

@stack('scripts')
</body>
</html>
