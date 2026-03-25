<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} | Videos</title>
    <style>
        :root {
            --bg: #0f1720;
            --panel: #132638;
            --line: #2d4e67;
            --ink: #eef6ff;
            --muted: #9fbad0;
            --accent: #ffcf5a;
            --success: #63d490;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            color: var(--ink);
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background:
                radial-gradient(circle at 85% 12%, #294861 0%, transparent 35%),
                radial-gradient(circle at 15% 85%, #1d3d2f 0%, transparent 30%),
                linear-gradient(145deg, #0c141c 0%, #101e2b 100%);
        }

        .wrap {
            max-width: 920px;
            margin: 0 auto;
            padding: 28px 18px 40px;
        }

        .panel {
            border: 1px solid var(--line);
            border-radius: 16px;
            background: var(--panel);
            padding: 18px;
            box-shadow: 0 16px 32px #00000044;
        }

        h1 {
            margin: 0 0 12px;
            font-size: clamp(28px, 5vw, 40px);
        }

        p {
            margin: 0 0 18px;
            color: var(--muted);
            line-height: 1.45;
        }

        .videos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 14px;
            margin-bottom: 16px;
        }

        .video-card {
            border: 1px solid var(--line);
            border-radius: 12px;
            background: #0f2231;
            padding: 14px;
            transition: border-color .2s ease;
            display: flex;
            flex-direction: column;
        }

        .video-card:hover {
            border-color: #4a7392;
        }

        .video-title {
            margin: 0 0 6px;
            font-size: 16px;
            font-weight: 600;
            color: var(--ink);
        }

        .video-meta {
            margin: 0 0 10px;
            font-size: 13px;
            color: var(--muted);
        }

        .video-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            margin-bottom: 10px;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            width: fit-content;
        }

        .video-badge.validated {
            background: rgba(99, 212, 144, 0.2);
            color: var(--success);
        }

        .video-badge.pending {
            background: rgba(255, 207, 90, 0.2);
            color: var(--accent);
        }

        .video-badge::before {
            content: '✓';
        }

        .video-badge.pending::before {
            content: '○';
        }

        .video-link {
            display: inline-block;
            margin-top: auto;
            text-decoration: none;
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 8px 12px;
            color: var(--ink);
            font-weight: 600;
            font-size: 13px;
            text-align: center;
            transition: all .2s ease;
        }

        .video-link:hover {
            background: var(--accent);
            color: #1e1705;
            border-color: var(--accent);
        }

        .actions {
            margin-top: 14px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            text-decoration: none;
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 9px 12px;
            color: var(--ink);
            font-weight: 600;
            font-size: 14px;
        }

        .btn:hover {
            border-color: #4a7392;
        }
    </style>
</head>
<body>
    @include('partials.auth-banner')

    <main class="wrap">
        <section class="panel">
            <h1>Vidéos de Test</h1>
            <p>
                Sélectionne une vidéo pour la regarder. Dès que tu quittes la page ou changes d'onglet,
                la vidéo se met automatiquement en pause (anti-triche). Une fois finie, tu peux naviguer librement.
            </p>

            <div class="videos-grid" id="videosContainer"></div>

            <div class="actions">
                <a class="btn" href="{{ route('home') }}">Accueil</a>
                <a class="btn" href="{{ route('project.overview') }}">Projet</a>
            </div>
        </section>
    </main>

    <script>
        const userId = {{ auth()->id() ?? 'null' }};
        const completedVideosKey = userId ? `completedVideos_user_${userId}` : 'completedVideos';

        const videos = [
            { file: 'test.mp4', label: 'Test', link: '{{ route("video.player", "test.mp4") }}' },
            { file: 'Laravel.mp4', label: 'Laravel', link: '{{ route("video.player", "Laravel.mp4") }}' }
        ];

        const container = document.getElementById('videosContainer');
        const completedVideos = JSON.parse(localStorage.getItem(completedVideosKey) || '[]');

        videos.forEach((video) => {
            const isCompleted = completedVideos.includes(video.file);
            const card = document.createElement('div');
            card.className = 'video-card';
            card.innerHTML = `
                <p class="video-title">${video.label}</p>
                <p class="video-meta">${video.file}</p>
                <div class="video-badge ${isCompleted ? 'validated' : 'pending'}">
                    ${isCompleted ? 'Validée' : 'À regarder'}
                </div>
                <a href="${video.link}" class="video-link">
                    ${isCompleted ? 'Revoir' : 'Regarder'}
                </a>
            `;
            container.appendChild(card);
        });
    </script>
</body>
</html>
