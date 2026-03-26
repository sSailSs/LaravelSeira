<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} | School API</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=IBM+Plex+Sans:wght@400;500;700&family=IBM+Plex+Mono:wght@400;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg: #0f1a24;
            --panel: #132636;
            --panel-2: #173247;
            --ink: #eff7ff;
            --muted: #9fb6ca;
            --accent: #ffb347;
            --accent-2: #58d6ff;
            --line: #2a475e;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            color: var(--ink);
            font-family: "IBM Plex Sans", sans-serif;
            background:
                radial-gradient(circle at 12% 15%, #23405a 0%, #0f1a24 38%),
                radial-gradient(circle at 88% 82%, #2a513a 0%, #0f1a24 42%),
                linear-gradient(140deg, #0c141d 0%, #121f2c 100%);
        }

        .aura {
            position: fixed;
            inset: 0;
            pointer-events: none;
            background:
                radial-gradient(circle at 20% 25%, #ffb3472a, transparent 35%),
                radial-gradient(circle at 80% 75%, #58d6ff26, transparent 35%);
            animation: pulse 6s ease-in-out infinite;
        }

        .wrap {
            position: relative;
            max-width: 1100px;
            margin: 0 auto;
            padding: 32px 18px 40px;
        }

        .hero {
            border: 1px solid var(--line);
            border-radius: 20px;
            padding: 28px;
            background: linear-gradient(160deg, #173247ea 0%, #142434ea 100%);
            box-shadow: 0 20px 45px #00000040;
            animation: slide-in .7s ease-out both;
        }

        .kicker {
            margin: 0;
            letter-spacing: .13em;
            text-transform: uppercase;
            color: var(--accent);
            font-size: 12px;
            font-weight: 700;
        }

        h1 {
            margin: 8px 0 12px;
            font-family: "Bebas Neue", sans-serif;
            letter-spacing: .04em;
            line-height: .95;
            font-size: clamp(40px, 8vw, 78px);
            font-weight: 400;
        }

        .subtitle {
            margin: 0;
            color: var(--muted);
            max-width: 780px;
            font-size: 16px;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 22px;
        }

        .btn {
            display: inline-block;
            border-radius: 12px;
            padding: 10px 14px;
            text-decoration: none;
            font-weight: 700;
            font-size: 14px;
            transition: transform .2s ease, background .2s ease, border-color .2s ease;
            border: 1px solid transparent;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn-primary {
            background: var(--accent);
            color: #1a1409;
        }

        .btn-secondary {
            background: transparent;
            color: var(--ink);
            border-color: #4f7088;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 12px;
            margin-top: 16px;
        }

        .stat {
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 12px;
            background: #0f2231cf;
            animation: slide-in .7s ease-out both;
        }

        .stat:nth-child(1) { animation-delay: .06s; }
        .stat:nth-child(2) { animation-delay: .12s; }
        .stat:nth-child(3) { animation-delay: .18s; }
        .stat:nth-child(4) { animation-delay: .24s; }
        .stat:nth-child(5) { animation-delay: .30s; }

        .label {
            margin: 0;
            font-size: 11px;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .value {
            margin: 6px 0 0;
            font-size: 30px;
            font-family: "Bebas Neue", sans-serif;
            letter-spacing: .05em;
        }

        .panels {
            display: grid;
            grid-template-columns: 1.2fr .8fr;
            gap: 14px;
            margin-top: 14px;
        }

        .panel {
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 18px;
            background: var(--panel);
            animation: slide-in .8s ease-out both;
        }

        .panel h2 {
            margin: 0 0 12px;
            font-size: 16px;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .route-list {
            display: grid;
            gap: 10px;
        }

        .route-item {
            background: var(--panel-2);
            border: 1px solid #2c4f67;
            border-radius: 10px;
            padding: 10px 12px;
            font-family: "IBM Plex Mono", monospace;
            color: #ddf0ff;
            font-size: 13px;
        }

        .chip {
            display: inline-block;
            margin-right: 8px;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            color: #0a151f;
            background: var(--accent-2);
        }

        pre {
            margin: 0;
            border-radius: 10px;
            padding: 12px;
            background: #0b1a26;
            border: 1px solid #274258;
            color: #c6e4ff;
            overflow: auto;
            font-size: 12px;
            font-family: "IBM Plex Mono", monospace;
            line-height: 1.45;
        }

        .footer-note {
            margin-top: 10px;
            color: var(--muted);
            font-size: 12px;
        }

        @keyframes slide-in {
            from {
                opacity: 0;
                transform: translateY(14px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {
            0%, 100% { opacity: .7; }
            50% { opacity: 1; }
        }

        @media (max-width: 980px) {
            .grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .panels {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .hero {
                padding: 20px;
            }

            .grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .value {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    @include('partials.auth-banner')

    @php
        $statLabels = [
            'users' => 'Users',
            'classes' => 'Classes',
            'courses' => 'Courses',
            'chapters' => 'Chapters',
            'contents' => 'Chapter Contents',
        ];
        $relationPayloadJson = json_encode($relationPayloadExample, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    @endphp

    <div class="aura"></div>

    <div class="wrap">
        <section class="hero">
            <p class="kicker">School API Workspace</p>
            <h1>Laravel + Swagger<br>School CRUD Hub</h1>
            <p class="subtitle">
                Cette page centralise les acces rapides de ton mini-projet: documentation Swagger,
                endpoints CRUD ecole et etat des donnees seedees.
            </p>

            <div class="actions">
                <a class="btn btn-primary" href="{{ url('/docs') }}">Open Swagger UI</a>
                <a class="btn btn-secondary" href="{{ url($apiPrefix) }}">API Entrypoint</a>
                <a class="btn btn-secondary" href="{{ url($apiPrefix.'/docs.jsonopenapi') }}">OpenAPI JSON</a>
                <a class="btn btn-secondary" href="{{ route('project.overview') }}">Project Overview</a>
                <a class="btn btn-secondary" href="{{ route('role.home') }}">Espace Test (site)</a>
            </div>
        </section>

        <section class="grid">
            @foreach ($statLabels as $key => $label)
                <article class="stat">
                    <p class="label">{{ $label }}</p>
                    <p class="value">{{ $stats[$key] ?? 0 }}</p>
                </article>
            @endforeach
        </section>

        <section class="panels">
            <article class="panel">
                <h2>Main Endpoints</h2>
                <div class="route-list">
                    @foreach ($mainEndpoints as $endpoint)
                        <div class="route-item">
                            <span class="chip">{{ str_contains($endpoint, '/docs') ? 'DOCS' : 'CRUD' }}</span>{{ $apiPrefix . $endpoint }}
                        </div>
                    @endforeach
                </div>
            </article>

            <article class="panel">
                <h2>Relation Payload Example</h2>
                <pre>{!! $relationPayloadJson !!}</pre>
                <p class="footer-note">
                    Rappel: avec API Platform, les relations se passent en IRI (URL de ressource), pas en *_id.
                </p>
            </article>
        </section>
    </div>
</body>
</html>
