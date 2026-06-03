@extends('layouts.sirae')
@section('title', 'Mes cours')

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

// Compute the circumference offset for the SVG ring (r=52, circumference ≈ 326.7)
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
        <a href="{{ route('space.eleve') }}" class="nav-item active">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 5.5A1.5 1.5 0 015.5 4H11a2 2 0 012 2v14a1.5 1.5 0 00-1.5-1.5H4zM20 5.5A1.5 1.5 0 0018.5 4H13a2 2 0 00-2 2v14a1.5 1.5 0 011.5-1.5H20z"/></svg>
          Mes cours
        </a>
        <a href="{{ route('space.eleve.progression') }}" class="nav-item">
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
      <h1 class="text-[19px] font-bold leading-tight">Mes cours</h1>
      <div class="ml-auto flex items-center gap-2">
        <button class="btn btn-ghost btn-icon" onclick="Sirae.toggleTheme()">
          <svg data-theme-sun viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-5 h-5"><circle cx="12" cy="12" r="4.5"/><path d="M12 2v2M12 20v2M4 12H2M22 12h-2M5 5l1.5 1.5M17.5 17.5L19 19M19 5l-1.5 1.5M6.5 17.5L5 19" stroke-linecap="round"/></svg>
          <svg data-theme-moon viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-5 h-5" hidden><path d="M21 12.8A8.5 8.5 0 1111.2 3a6.6 6.6 0 009.8 9.8z" stroke-linejoin="round"/></svg>
        </button>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-ghost btn-sm text-muted">Déconnexion</button></form>
      </div>
    </header>

    <main class="flex-1 overflow-y-auto scroll-area">
      <div class="max-w-[1180px] mx-auto px-5 sm:px-7 py-7 space-y-7">

        @if(session('error'))
          <div class="card p-3 border-red-300 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 text-[13.5px]">{{ session('error') }}</div>
        @endif

        {{-- Greeting --}}
        <div>
          @php $firstName = explode(' ', auth()->user()->name)[0]; @endphp
          <h2 class="text-[26px] font-extrabold leading-tight">Bonjour, {{ $firstName }} 👋</h2>
          @if($completedVideos > 0)
            <p class="text-[14px] text-muted mt-1">Vous avez terminé <b class="text-ink">{{ $completedVideos }} vidéo{{ $completedVideos != 1 ? 's' : '' }}</b> au total. Continuez sur votre lancée.</p>
          @else
            <p class="text-[14px] text-muted mt-1">Commencez votre première vidéo pour suivre votre progression.</p>
          @endif
        </div>

        {{-- Resume + progression ring --}}
        @if($courses->isNotEmpty())
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-stretch">

          {{-- Reprendre hero --}}
          @if($lastProgress && $lastProgress->chapterContent && $lastProgress->chapterContent->chapter && $lastProgress->chapterContent->chapter->course)
          @php
            $lc = $lastProgress->chapterContent;
            $resCourse = $lc->chapter->course;
            $resPct = $lc->duration_seconds > 0 ? min(100, (int)round($lastProgress->progress_seconds / $lc->duration_seconds * 100)) : 0;
          @endphp
          <a href="{{ route('space.eleve.player', ['course' => $resCourse->id, 'content' => $lc->id]) }}" class="lg:col-span-2 card overflow-hidden flex flex-col sm:flex-row group">
            <div class="media-ph w-full sm:w-[260px] h-[160px] sm:h-auto shrink-0 relative">
              <span class="absolute inset-0 grid place-items-center">
                <span class="w-14 h-14 rounded-full bg-accent text-white grid place-items-center shadow-soft group-hover:scale-105 transition">
                  <svg viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 ml-0.5"><path d="M8 5v14l11-7z"/></svg>
                </span>
              </span>
            </div>
            <div class="p-5 sm:p-6 flex flex-col flex-1 min-w-0">
              <span class="badge badge-accent self-start mb-3">Reprendre</span>
              <div class="text-[12.5px] text-faint mb-1">{{ $resCourse->title }}</div>
              <h3 class="text-[18px] font-bold leading-snug">{{ $lc->title ?: 'Vidéo sans titre' }}</h3>
              @if($lc->duration_seconds && $lastProgress->progress_seconds)
                <p class="text-[13.5px] text-muted mt-1.5">
                  Vous étiez à <span class="tnum font-semibold text-ink">{{ gmdate('i:s', $lastProgress->progress_seconds) }}</span>
                  sur {{ gmdate('i:s', $lc->duration_seconds) }}.
                </p>
              @endif
              <div class="mt-auto pt-4">
                <div class="flex items-center gap-3">
                  <div class="track flex-1"><i style="width: {{ $resPct }}%"></i></div>
                  <span class="text-[13px] font-semibold tnum">{{ $resPct }}%</span>
                </div>
              </div>
            </div>
          </a>
          @else
          {{-- No last progress — show first available course --}}
          @php $firstCourse = $courses->first(); @endphp
          @if($firstCourse && $firstCourse->total_videos > 0)
          <a href="{{ route('space.eleve.course', $firstCourse) }}" class="lg:col-span-2 card overflow-hidden flex flex-col sm:flex-row group">
            <div class="media-ph w-full sm:w-[260px] h-[160px] sm:h-auto shrink-0 relative">
              <span class="absolute inset-0 grid place-items-center">
                <span class="w-14 h-14 rounded-full bg-accent text-white grid place-items-center shadow-soft group-hover:scale-105 transition">
                  <svg viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 ml-0.5"><path d="M8 5v14l11-7z"/></svg>
                </span>
              </span>
            </div>
            <div class="p-5 sm:p-6 flex flex-col flex-1 min-w-0">
              <span class="badge self-start mb-3">Commencer</span>
              <div class="text-[12.5px] text-faint mb-1">{{ $firstCourse->title }}</div>
              <h3 class="text-[18px] font-bold leading-snug">{{ $firstCourse->total_videos }} vidéo{{ $firstCourse->total_videos != 1 ? 's' : '' }} à visionner</h3>
              <div class="mt-auto pt-4">
                <div class="track"><i style="width: 0%"></i></div>
              </div>
            </div>
          </a>
          @else
          <div class="lg:col-span-2 card p-6 flex items-center justify-center text-center text-muted">
            Aucun contenu disponible pour le moment.
          </div>
          @endif
          @endif

          {{-- Progression ring --}}
          <div class="card p-6 flex flex-col items-center justify-center text-center">
            <div class="relative w-[132px] h-[132px]">
              <svg viewBox="0 0 120 120" class="w-full h-full -rotate-90">
                <circle cx="60" cy="60" r="52" fill="none" stroke="var(--surface-2)" stroke-width="12"/>
                <circle cx="60" cy="60" r="52" fill="none" stroke="var(--accent)" stroke-width="12"
                        stroke-linecap="round"
                        stroke-dasharray="{{ round($ringCircumference, 1) }}"
                        stroke-dashoffset="{{ round($ringOffset, 1) }}"/>
              </svg>
              <div class="absolute inset-0 grid place-items-center">
                <div>
                  <div class="text-[30px] font-extrabold leading-none tnum">{{ $overallPct }}%</div>
                  <div class="text-[12px] text-faint mt-0.5">du programme</div>
                </div>
              </div>
            </div>
            <p class="text-[13.5px] text-muted mt-4">
              <b class="text-ink tnum">{{ $completedVideos }}</b> / {{ $totalVideos }} vidéo{{ $totalVideos != 1 ? 's' : '' }} terminée{{ $completedVideos != 1 ? 's' : '' }}
            </p>
          </div>
        </div>
        @endif

        {{-- Mes matières --}}
        <div>
          <div class="flex items-center gap-3 mb-4">
            <h2 class="text-[17px] font-bold">Mes matières</h2>
            <span class="badge">{{ $courses->count() }}</span>
          </div>

          @if($courses->isEmpty())
            <div class="card p-10 text-center">
              <p class="text-muted text-[14px]">Aucune matière assignée à votre classe pour le moment.</p>
            </div>
          @else
          <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
            @foreach($courses as $course)
            @php
              $isDone = $course->progress_pct >= 85;
            @endphp
            <a href="{{ route('space.eleve.course', $course) }}" class="card p-5 flex flex-col hover:shadow-soft hover:-translate-y-0.5 transition group">
              <div class="flex items-start gap-3 mb-4">
                <span class="w-11 h-11 rounded-xl grid place-items-center shrink-0" style="background: {{ $course->progress_pct > 0 ? 'var(--accent-soft)' : 'var(--surface-2)' }}; color: {{ $course->progress_pct > 0 ? 'var(--accent-ink)' : 'var(--muted)' }}">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-5 h-5" stroke-width="1.8"><path d="M4 5.5A1.5 1.5 0 015.5 4H11a2 2 0 012 2v14a1.5 1.5 0 00-1.5-1.5H4zM20 5.5A1.5 1.5 0 0018.5 4H13a2 2 0 00-2 2v14a1.5 1.5 0 011.5-1.5H20z"/></svg>
                </span>
                <div class="min-w-0 flex-1">
                  <h3 class="font-bold text-[15.5px] leading-tight">{{ $course->title }}</h3>
                  @if($course->teacher)
                  <div class="flex items-center gap-1.5 mt-1.5 text-[12.5px] text-muted">
                    <span class="avatar !w-5 !h-5 !text-[9px]" style="background: {{ siraieColor($course->teacher->name) }}">{{ siraieInitials($course->teacher->name) }}</span>
                    {{ $course->teacher->name }}
                  </div>
                  @endif
                </div>
              </div>
              <div class="flex items-center justify-between text-[12.5px] mb-1.5">
                <span class="text-muted tnum">{{ $course->completed_videos }} / {{ $course->total_videos }} vidéo{{ $course->total_videos != 1 ? 's' : '' }}</span>
                <span class="font-semibold tnum">{{ $course->progress_pct }}%</span>
              </div>
              <div class="track {{ $isDone ? 'is-done' : '' }}"><i style="width: {{ $course->progress_pct }}%"></i></div>
              <div class="mt-4 flex items-center gap-1.5 text-[13.5px] font-semibold text-accent-ink group-hover:gap-2.5 transition-all">
                {{ $course->progress_pct > 0 ? 'Continuer' : 'Commencer' }}
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-4 h-4"><path d="M5 12h14M13 6l6 6-6 6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </div>
            </a>
            @endforeach
          </div>
          @endif
        </div>

      </div>
    </main>
  </div>
</div>
@endsection
