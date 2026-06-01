/* Sirae — comportements partagés (vanilla, transposable en Alpine/HTMX) */

/* ---- Thème clair / sombre ---- */
(function () {
  window.Sirae = window.Sirae || {};
  window.Sirae.toggleTheme = function () {
    var dark = document.documentElement.classList.toggle('dark');
    try { localStorage.setItem('sirae-theme', dark ? 'dark' : 'light'); } catch (e) {}
    syncThemeButtons();
  };
  function syncThemeButtons() {
    var dark = document.documentElement.classList.contains('dark');
    document.querySelectorAll('[data-theme-sun]').forEach(function (el) { el.hidden = dark; });
    document.querySelectorAll('[data-theme-moon]').forEach(function (el) { el.hidden = !dark; });
  }
  window.Sirae._syncThemeButtons = syncThemeButtons;
  document.addEventListener('DOMContentLoaded', syncThemeButtons);
})();

/* ---- Menus déroulants (data-menu / data-menu-panel) ---- */
document.addEventListener('click', function (e) {
  var trigger = e.target.closest('[data-menu]');
  document.querySelectorAll('[data-menu-panel]').forEach(function (panel) {
    var keep = trigger && panel.getAttribute('data-menu-panel') === trigger.getAttribute('data-menu');
    if (!keep && !e.target.closest('[data-menu-panel]')) panel.hidden = true;
  });
  if (trigger) {
    var id = trigger.getAttribute('data-menu');
    var panel = document.querySelector('[data-menu-panel="' + id + '"]');
    if (panel) panel.hidden = !panel.hidden;
  }
});
document.addEventListener('keydown', function (e) {
  if (e.key === 'Escape') document.querySelectorAll('[data-menu-panel]').forEach(function (p) { p.hidden = true; });
});

/* ---- Mobile : ouverture/fermeture sidebar ---- */
window.Sirae = window.Sirae || {};
window.Sirae.toggleSidebar = function () {
  var sb = document.querySelector('[data-sidebar]');
  var ov = document.querySelector('[data-sidebar-overlay]');
  if (!sb) return;
  var open = sb.classList.toggle('is-open');
  if (ov) ov.hidden = !open;
};
window.Sirae.closeSidebar = function () {
  var sb = document.querySelector('[data-sidebar]');
  var ov = document.querySelector('[data-sidebar-overlay]');
  if (sb) sb.classList.remove('is-open');
  if (ov) ov.hidden = true;
};
