{{--
  Admin sidebar partial.
  Requires: $activeNav (string: overview|courses|classes|teachers|students)
--}}
@php
$colors = ['#3f6b8a','#7a5c46','#5a6b4a','#8a4f6b','#6b5a4a','#4a6b5a','#8a6b3a'];
$uName   = auth()->user()->name;
$uParts  = preg_split('/\s+/', trim($uName));
$uInit   = mb_strtoupper(mb_substr($uParts[0], 0, 1)) . (count($uParts) > 1 ? mb_strtoupper(mb_substr(end($uParts), 0, 1)) : '');
$uColor  = $colors[abs(crc32($uName)) % count($colors)];
@endphp

<aside class="sidebar w-[264px] shrink-0 h-full flex flex-col" data-sidebar>
  <div class="px-4 pt-4 pb-3">
    <div class="flex items-center gap-3 p-2">
      <span class="w-9 h-9 rounded-[11px] bg-accent grid place-items-center text-white font-extrabold text-[15px] shrink-0">S</span>
      <span class="min-w-0 flex-1">
        <span class="block text-[14.5px] font-bold leading-tight truncate">Monto</span>
        <span class="block text-[12px] text-faint leading-tight">Administration</span>
      </span>
    </div>
  </div>

  <nav class="flex-1 overflow-y-auto scroll-area px-3 pb-3 space-y-5">
    <div>
      <p class="px-3 mb-1.5 text-[11px] font-semibold uppercase tracking-wider text-faint">Pilotage</p>
      <div class="space-y-0.5">
        <a href="{{ route('space.admin') }}" class="nav-item {{ ($activeNav ?? '') === 'overview' ? 'active' : '' }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/></svg>
          Vue d'ensemble
        </a>
      </div>
    </div>
    <div>
      <p class="px-3 mb-1.5 text-[11px] font-semibold uppercase tracking-wider text-faint">Gestion</p>
      <div class="space-y-0.5">
        <a href="{{ route('space.admin.courses') }}" class="nav-item {{ ($activeNav ?? '') === 'courses' ? 'active' : '' }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 5.5A1.5 1.5 0 015.5 4H11a2 2 0 012 2v14a1.5 1.5 0 00-1.5-1.5H4z"/><path d="M20 5.5A1.5 1.5 0 0018.5 4H13a2 2 0 00-2 2v14a1.5 1.5 0 011.5-1.5H20z"/></svg>
          Matières
        </a>
        <a href="{{ route('space.admin.classes') }}" class="nav-item {{ ($activeNav ?? '') === 'classes' ? 'active' : '' }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="4" width="18" height="14" rx="2"/><path d="M3 8h18M7 21h10"/></svg>
          Classes
        </a>
        <a href="{{ route('space.admin.teachers') }}" class="nav-item {{ ($activeNav ?? '') === 'teachers' ? 'active' : '' }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 21v-1a5 5 0 015-5h0a5 5 0 015 5v1"/><circle cx="8" cy="7" r="3.2"/><path d="M16 14a4 4 0 013.8 2.8M15 7.5a3 3 0 100-1"/></svg>
          Formateurs
        </a>
        <a href="{{ route('space.admin.students') }}" class="nav-item {{ ($activeNav ?? '') === 'students' ? 'active' : '' }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 4L2 9l10 5 10-5-10-5z"/><path d="M6 11v4.5c0 1.4 2.7 2.5 6 2.5s6-1.1 6-2.5V11M22 9v5"/></svg>
          Élèves
        </a>
      </div>
    </div>
    <div>
      <p class="px-3 mb-1.5 text-[11px] font-semibold uppercase tracking-wider text-faint">Système</p>
      <div class="space-y-0.5">
        <a href="{{ route('profile.edit') }}" class="nav-item {{ ($activeNav ?? '') === 'settings' ? 'active' : '' }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.6 1.6 0 00.3 1.8l.1.1a2 2 0 11-2.8 2.8l-.1-.1a1.6 1.6 0 00-2.7 1.1V21a2 2 0 11-4 0v-.1A1.6 1.6 0 005 19.4l-.1.1a2 2 0 11-2.8-2.8l.1-.1A1.6 1.6 0 002.3 14H2a2 2 0 110-4h.1A1.6 1.6 0 003.4 7.3L3.3 7.2a2 2 0 112.8-2.8l.1.1A1.6 1.6 0 009 4.6V4a2 2 0 114 0v.1a1.6 1.6 0 002.7 1.1l.1-.1a2 2 0 112.8 2.8l-.1.1a1.6 1.6 0 001.1 2.7H21a2 2 0 110 4h-.1a1.6 1.6 0 00-1.5 1z"/></svg>
          Paramètres
        </a>
      </div>
    </div>
  </nav>

  <div class="p-3 border-t border-border">
    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 p-2 rounded-xl hover:bg-surface-2 transition">
      <span class="avatar" style="background: {{ $uColor }}">{{ $uInit }}</span>
      <span class="min-w-0 flex-1">
        <span class="block text-[13.5px] font-semibold leading-tight truncate">{{ $uName }}</span>
        <span class="block text-[12px] text-faint leading-tight">Administrateur</span>
      </span>
    </a>
  </div>
</aside>
<div class="sidebar-overlay" data-sidebar-overlay hidden onclick="Sirae.closeSidebar()"></div>
