<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    @include('partials.auth-banner')

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} | Video Test</title>
    <style>
        :root {
            --bg: #0f1720;
            --panel: #132638;
            --line: #2d4e67;
            --ink: #eef6ff;
            --muted: #9fbad0;
            --accent: #ffcf5a;
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
            margin: 0 0 8px;
            font-size: clamp(28px, 5vw, 40px);
        }

        p {
            margin: 0 0 12px;
            color: var(--muted);
            line-height: 1.45;
        }

        .hint {
            margin: 12px 0 16px;
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 10px 12px;
            background: #102131;
            font-size: 14px;
        }

        .hint strong {
            color: var(--accent);
        }

        video {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: #000;
            display: block;
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

        .btn-primary {
            background: var(--accent);
            color: #1e1705;
            border-color: #cba13a;
        }

        .success-message {
            display: none;
            margin-top: 14px;
            border: 1px solid #2f7d57;
            border-radius: 10px;
            padding: 10px 12px;
            background: #103224;
            color: #b8f7d8;
            font-weight: 600;
        }

        .success-message.is-visible {
            display: block;
        }

        .video-selector {
            margin: 12px 0 16px;
        }

        .video-selector label {
            display: block;
            margin-bottom: 6px;
            font-size: 13px;
            font-weight: 600;
            color: var(--accent);
        }

        .video-selector select {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #0f2231;
            color: var(--ink);
            font-size: 14px;
            cursor: pointer;
        }

        .video-selector select:hover {
            border-color: #4a7392;
        }

        .video-selector select:focus {
            outline: none;
            border-color: var(--accent);
        }
    </style>
</head>
<body>
    <main class="wrap">
        <section class="panel">
            <h1>Video Test (Anti-triche)</h1>
            <p>
                Cette page sert uniquement a tester le comportement video: des que tu changes de page,
                changes d'onglet, ou fermes la fenetre, la video est automatiquement mise en pause.
            </p>

            <div class="video-selector">
                <label for="videoSelect">Choisir une vidéo:</label>
                <select id="videoSelect">
                    <option value="/videos/test.mp4">test.mp4</option>
                    <option value="/videos/Laravel.mp4">Laravel.mp4</option>
                </select>
            </div>

            <video id="trainingVideo" controls preload="metadata">
                <source src="" type="video/mp4">
                Votre navigateur ne supporte pas la lecture video.
            </video>

            <div id="videoCompletedMessage" class="success-message" role="status" aria-live="polite">
                Bravo, video finie !
            </div>

            <div class="actions">
                <a class="btn btn-primary" href="{{ route('project.overview') }}">Changer de page (test pause)</a>
                <a class="btn" href="{{ route('home') }}">Accueil</a>
                <a class="btn" href="{{ route('docs.redirect') }}">Swagger</a>
            </div>
        </section>
    </main>

    <script>
        const player = document.getElementById('trainingVideo');
        const completedMessage = document.getElementById('videoCompletedMessage');
        const videoSelect = document.getElementById('videoSelect');
        let videoCompleted = false;

        // Initialiser la vidéo
        if (player && videoSelect) {
            const sourceElement = player.querySelector('source');
            player.src = videoSelect.value;
            player.load();

            // Changer la vidéo au changement du select
            videoSelect.addEventListener('change', (e) => {
                if (player.paused || !videoCompleted) {
                    player.pause();
                }
                
                const newVideoPath = e.target.value;
                const sourceElement = player.querySelector('source') || document.createElement('source');
                sourceElement.src = newVideoPath;
                sourceElement.type = 'video/mp4';
                
                if (!player.querySelector('source')) {
                    player.appendChild(sourceElement);
                }
                
                player.load();
                videoCompleted = false;
                completedMessage.classList.remove('is-visible');
            });
        }

        const forcePause = () => {
            if (videoCompleted) {
                return; // Video terminée, autoriser la navigation
            }
            if (player && !player.paused) {
                player.pause();
            }
        };

        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                forcePause();
            }
        });

        window.addEventListener('pagehide', forcePause);
        window.addEventListener('beforeunload', forcePause);
        window.addEventListener('blur', forcePause);

        document.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', forcePause);
        });

        if (player && completedMessage) {
            player.addEventListener('ended', () => {
                completedMessage.classList.add('is-visible');
                videoCompleted = true;
            });

            player.addEventListener('play', () => {
                completedMessage.classList.remove('is-visible');
                videoCompleted = false;
            });
        }
    </script>
</body>
</html>
