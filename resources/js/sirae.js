/* Sirae — shared UI behaviours (theme, menus, sidebar) */
(function () {
    window.Sirae = window.Sirae || {};

    /* Theme toggle */
    window.Sirae.toggleTheme = function () {
        const dark = document.documentElement.classList.toggle('dark');
        try { localStorage.setItem('sirae-theme', dark ? 'dark' : 'light'); } catch (e) {}
        syncThemeButtons();
    };

    function syncThemeButtons() {
        const dark = document.documentElement.classList.contains('dark');
        document.querySelectorAll('[data-theme-sun]').forEach(el => { el.hidden = dark; });
        document.querySelectorAll('[data-theme-moon]').forEach(el => { el.hidden = !dark; });
    }

    window.Sirae._syncThemeButtons = syncThemeButtons;
    document.addEventListener('DOMContentLoaded', syncThemeButtons);

    /* Dropdown menus (data-menu / data-menu-panel) */
    document.addEventListener('click', function (e) {
        const trigger = e.target.closest('[data-menu]');
        document.querySelectorAll('[data-menu-panel]').forEach(function (panel) {
            const keep = trigger && panel.getAttribute('data-menu-panel') === trigger.getAttribute('data-menu');
            if (!keep && !e.target.closest('[data-menu-panel]')) panel.hidden = true;
        });
        if (trigger) {
            const id = trigger.getAttribute('data-menu');
            const panel = document.querySelector('[data-menu-panel="' + id + '"]');
            if (panel) panel.hidden = !panel.hidden;
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('[data-menu-panel]').forEach(p => { p.hidden = true; });
        }
    });

    /* Mobile sidebar */
    window.Sirae.toggleSidebar = function () {
        const sb = document.querySelector('[data-sidebar]');
        const ov = document.querySelector('[data-sidebar-overlay]');
        if (!sb) return;
        const open = sb.classList.toggle('is-open');
        if (ov) ov.hidden = !open;
    };

    window.Sirae.closeSidebar = function () {
        const sb = document.querySelector('[data-sidebar]');
        const ov = document.querySelector('[data-sidebar-overlay]');
        if (sb) sb.classList.remove('is-open');
        if (ov) ov.hidden = true;
    };
})();
