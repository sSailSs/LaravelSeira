@extends('layouts.sirae')
@section('title', $course->title)

@php
function siraieInitials(string $name): string {
    $parts = preg_split('/\s+/', trim($name));
    $f = mb_strtoupper(mb_substr($parts[0], 0, 1));
    $l = count($parts) > 1 ? mb_strtoupper(mb_substr(end($parts), 0, 1)) : '';
    return $f . $l;
}
function siraieColor(string $name): string {
    $c = ['#3f6b8a','#7a5c46','#5a6b4a','#8a4f6b','#6b5a4a','#4a6b5a','#8a6b3a'];
    return $c[abs(crc32($name)) % count($c)];
}
@endphp

@section('body')
<div class="h-screen flex overflow-hidden">

  {{-- SIDEBAR --}}
  <aside class="sidebar w-[264px] shrink-0 h-full flex flex-col" data-sidebar>
    <div class="px-4 pt-4 pb-3">
      <div class="flex items-center gap-3 p-2">
        <span class="w-9 h-9 rounded-[11px] bg-accent grid place-items-center text-white font-extrabold text-[15px] shrink-0">S</span>
        <span class="min-w-0 flex-1">
          <span class="block text-[14.5px] font-bold leading-tight">Sirae</span>
          <span class="block text-[12px] text-faint leading-tight">Espace formateur</span>
        </span>
      </div>
    </div>
    <nav class="flex-1 overflow-y-auto scroll-area px-3 pb-3 space-y-5">
      <div class="space-y-0.5">
        <a href="{{ route('space.prof') }}" class="nav-item">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/></svg>
          Tableau de bord
        </a>
        <a href="{{ route('space.prof') }}" class="nav-item active">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 5.5A1.5 1.5 0 015.5 4H11a2 2 0 012 2v14a1.5 1.5 0 00-1.5-1.5H4zM20 5.5A1.5 1.5 0 0018.5 4H13a2 2 0 00-2 2v14a1.5 1.5 0 011.5-1.5H20z"/></svg>
          Mes matières <span class="ml-auto badge">{{ $teacherCourses->count() }}</span>
        </a>
      </div>
      @if($teacherCourses->isNotEmpty())
      <div>
        <p class="px-3 mb-1.5 text-[11px] font-semibold uppercase tracking-wider text-faint">Mes matières</p>
        <div class="space-y-0.5">
          @foreach($teacherCourses as $tc)
          <a href="{{ route('space.prof.course', $tc) }}" class="nav-item {{ $tc->id === $course->id ? '!text-ink' : '' }}" style="{{ $tc->id === $course->id ? 'background:var(--surface-2)' : '' }}">
            <span class="w-2 h-2 rounded-full shrink-0" style="background: {{ $tc->id === $course->id ? 'var(--accent)' : 'var(--faint)' }}"></span>
            <span class="truncate">{{ $tc->title }}</span>
          </a>
          @endforeach
        </div>
      </div>
      @endif
    </nav>
    <div class="p-3 border-t border-border">
      <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 p-2 rounded-xl hover:bg-surface-2 transition">
        <span class="avatar" style="background: {{ siraieColor(auth()->user()->name) }}">{{ siraieInitials(auth()->user()->name) }}</span>
        <span class="min-w-0 flex-1">
          <span class="block text-[13.5px] font-semibold leading-tight truncate">{{ auth()->user()->name }}</span>
          <span class="block text-[12px] text-faint leading-tight">Formateur</span>
        </span>
      </a>
    </div>
  </aside>
  <div class="sidebar-overlay" data-sidebar-overlay hidden onclick="Sirae.closeSidebar()"></div>

  {{-- MAIN --}}
  <div class="flex-1 flex flex-col min-w-0 h-full">

    <header class="shrink-0 h-[68px] border-b border-border topbar-blur flex items-center gap-3 px-5 sm:px-7">
      <button class="btn btn-ghost btn-icon btn-sm burger -ml-1" onclick="Sirae.toggleSidebar()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-5 h-5"><path d="M4 6h16M4 12h16M4 18h16" stroke-width="2" stroke-linecap="round"/></svg>
      </button>
      <nav class="flex items-center gap-2 text-[13.5px] min-w-0">
        <a href="{{ route('space.prof') }}" class="text-muted hover:text-ink">Mes matières</a>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-4 h-4 text-faint shrink-0"><path d="M9 18l6-6-6-6" stroke-width="2" stroke-linecap="round"/></svg>
        <span class="font-semibold truncate">{{ $course->title }}</span>
      </nav>
      <div class="ml-auto flex items-center gap-2">
        <button class="btn btn-ghost btn-icon" onclick="Sirae.toggleTheme()">
          <svg data-theme-sun viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-5 h-5"><circle cx="12" cy="12" r="4.5"/><path d="M12 2v2M12 20v2M4 12H2M22 12h-2M5 5l1.5 1.5M17.5 17.5L19 19M19 5l-1.5 1.5M6.5 17.5L5 19" stroke-linecap="round"/></svg>
          <svg data-theme-moon viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-5 h-5" hidden><path d="M21 12.8A8.5 8.5 0 1111.2 3a6.6 6.6 0 009.8 9.8z" stroke-linejoin="round"/></svg>
        </button>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-ghost btn-sm text-muted">Déconnexion</button></form>
      </div>
    </header>

    <main class="flex-1 overflow-y-auto scroll-area">
      <div class="max-w-[1180px] mx-auto px-5 sm:px-7 py-7">

        @if(session('status'))
          <div class="card p-3 mb-5 border-success/40 bg-success-soft text-success text-[13.5px] font-medium">{{ session('status') }}</div>
        @endif

        {{-- Matière header --}}
        <div class="flex flex-wrap items-start gap-4 mb-6">
          <span class="w-12 h-12 rounded-2xl grid place-items-center shrink-0 bg-accent-soft text-accent-ink">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-6 h-6" stroke-width="1.8"><path d="M8 9l-3 3 3 3M16 9l3 3-3 3M13.5 6l-3 12" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </span>
          <div class="flex-1 min-w-[240px]">
            <h1 class="text-[24px] font-extrabold leading-tight">{{ $course->title }}</h1>
            <div class="flex flex-wrap items-center gap-2 mt-2">
              @if($course->schoolClass)
                <span class="badge badge-accent">{{ $course->schoolClass->name }}</span>
              @endif
              <span class="text-[13px] text-muted tnum">{{ $videoCount }} vidéo{{ $videoCount != 1 ? 's' : '' }}</span>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <button class="btn btn-primary" onclick="openAddModal(null)">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 5v14M5 12h14" stroke-width="2.2" stroke-linecap="round"/></svg>
              Ajouter une vidéo
            </button>
            <div class="relative">
              <button data-menu="course-opts" class="btn btn-ghost btn-icon" aria-label="Options de la matière">
                <svg viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><circle cx="12" cy="5" r="1.6"/><circle cx="12" cy="12" r="1.6"/><circle cx="12" cy="19" r="1.6"/></svg>
              </button>
              <div data-menu-panel="course-opts" hidden class="absolute right-0 top-full mt-1 z-30 card shadow-soft min-w-[190px] p-1">
                <button onclick="document.getElementById('editCourseModal').hidden=false" class="w-full text-left flex items-center gap-2 px-3 py-2 rounded-lg text-[13.5px] hover:bg-surface-2">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-4 h-4 shrink-0"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                  Modifier la matière
                </button>
                <button onclick="if(confirm('Supprimer définitivement cette matière et tout son contenu ?')) document.getElementById('del-course').submit()" class="w-full text-left flex items-center gap-2 px-3 py-2 rounded-lg text-[13.5px] hover:bg-surface-2 text-red-500">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-4 h-4 shrink-0"><path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                  Supprimer la matière
                </button>
              </div>
              <form id="del-course" method="POST" action="{{ route('space.prof.course.destroy', $course) }}" style="display:none">
                @csrf @method('DELETE')
              </form>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 items-start">

          {{-- Séquence pédagogique --}}
          <section class="xl:col-span-2 space-y-5">
            <div class="flex items-center gap-3">
              <h2 class="text-[16px] font-bold">Séquence pédagogique</h2>
              <span class="text-[12.5px] text-faint">Glissez pour réordonner</span>
              <button class="ml-auto btn btn-ghost btn-sm" onclick="openChapterModal()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 5v14M5 12h14" stroke-width="2.2" stroke-linecap="round"/></svg>
                Module
              </button>
            </div>

            @forelse($course->chapters as $chapter)
            <div class="card" data-chapter="{{ $chapter->id }}">
              <div class="flex items-center gap-3 px-5 py-3.5 border-b border-border bg-surface-2/40 rounded-t-[15px]">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-4 h-4 text-faint"><path d="M6 9l6 6 6-6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <h3 class="font-bold text-[14.5px]">{{ $chapter->title }}</h3>
                <span class="badge ml-1">{{ $chapter->contents->count() }} vidéo{{ $chapter->contents->count() != 1 ? 's' : '' }}</span>
                <button class="ml-auto btn btn-ghost btn-sm btn-icon" onclick="openAddModal({{ $chapter->id }})" aria-label="Ajouter une vidéo">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-4 h-4"><path d="M12 5v14M5 12h14" stroke-width="2.2" stroke-linecap="round"/></svg>
                </button>
              </div>
              @if($chapter->contents->isEmpty())
                <div class="px-5 py-6 text-center text-muted text-[13.5px]">
                  Aucun contenu.
                  <button onclick="openAddModal({{ $chapter->id }})" class="text-accent-ink font-semibold hover:underline ml-1">Ajouter une vidéo</button>
                </div>
              @else
              <div class="p-2.5 space-y-1.5 seq-list" data-list="{{ $chapter->id }}">
                @foreach($chapter->contents as $index => $content)
                <div class="seq-item flex items-center gap-3 p-2.5 rounded-xl border border-border bg-surface" draggable="true" data-id="{{ $content->id }}">
                  <span class="handle text-faint shrink-0">
                    <svg viewBox="0 0 24 24" fill="none" class="w-4 h-4">
                      <circle cx="9" cy="6" r="1.4" fill="currentColor"/><circle cx="15" cy="6" r="1.4" fill="currentColor"/>
                      <circle cx="9" cy="12" r="1.4" fill="currentColor"/><circle cx="15" cy="12" r="1.4" fill="currentColor"/>
                      <circle cx="9" cy="18" r="1.4" fill="currentColor"/><circle cx="15" cy="18" r="1.4" fill="currentColor"/>
                    </svg>
                  </span>
                  <span class="seq-num w-6 text-center font-bold text-[13px] text-faint tnum shrink-0">{{ $loop->iteration }}</span>
                  <span class="media-ph w-[68px] h-[40px] rounded-lg shrink-0">
                    @if($content->duration_seconds)
                      {{ gmdate('i:s', $content->duration_seconds) }}
                    @else
                      VIDÉO
                    @endif
                  </span>
                  <div class="flex-1 min-w-0">
                    <div class="font-semibold text-[14px] leading-tight truncate">{{ $content->title ?: 'Vidéo sans titre' }}</div>
                    <div class="text-[12px] text-faint">Vidéo{{ $content->duration_seconds ? ' · ' . ceil($content->duration_seconds / 60) . ' min' : '' }}</div>
                  </div>
                  <span class="badge badge-success">Publié</span>
                  {{-- Three-dot dropdown --}}
                  <div class="relative shrink-0">
                    <button data-menu="vid-{{ $content->id }}" class="btn btn-ghost btn-icon btn-sm" aria-label="Options">
                      <svg viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><circle cx="12" cy="5" r="1.6"/><circle cx="12" cy="12" r="1.6"/><circle cx="12" cy="19" r="1.6"/></svg>
                    </button>
                    <div data-menu-panel="vid-{{ $content->id }}" hidden class="absolute right-0 top-full mt-1 z-20 card shadow-soft min-w-[150px] p-1">
                      <button onclick="openEditModal({{ $content->id }}, @js($content->title ?? ''), @js($content->video_url ?? ''), {{ $content->duration_seconds ?? 0 }})" class="w-full text-left flex items-center gap-2 px-3 py-2 rounded-lg text-[13.5px] hover:bg-surface-2">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-4 h-4 shrink-0"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Modifier
                      </button>
                      <button onclick="if(confirm('Supprimer cette vidéo ?')) document.getElementById('del-{{ $content->id }}').submit()" class="w-full text-left flex items-center gap-2 px-3 py-2 rounded-lg text-[13.5px] hover:bg-surface-2 text-red-500">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-4 h-4 shrink-0"><path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Supprimer
                      </button>
                    </div>
                    <form id="del-{{ $content->id }}" method="POST" action="{{ route('space.prof.content.destroy', [$course, $content]) }}" style="display:none">
                      @csrf @method('DELETE')
                    </form>
                  </div>
                </div>
                @endforeach
              </div>
              @endif
            </div>
            @empty
            <div class="card p-8 text-center">
              <p class="text-muted text-[14px] mb-4">Aucun module pour le moment.</p>
              <button onclick="openChapterModal()" class="btn btn-secondary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 5v14M5 12h14" stroke-width="2.2" stroke-linecap="round"/></svg>
                Créer un premier module
              </button>
            </div>
            @endforelse
          </section>

          {{-- Info panel --}}
          <aside class="space-y-5">
            <section class="card p-5">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-[14px] font-bold">Vidéos</h3>
                <span class="text-[13px] font-semibold tnum">{{ $videoCount }}</span>
              </div>
              <div class="track"><i style="width: {{ $videoCount > 0 ? min(100, $videoCount * 3) : 0 }}%"></i></div>
            </section>

            @if($allClasses->isNotEmpty())
            <section class="card p-5">
              <h3 class="text-[14px] font-bold mb-3">Classes associées</h3>
              <div class="space-y-2">
                @foreach($allClasses as $cls)
                <div class="flex items-center gap-2.5">
                  <span class="w-7 h-7 rounded-lg bg-surface-2 border border-border grid place-items-center text-[11px] font-bold text-muted">{{ mb_strtoupper(mb_substr(preg_replace('/[^A-Za-z0-9]/', '', $cls->name), 0, 2)) }}</span>
                  <span class="text-[13.5px] flex-1">{{ $cls->name }}</span>
                </div>
                @endforeach
              </div>
            </section>
            @endif

            @if($course->description)
            <section class="card p-5">
              <h3 class="text-[14px] font-bold mb-2.5">Description</h3>
              <p class="text-[13.5px] text-muted leading-snug">{{ $course->description }}</p>
            </section>
            @endif

            <section class="card p-5">
              <h3 class="text-[14px] font-bold mb-3">Actions rapides</h3>
              <div class="space-y-2">
                <button onclick="openAddModal(null)" class="btn btn-secondary w-full justify-start">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M15 10l4 4-4 4M19 14H9M4 6l3-3 3 3M7 3v13" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                  Ajouter une vidéo
                </button>
                <button onclick="openChapterModal()" class="btn btn-ghost w-full justify-start text-muted">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 5v14M5 12h14" stroke-width="2.2" stroke-linecap="round"/></svg>
                  Nouveau module
                </button>
              </div>
            </section>
          </aside>
        </div>

      </div>
    </main>
  </div>
</div>

{{-- Ajouter une vidéo (modal) --}}
<div id="addModal" hidden class="fixed inset-0 z-[60] flex items-end sm:items-center justify-center p-0 sm:p-6">
  <div class="absolute inset-0 bg-black/45" onclick="closeAddModal()"></div>
  <form method="POST" id="addForm" action="#" class="relative card shadow-soft w-full sm:max-w-[480px] rounded-b-none sm:rounded-2xl p-6">
    @csrf
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-[17px] font-bold">Ajouter une vidéo</h3>
      <button type="button" class="btn btn-ghost btn-icon btn-sm" onclick="closeAddModal()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-5 h-5"><path d="M6 6l12 12M18 6L6 18" stroke-width="2" stroke-linecap="round"/></svg>
      </button>
    </div>
    <label class="block text-[13px] font-semibold mb-1.5">Titre</label>
    <input name="title" class="w-full h-10 px-3 rounded-[10px] bg-surface border border-border-strong outline-none focus-ring text-[14px] mb-4" placeholder="Ex. Introduction aux variables">
    <label class="block text-[13px] font-semibold mb-1.5">URL de la vidéo</label>
    <input name="video_url" type="url" required class="w-full h-10 px-3 rounded-[10px] bg-surface border border-border-strong outline-none focus-ring text-[14px] mb-4" placeholder="https://…">
    <div class="flex items-center gap-3 mb-5">
      <div class="flex-1">
        <label class="block text-[13px] font-semibold mb-1.5">Module</label>
        <select name="chapter_id" id="chapterSelect" required class="w-full h-10 px-3 rounded-[10px] bg-surface border border-border-strong outline-none focus-ring text-[14px]">
          @foreach($course->chapters as $chapter)
            <option value="{{ $chapter->id }}">{{ $chapter->title }}</option>
          @endforeach
        </select>
      </div>
      <div class="w-28">
        <label class="block text-[13px] font-semibold mb-1.5">Durée (s)</label>
        <input name="duration_seconds" type="number" min="1" class="w-full h-10 px-3 rounded-[10px] bg-surface border border-border-strong outline-none focus-ring text-[14px]" placeholder="240">
      </div>
    </div>
    <input type="hidden" name="content_type" value="video">
    <div class="flex justify-end gap-2">
      <button type="button" class="btn btn-secondary" onclick="closeAddModal()">Annuler</button>
      <button type="submit" class="btn btn-primary">Ajouter</button>
    </div>
  </form>
</div>

{{-- Modifier une vidéo (modal, partagé) --}}
<div id="editModal" hidden class="fixed inset-0 z-[60] flex items-end sm:items-center justify-center p-0 sm:p-6">
  <div class="absolute inset-0 bg-black/45" onclick="document.getElementById('editModal').hidden=true"></div>
  <form id="editForm" method="POST" action="" class="relative card shadow-soft w-full sm:max-w-[480px] rounded-b-none sm:rounded-2xl p-6">
    @csrf @method('PATCH')
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-[17px] font-bold">Modifier la vidéo</h3>
      <button type="button" class="btn btn-ghost btn-icon btn-sm" onclick="document.getElementById('editModal').hidden=true">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-5 h-5"><path d="M6 6l12 12M18 6L6 18" stroke-width="2" stroke-linecap="round"/></svg>
      </button>
    </div>
    <label class="block text-[13px] font-semibold mb-1.5">Titre</label>
    <input id="editTitle" name="title" class="w-full h-10 px-3 rounded-[10px] bg-surface border border-border-strong outline-none focus-ring text-[14px] mb-4">
    <label class="block text-[13px] font-semibold mb-1.5">URL de la vidéo</label>
    <input id="editVideoUrl" name="video_url" type="url" class="w-full h-10 px-3 rounded-[10px] bg-surface border border-border-strong outline-none focus-ring text-[14px] mb-4" placeholder="https://…">
    <label class="block text-[13px] font-semibold mb-1.5">Durée (secondes)</label>
    <input id="editDuration" name="duration_seconds" type="number" min="1" class="w-full h-10 px-3 rounded-[10px] bg-surface border border-border-strong outline-none focus-ring text-[14px] mb-5">
    <div class="flex justify-end gap-2">
      <button type="button" class="btn btn-secondary" onclick="document.getElementById('editModal').hidden=true">Annuler</button>
      <button type="submit" class="btn btn-primary">Enregistrer</button>
    </div>
  </form>
</div>

{{-- Modifier la matière (modal) --}}
<div id="editCourseModal" hidden class="fixed inset-0 z-[60] flex items-end sm:items-center justify-center p-0 sm:p-6">
  <div class="absolute inset-0 bg-black/45" onclick="document.getElementById('editCourseModal').hidden=true"></div>
  <form method="POST" action="{{ route('space.prof.course.update', $course) }}" class="relative card shadow-soft w-full sm:max-w-[480px] rounded-b-none sm:rounded-2xl p-6">
    @csrf @method('PATCH')
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-[17px] font-bold">Modifier la matière</h3>
      <button type="button" class="btn btn-ghost btn-icon btn-sm" onclick="document.getElementById('editCourseModal').hidden=true">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-5 h-5"><path d="M6 6l12 12M18 6L6 18" stroke-width="2" stroke-linecap="round"/></svg>
      </button>
    </div>
    <label class="block text-[13px] font-semibold mb-1.5">Titre</label>
    <input name="title" value="{{ $course->title }}" required class="w-full h-10 px-3 rounded-[10px] bg-surface border border-border-strong outline-none focus-ring text-[14px] mb-4">
    <label class="block text-[13px] font-semibold mb-1.5">Description <span class="font-normal text-faint">(optionnel)</span></label>
    <textarea name="description" rows="2" class="w-full px-3 py-2 rounded-[10px] bg-surface border border-border-strong outline-none focus-ring text-[14px] mb-5 resize-none">{{ $course->description }}</textarea>
    <div class="flex justify-end gap-2">
      <button type="button" class="btn btn-secondary" onclick="document.getElementById('editCourseModal').hidden=true">Annuler</button>
      <button type="submit" class="btn btn-primary">Enregistrer</button>
    </div>
  </form>
</div>

{{-- Créer un module (chapter) modal --}}
<div id="chapterModal" hidden class="fixed inset-0 z-[60] flex items-end sm:items-center justify-center p-0 sm:p-6">
  <div class="absolute inset-0 bg-black/45" onclick="document.getElementById('chapterModal').hidden=true"></div>
  <form method="POST" action="{{ route('space.prof.chapters.store', $course) }}" class="relative card shadow-soft w-full sm:max-w-[420px] rounded-b-none sm:rounded-2xl p-6">
    @csrf
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-[17px] font-bold">Créer un module</h3>
      <button type="button" class="btn btn-ghost btn-icon btn-sm" onclick="document.getElementById('chapterModal').hidden=true">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-5 h-5"><path d="M6 6l12 12M18 6L6 18" stroke-width="2" stroke-linecap="round"/></svg>
      </button>
    </div>
    <label class="block text-[13px] font-semibold mb-1.5">Nom du module</label>
    <input name="title" required class="w-full h-10 px-3 rounded-[10px] bg-surface border border-border-strong outline-none focus-ring text-[14px] mb-5" placeholder="Ex. Module 1 — Fondamentaux">
    <div class="flex justify-end gap-2">
      <button type="button" class="btn btn-secondary" onclick="document.getElementById('chapterModal').hidden=true">Annuler</button>
      <button type="submit" class="btn btn-primary">Créer</button>
    </div>
  </form>
</div>

@push('scripts')
<script>
  const courseId = {{ $course->id }};

  function openAddModal(chapterId) {
    const sel = document.getElementById('chapterSelect');
    if (sel && chapterId) {
      sel.value = chapterId;
    }
    // Set the form action dynamically based on selected chapter
    updateFormAction();
    document.getElementById('addModal').hidden = false;
  }

  function closeAddModal() {
    document.getElementById('addModal').hidden = true;
  }

  function updateFormAction() {
    const sel = document.getElementById('chapterSelect');
    const chapterId = sel ? sel.value : '';
    if (chapterId) {
      document.getElementById('addForm').action = `/space/prof/courses/${courseId}/chapters/${chapterId}/contents`;
    }
  }

  document.getElementById('chapterSelect')?.addEventListener('change', updateFormAction);

  function openEditModal(id, title, videoUrl, duration) {
    document.getElementById('editForm').action = `/space/prof/courses/${courseId}/contents/${id}`;
    document.getElementById('editTitle').value = title;
    document.getElementById('editVideoUrl').value = videoUrl;
    document.getElementById('editDuration').value = duration || '';
    document.getElementById('editModal').hidden = false;
  }

  function openChapterModal() {
    document.getElementById('chapterModal').hidden = false;
  }

  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
      closeAddModal();
      document.getElementById('chapterModal').hidden = true;
      document.getElementById('editModal').hidden = true;
      document.getElementById('editCourseModal').hidden = true;
    }
  });

  // Drag & drop reorder
  let dragged = null;
  function renumber(list) {
    list.querySelectorAll(':scope > .seq-item .seq-num').forEach((n, i) => n.textContent = i + 1);
  }

  document.querySelectorAll('.seq-list').forEach(list => {
    list.addEventListener('dragstart', e => {
      const item = e.target.closest('.seq-item');
      if (!item) return;
      dragged = item;
      item.classList.add('dragging');
    });
    list.addEventListener('dragend', e => {
      const item = e.target.closest('.seq-item');
      if (item) item.classList.remove('dragging');
      list.querySelectorAll('.seq-item').forEach(i => i.classList.remove('drop-target'));
      dragged = null;
      // Persist new order
      const ids = [...list.querySelectorAll('.seq-item')].map(el => el.dataset.id);
      fetch(`/space/prof/courses/${courseId}/reorder`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
        body: JSON.stringify({ ids }),
      });
    });
    list.addEventListener('dragover', e => {
      e.preventDefault();
      if (!dragged || dragged.parentElement !== list) return;
      const after = [...list.querySelectorAll('.seq-item:not(.dragging)')].find(el => {
        const r = el.getBoundingClientRect();
        return e.clientY < r.top + r.height / 2;
      });
      list.querySelectorAll('.seq-item').forEach(i => i.classList.remove('drop-target'));
      if (after) { after.classList.add('drop-target'); list.insertBefore(dragged, after); }
      else { list.appendChild(dragged); }
      renumber(list);
    });
  });
</script>
@endpush
@endsection
