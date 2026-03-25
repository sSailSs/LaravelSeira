<style>
    .auth-banner {
        width: 100%;
        border-bottom: 1px solid #355169;
        background: linear-gradient(90deg, #102131 0%, #162f44 100%);
        color: #eef6ff;
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    }

    .auth-banner__inner {
        max-width: 1100px;
        margin: 0 auto;
        padding: 10px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        flex-wrap: wrap;
    }

    .auth-banner__label {
        margin: 0;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 0.02em;
    }

    .auth-banner__meta {
        margin: 0;
        font-size: 13px;
        color: #b9d2e8;
    }

    .auth-banner__actions {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .auth-banner__logout {
        border: 1px solid #4f7088;
        background: transparent;
        color: #eef6ff;
        border-radius: 8px;
        padding: 6px 10px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
    }

    .auth-banner__logout:hover {
        border-color: #ffcf5a;
        color: #ffcf5a;
    }

    .auth-banner__badge {
        display: inline-block;
        margin-left: 8px;
        padding: 2px 8px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        background: #63d490;
        color: #11261b;
    }

    .auth-banner__badge.guest {
        background: #ffcf5a;
        color: #1d1606;
    }
</style>

<div class="auth-banner" role="status" aria-live="polite">
    <div class="auth-banner__inner">
        @auth
            <p class="auth-banner__label">
                Compte connecté:
                <span class="auth-banner__badge">Connecté</span>
            </p>
            <div class="auth-banner__actions">
                <p class="auth-banner__meta">
                    {{ auth()->user()->name }} ({{ auth()->user()->email }})
                </p>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="auth-banner__logout">Déconnexion</button>
                </form>
            </div>
        @else
            <p class="auth-banner__label">
                Compte connecté:
                <span class="auth-banner__badge guest">Non connecté</span>
            </p>
            <p class="auth-banner__meta">Connecte-toi pour suivre tes vidéos et ta progression.</p>
        @endauth
    </div>
</div>