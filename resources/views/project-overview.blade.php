<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    @include('partials.auth-banner')

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} | Project Overview</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=IBM+Plex+Sans:wght@400;500;700&family=IBM+Plex+Mono:wght@400;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg: #0e1923;
            --card: #132939;
            --card-2: #173044;
            --ink: #f1f8ff;
            --muted: #9db6cb;
            --accent: #ffd166;
            --line: #2c4a61;
            --ok: #63d490;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            color: var(--ink);
            font-family: "IBM Plex Sans", sans-serif;
            background:
                radial-gradient(circle at 85% 18%, #25445f 0%, transparent 40%),
                radial-gradient(circle at 14% 82%, #1c3f33 0%, transparent 42%),
                linear-gradient(140deg, #0c141d 0%, #111f2d 100%);
        }

        .wrap {
            max-width: 1100px;
            margin: 0 auto;
            padding: 30px 18px 42px;
        }

        .hero {
            border: 1px solid var(--line);
            border-radius: 20px;
            background: linear-gradient(155deg, #173449eb 0%, #122433eb 100%);
            padding: 24px;
            box-shadow: 0 18px 40px #00000044;
        }

        .kicker {
            margin: 0;
            text-transform: uppercase;
            letter-spacing: .12em;
            color: var(--accent);
            font-size: 12px;
            font-weight: 700;
        }

        h1 {
            margin: 8px 0 8px;
            font-family: "Bebas Neue", sans-serif;
            letter-spacing: .04em;
            font-size: clamp(38px, 8vw, 72px);
            line-height: .95;
            font-weight: 400;
        }

        .subtitle {
            margin: 0;
            max-width: 760px;
            color: var(--muted);
            font-size: 16px;
        }

        .actions {
            margin-top: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .btn {
            display: inline-block;
            text-decoration: none;
            border-radius: 12px;
            padding: 10px 14px;
            border: 1px solid transparent;
            font-size: 14px;
            font-weight: 700;
            transition: transform .2s ease;
        }

        .btn:hover { transform: translateY(-2px); }

        .btn-primary {
            color: #1f1703;
            background: var(--accent);
        }

        .btn-secondary {
            color: var(--ink);
            border-color: #4e7088;
            background: transparent;
        }

        .grid {
            margin-top: 14px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .panel {
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 16px;
            background: var(--card);
        }

        .panel h2 {
            margin: 0 0 12px;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .list {
            margin: 0;
            padding: 0;
            list-style: none;
            display: grid;
            gap: 8px;
        }

        .list li {
            border: 1px solid #2d4f66;
            background: var(--card-2);
            border-radius: 10px;
            padding: 10px 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            font-family: "IBM Plex Mono", monospace;
            font-size: 13px;
        }

        .tag {
            border-radius: 999px;
            padding: 2px 8px;
            font-size: 11px;
            font-weight: 700;
            color: #0a1d11;
            background: var(--ok);
            white-space: nowrap;
        }

        .stats {
            margin-top: 14px;
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 16px;
            background: var(--card);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 10px;
        }

        .stat {
            border: 1px solid #2d4f66;
            background: var(--card-2);
            border-radius: 10px;
            padding: 10px;
        }

        .stat-label {
            margin: 0;
            text-transform: uppercase;
            color: var(--muted);
            font-size: 11px;
            letter-spacing: .08em;
        }

        .stat-value {
            margin: 6px 0 0;
            font-size: 28px;
            font-family: "Bebas Neue", sans-serif;
            letter-spacing: .05em;
        }

        @media (max-width: 900px) {
            .grid { grid-template-columns: 1fr; }
            .stats-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        }

        @media (max-width: 640px) {
            .stats-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
    </style>
</head>
<body>
    @php
        $statLabels = [
            'users' => 'Users',
            'classes' => 'Classes',
            'courses' => 'Courses',
            'chapters' => 'Chapters',
            'contents' => 'Contents',
        ];
    @endphp

    <div class="wrap">
        <section class="hero">
            <p class="kicker">Application Checkpoint</p>
            <h1>Models + Controllers<br>Ready</h1>
            <p class="subtitle">
                Cette page resume l'etat du code applicatif. Tu peux montrer ici que la partie modeles,
                controleurs et validation API est bien en place.
            </p>

            <div class="actions">
                <a class="btn btn-primary" href="{{ route('home') }}">Back To Welcome</a>
                <a class="btn btn-secondary" href="{{ url('/docs') }}">Open Swagger UI</a>
            </div>
        </section>

        <section class="grid">
            <article class="panel">
                <h2>Models Implemented</h2>
                <ul class="list">
                    @foreach ($modelChecklist as $model)
                        <li>
                            <span>{{ $model }}</span>
                            <span class="tag">OK</span>
                        </li>
                    @endforeach
                </ul>
            </article>

            <article class="panel">
                <h2>Controllers / State Flow</h2>
                <ul class="list">
                    @foreach ($controllerChecklist as $controller)
                        <li>
                            <span>{{ $controller }}</span>
                            <span class="tag">OK</span>
                        </li>
                    @endforeach
                </ul>
            </article>
        </section>

        <section class="stats">
            <h2>Seeded Data Snapshot</h2>
            <div class="stats-grid">
                @foreach ($statLabels as $key => $label)
                    <article class="stat">
                        <p class="stat-label">{{ $label }}</p>
                        <p class="stat-value">{{ $stats[$key] ?? 0 }}</p>
                    </article>
                @endforeach
            </div>
        </section>
    </div>
</body>
</html>
