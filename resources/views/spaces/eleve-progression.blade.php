@extends('layouts.sirae')
@section('title', 'Ma progression')

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
$ringCircumference = 2 * M_PI * 52;
$ringOffset = $ringCircumference * (1 - $overallPct / 100);
@endphp

@section('body')
<div class="h-screen flex overflow-hidden">

  {{-- SIDEBAR --}}
  <aside class="sidebar w-[264px] shrink-0 h-full flex flex-col" data-sidebar>
    <div class="px-4 pt-4 pb-3">
      <div class="flex items-center gap-3 p-2">
        <span class="w-9 h-9 rounded-[11px] bg-accent grid place-items-center text-white font-extrabold text-[15px] shrink-0">S</span>
        <span class="min-w-0 flex-1">
          <span class="block text-[14.5px] font-bold leading-tight">Monto</span>
          <span class="block text-[12px] text-faint leading-tight">Espace élève</span>
        </span>
      </div>
    </div>
    <nav class="flex-1 overflow-y-auto scroll-area px-3 pb-3 space-y-5">
      <div class="space-y-0.5">
        <a href="{{ route('space.eleve') }}" class="nav-item">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 5.5A1.5 1.5 0 015.5 4H11a2 2 0 012 2v14a1.5 1.5 0 00-1.5-1.5H4zM20 5.5A1.5 1.5 0 0018.5 4H13a2 2 0 00-2 2v14a1.5 1.5 0 011.5-1.5H20z"/></svg>
          Mes cours
        </a>
        <a href="{{ route('space.eleve.progression') }}" class="nav-item active">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 3v18h18M7 14l4-4 3 3 5-6" stroke-linecap="round" stroke-linejoin="round"/></svg>
          Ma progression
        </a>
      </div>
      @if($studentClasses->isNotEmpty())
      <div>
        <p class="px-3 mb-1.5 text-[11px] font-semibold uppercase tracking-wider text-faint">{{ $studentClasses->count() > 1 ? 'Mes classes' : 'Ma classe' }}</p>
        @foreach($studentClasses as $class)
        <div class="px-3 py-2.5 rounded-xl border border-border bg-surface-2/50 mb-1">
          <div class="flex items-center gap-2.5">
            <span class="w-8 h-8 rounded-lg bg-surface border border-border grid place-items-center text-[11px] font-bold text-muted">{{ mb_strtoupper(mb_substr(preg_replace('/[^A-Za-z0-9]/', '', $class->name), 0, 2)) }}</span>
            <div class="min-w-0">
              <div class="text-[13.5px] font-semibold leading-tight truncate">{{ $class->name }}</div>
              <div class="text-[12px] text-faint leading-tight tnum">{{ $courses->count() }} matière{{ $courses->count() != 1 ? 's' : '' }}</div>
            </div>
          </div>
        </div>
        @endforeach
      </div>
      @endif
    </nav>
    <div class="p-3 border-t border-border">
      <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 p-2 rounded-xl hover:bg-surface-2 transition">
        <span class="avatar" style="background: {{ siraieColor(auth()->user()->name) }}">{{ siraieInitials(auth()->user()->name) }}</span>
        <span class="min-w-0 flex-1">
          <span class="block text-[13.5px] font-semibold leading-tight truncate">{{ auth()->user()->name }}</span>
          <span class="block text-[12px] text-faint leading-tight">Élève</span>
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
      <h1 class="text-[19px] font-bold leading-tight">Ma progression</h1>
      <div class="ml-auto flex items-center gap-2">
        <button class="btn btn-ghost btn-icon" onclick="Sirae.toggleTheme()">
          <svg data-theme-sun viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-5 h-5"><circle cx="12" cy="12" r="4.5"/><path d="M12 2v2M12 20v2M4 12H2M22 12h-2M5 5l1.5 1.5M17.5 17.5L19 19M19 5l-1.5 1.5M6.5 17.5L5 19" stroke-linecap="round"/></svg>
          <svg data-theme-moon viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-5 h-5" hidden><path d="M21 12.8A8.5 8.5 0 1111.2 3a6.6 6.6 0 009.8 9.8z" stroke-linejoin="round"/></svg>
        </button>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-ghost btn-sm text-muted">Déconnexion</button></form>
      </div>
    </header>

    <main class="flex-1 overflow-y-auto scroll-area">
      <div class="max-w-[900px] mx-auto px-5 sm:px-7 py-7 space-y-6">

        {{-- Overview strip --}}
        <div class="card p-5 flex flex-col sm:flex-row items-center gap-6">
          {{-- Ring --}}
          <div class="relative w-[108px] h-[108px] shrink-0">
            <svg viewBox="0 0 120 120" class="w-full h-full -rotate-90">
              <circle cx="60" cy="60" r="52" fill="none" stroke="var(--surface-2)" stroke-width="12"/>
              <circle cx="60" cy="60" r="52" fill="none" stroke="var(--accent)" stroke-width="12"
                      stroke-linecap="round"
                      stroke-dasharray="{{ round($ringCircumference, 1) }}"
                      stroke-dashoffset="{{ round($ringOffset, 1) }}"/>
            </svg>
            <div class="absolute inset-0 grid place-items-center">
              <div>
                <div class="text-[24px] font-extrabold leading-none tnum text-center">{{ $overallPct }}%</div>
                <div class="text-[11px] text-faint mt-0.5 text-center">global</div>
              </div>
            </div>
          </div>
          {{-- Stats --}}
          <div class="flex flex-wrap gap-x-8 gap-y-3 sm:divide-x divide-border">
            <div class="sm:pr-8">
              <div class="text-[28px] font-extrabold tnum leading-none">{{ $completedVideos }}</div>
              <div class="text-[13px] text-muted mt-1">vidéo{{ $completedVideos != 1 ? 's' : '' }} terminée{{ $completedVideos != 1 ? 's' : '' }}</div>
            </div>
            <div class="sm:px-8">
              <div class="text-[28px] font-extrabold tnum leading-none">{{ $totalVideos }}</div>
              <div class="text-[13px] text-muted mt-1">vidéo{{ $totalVideos != 1 ? 's' : '' }} au total</div>
            </div>
            <div class="sm:pl-8">
              <div class="text-[28px] font-extrabold tnum leading-none">{{ $courses->count() }}</div>
              <div class="text-[13px] text-muted mt-1">matière{{ $courses->count() != 1 ? 's' : '' }}</div>
            </div>
          </div>
        </div>

        {{-- Per-course breakdown --}}
        @forelse($courses as $course)
        <section class="card overflow-hidden">

          {{-- Course header --}}
          <div class="px-5 py-4 border-b border-border">
            <div class="flex items-start gap-3 mb-3">
              <span class="w-9 h-9 rounded-xl grid place-items-center shrink-0 mt-0.5" style="background: {{ $course->progress_pct >= 100 ? 'var(--success-soft)' : ($course->progress_pct > 0 ? 'var(--accent-soft)' : 'var(--surface-2)') }}; color: {{ $course->progress_pct >= 100 ? 'var(--success)' : ($course->progress_pct > 0 ? 'var(--accent-ink)' : 'var(--muted)') }}">
                @if($course->progress_pct >= 100)
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-4 h-4" stroke-width="2.2"><path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                @else
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-4 h-4" stroke-width="1.8"><path d="M4 5.5A1.5 1.5 0 015.5 4H11a2 2 0 012 2v14a1.5 1.5 0 00-1.5-1.5H4zM20 5.5A1.5 1.5 0 0018.5 4H13a2 2 0 00-2 2v14a1.5 1.5 0 011.5-1.5H20z"/></svg>
                @endif
              </span>
              <div class="flex-1 min-w-0">
                <h3 class="font-bold text-[15.5px] leading-tight">{{ $course->title }}</h3>
                @if($course->teacher)
                  <div class="text-[12.5px] text-muted mt-0.5">{{ $course->teacher->name }}</div>
                @endif
              </div>
              <div class="text-right shrink-0">
                <div class="text-[19px] font-extrabold tnum leading-none {{ $course->progress_pct >= 100 ? 'text-success' : '' }}">{{ $course->progress_pct }}%</div>
                <div class="text-[12px] text-faint tnum mt-0.5">{{ $course->completed_videos }}/{{ $course->total_videos }}</div>
              </div>
            </div>
            <div class="track {{ $course->progress_pct >= 100 ? 'is-done' : '' }}">
              <i style="width: {{ $course->progress_pct }}%"></i>
            </div>
          </div>

          {{-- Chapters --}}
          @if($course->chapters->isNotEmpty())
          <div class="divide-y divide-border">
            @foreach($course->chapters as $chapter)
            <div class="px-5 py-3.5">
              <div class="flex items-center gap-3 mb-2">
                <span class="text-[13.5px] font-semibold flex-1 truncate">{{ $chapter->title }}</span>
                @if($chapter->total_videos > 0)
                  @if($chapter->progress_pct >= 100)
                    <span class="badge badge-success shrink-0">Terminé</span>
                  @else
                    <span class="text-[12.5px] text-muted tnum shrink-0">{{ $chapter->completed_videos }}/{{ $chapter->total_videos }} vidéo{{ $chapter->total_videos != 1 ? 's' : '' }}</span>
                  @endif
                @else
                  <span class="text-[12px] text-faint shrink-0">Aucune vidéo</span>
                @endif
              </div>
              @if($chapter->total_videos > 0)
              <div class="track h-[5px] {{ $chapter->progress_pct >= 100 ? 'is-done' : '' }}">
                <i style="width: {{ $chapter->progress_pct }}%"></i>
              </div>
              @endif

              {{-- Individual video rows --}}
              @if($chapter->contents->isNotEmpty())
              <div class="mt-2.5 space-y-1.5">
                @foreach($chapter->contents as $content)
                @php
                  $prog = $progressRecords[$content->id] ?? null;
                  $isDone = $prog?->is_completed ?? false;
                  $inProgress = $prog && !$isDone && ($prog->progress_seconds ?? 0) > 0;
                  $watchedPct = ($content->duration_seconds && $prog?->progress_seconds)
                    ? min(100, (int) round($prog->progress_seconds / $content->duration_seconds * 100))
                    : 0;
                @endphp
                <a href="{{ route('space.eleve.player', ['course' => $course->id, 'content' => $content->id]) }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-surface-2 transition group">
                  {{-- Status icon --}}
                  <span class="w-6 h-6 rounded-full shrink-0 flex items-center justify-center border
                    {{ $isDone ? 'bg-success-soft border-success/30' : ($inProgress ? 'bg-accent-soft border-accent/30' : 'bg-surface-2 border-border') }}">
                    @if($isDone)
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-3.5 h-3.5 text-success" stroke-width="2.5"><path d="M5 13l4 4L19 7" stroke-linecap="round"/></svg>
                    @elseif($inProgress)
                      <svg viewBox="0 0 24 24" fill="currentColor" class="w-3 h-3 text-accent ml-0.5"><path d="M8 5v14l11-7z"/></svg>
                    @else
                      <svg viewBox="0 0 24 24" fill="currentColor" class="w-3 h-3 text-faint ml-0.5"><path d="M8 5v14l11-7z"/></svg>
                    @endif
                  </span>
                  {{-- Title --}}
                  <span class="flex-1 text-[13.5px] truncate {{ $isDone ? 'text-muted' : '' }} group-hover:text-accent-ink transition">
                    {{ $content->title ?: 'Vidéo sans titre' }}
                  </span>
                  {{-- Duration / progress --}}
                  @if($content->duration_seconds)
                    <span class="text-[12px] text-faint tnum shrink-0">
                      @if($inProgress)
                        {{ gmdate('i:s', $prog->progress_seconds) }} / {{ gmdate('i:s', $content->duration_seconds) }}
                      @else
                        {{ ceil($content->duration_seconds / 60) }} min
                      @endif
                    </span>
                  @endif
                </a>
                @endforeach
              </div>
              @endif
            </div>
            @endforeach
          </div>
          @endif
        </section>
        @empty
        <div class="card p-10 text-center">
          <p class="text-muted text-[14px]">Aucune matière assignée à votre classe pour le moment.</p>
        </div>
        @endforelse

      </div>
    </main>
  </div>
</div>
@endsection
