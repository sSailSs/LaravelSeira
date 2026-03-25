<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} | Lecteur Vidéo</title>
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
            margin: 0 0 8px;
            font-size: clamp(28px, 5vw, 40px);
        }

        p {
            margin: 0 0 12px;
            color: var(--muted);
            line-height: 1.45;
        }

        video {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: #000;
            display: block;
            margin-bottom: 14px;
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
            margin-bottom: 14px;
            border: 1px solid #2f7d57;
            border-radius: 10px;
            padding: 12px;
            background: #103224;
            color: var(--success);
            font-weight: 600;
            text-align: center;
        }

        .success-message.is-visible {
            display: block;
        }
    </style>
</head>
<body>
    @include('partials.auth-banner')

    <main class="wrap">
        <section class="panel">
            <h1 id="videoTitle">Lecteur</h1>
            <p>
                Attention: la vidéo se met automatiquement en pause si tu changes d'onglet, de page ou fermes la fenêtre.
            </p>

            <div id="videoCompletedMessage" class="success-message" role="status" aria-live="polite">
                Bravo, vidéo finie ! Tu peux maintenant naviguer où tu veux.
            </div>

            <video id="trainingVideo" controls preload="metadata">
                Votre navigateur ne supporte pas la lecture vidéo.
            </video>

            <div class="actions">
                <a class="btn btn-primary" href="{{ route('video.list') }}">Retour aux vidéos</a>
                <a class="btn" href="{{ route('home') }}">Accueil</a>
            </div>
        </section>
    </main>

    <script>
        const userId = {{ auth()->id() ?? 'null' }};
        const completedVideosKey = userId ? `completedVideos_user_${userId}` : 'completedVideos';
        const videoProgressKey = userId ? `videoProgress_user_${userId}` : 'videoProgress';

        const videoFile = "{{ $videoFile }}";
        const player = document.getElementById('trainingVideo');
        const completedMessage = document.getElementById('videoCompletedMessage');
        const videoTitle = document.getElementById('videoTitle');
        let videoCompleted = false;
        let antiCheatDisabled = false;

        // Fonctions de progression
        const saveProgress = () => {
            if (player && !videoCompleted) {
                const progress = JSON.parse(localStorage.getItem(videoProgressKey) || '{}');
                progress[videoFile] = player.currentTime;
                localStorage.setItem(videoProgressKey, JSON.stringify(progress));
            }
        };

        const restoreProgress = () => {
            const progress = JSON.parse(localStorage.getItem(videoProgressKey) || '{}');
            const savedTime = progress[videoFile];
            if (savedTime && savedTime > 0 && player && player.duration > 0) {
                player.currentTime = savedTime;
                console.log(`Progression restaurée: ${videoFile} à ${savedTime.toFixed(2)}s / ${player.duration.toFixed(2)}s`);
            }
        };

        // Initialiser la vidéo
        if (player && videoFile) {
            const videoPath = `/videos/${videoFile}`;
            const source = document.createElement('source');
            source.src = videoPath;
            source.type = 'video/mp4';
            player.appendChild(source);
            player.load();

            // Titre
            videoTitle.textContent = videoFile;

            // Vérifier si la vidéo est déjà complétée au chargement
            const completedVideos = JSON.parse(localStorage.getItem(completedVideosKey) || '[]');
            if (completedVideos.includes(videoFile)) {
                videoCompleted = true;
                antiCheatDisabled = true;
            }

            // Restaurer la progression via plusieurs événements pour plus de fiabilité
            player.addEventListener('loadedmetadata', restoreProgress, { once: true });
            player.addEventListener('canplay', restoreProgress, { once: true });
            
            // Fallback: restaurer après un court délai
            setTimeout(restoreProgress, 500);
        }

        const forcePause = () => {
            if (player) {
                player.pause();
                // Sauvegarder la progression avant de forcer la pause
                saveProgress();
            }
        };

        // Sauvegarder la progression lors du changement d'onglet ou de page (anti-cheat)
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                saveProgress();
                if (!antiCheatDisabled) {
                    forcePause();
                }
            }
        });

        window.addEventListener('beforeunload', () => {
            saveProgress();
        });

        window.addEventListener('blur', () => {
            if (!antiCheatDisabled) {
                forcePause();
            }
        });

        // Sauvegarder la progression quand on appuie sur pause
        if (player) {
            player.addEventListener('pause', () => {
                saveProgress();
            });

            player.addEventListener('play', () => {
                // Enlever le message de succès si on rejoue
                if (completedMessage) {
                    completedMessage.classList.remove('is-visible');
                }
            });
        }

        // Empêcher la navigation sur les liens (anti-cheat seulement avant completion)
        document.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', (e) => {
                if (!antiCheatDisabled) {
                    e.preventDefault();
                    saveProgress();
                    forcePause();
                    // Naviguer après un délai pour s'assurer que la pause est exécutée
                    setTimeout(() => {
                        window.location.href = link.href;
                    }, 200);
                }
            });
        });

        if (player && completedMessage) {
            player.addEventListener('ended', () => {
                completedMessage.classList.add('is-visible');
                videoCompleted = true;
                antiCheatDisabled = true; // Désactiver l'anti-triche une fois finie

                // Marquer la vidéo comme complétée dans localStorage
                const completed = JSON.parse(localStorage.getItem(completedVideosKey) || '[]');
                if (!completed.includes(videoFile)) {
                    completed.push(videoFile);
                    localStorage.setItem(completedVideosKey, JSON.stringify(completed));
                }

                // Vidéo terminée: nettoyer la progression partielle sauvegardée
                const progress = JSON.parse(localStorage.getItem(videoProgressKey) || '{}');
                delete progress[videoFile];
                localStorage.setItem(videoProgressKey, JSON.stringify(progress));
            });
        }
    </script>
</body>
</html>
