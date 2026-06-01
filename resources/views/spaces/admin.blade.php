@extends('layouts.sirae')
@section('title', 'Vue d\'ensemble')

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

  @include('partials.admin-sidebar', ['activeNav' => 'overview'])

  {{-- MAIN --}}
  <div class="flex-1 flex flex-col min-w-0 h-full">

    <header class="shrink-0 h-[68px] border-b border-border topbar-blur flex items-center gap-3 px-5 sm:px-7">
      <button class="btn btn-ghost btn-icon btn-sm burger -ml-1" onclick="Sirae.toggleSidebar()" aria-label="Menu">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-5 h-5"><path d="M4 6h16M4 12h16M4 18h16" stroke-width="2" stroke-linecap="round"/></svg>
      </button>
      <div class="min-w-0">
        <h1 class="text-[19px] font-bold leading-tight truncate">Vue d'ensemble</h1>
        <p class="text-[12.5px] text-muted leading-tight">Sirae · Année {{ date('Y') }}–{{ date('Y') + 1 }}</p>
      </div>
      <div class="ml-auto flex items-center gap-2">
        <button class="btn btn-ghost btn-icon" onclick="Sirae.toggleTheme()" aria-label="Thème">
          <svg data-theme-sun viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-5 h-5"><circle cx="12" cy="12" r="4.5"/><path d="M12 2v2M12 20v2M4 12H2M22 12h-2M5 5l1.5 1.5M17.5 17.5L19 19M19 5l-1.5 1.5M6.5 17.5L5 19" stroke-linecap="round"/></svg>
          <svg data-theme-moon viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-5 h-5" hidden><path d="M21 12.8A8.5 8.5 0 1111.2 3a6.6 6.6 0 009.8 9.8z" stroke-linejoin="round"/></svg>
        </button>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="btn btn-ghost btn-sm text-muted">Déconnexion</button>
        </form>
      </div>
    </header>

    <main class="flex-1 overflow-y-auto scroll-area">
      <div class="max-w-[1240px] mx-auto px-5 sm:px-7 py-7 space-y-7">

        {{-- Actions --}}
        <div class="flex flex-wrap items-center gap-3">
          <div class="flex-1 min-w-[200px]">
            <h2 class="text-[15px] font-semibold text-muted">Pilotage de l'établissement</h2>
          </div>
          <a href="{{ route('space.admin.courses') }}" class="btn btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 5v14M5 12h14" stroke-width="2.2" stroke-linecap="round"/></svg>
            Créer une matière
          </a>
        </div>

        {{-- KPI strip --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
          <div class="card p-5">
            <div class="flex items-center justify-between mb-3">
              <span class="w-9 h-9 rounded-[10px] bg-accent-soft text-accent-ink grid place-items-center">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-[18px] h-[18px]"><path d="M12 4L2 9l10 5 10-5-10-5z"/><path d="M6 11v4.5c0 1.4 2.7 2.5 6 2.5s6-1.1 6-2.5V11"/></svg>
              </span>
              @if($stats['students'] > 0)
                <span class="badge badge-success badge-dot">{{ $stats['students'] }}</span>
              @endif
            </div>
            <div class="text-[30px] font-extrabold leading-none tnum">{{ $stats['students'] }}</div>
            <div class="text-[13px] text-muted mt-1.5">Élèves inscrits</div>
          </div>
          <div class="card p-5">
            <div class="flex items-center justify-between mb-3">
              <span class="w-9 h-9 rounded-[10px] bg-surface-2 text-muted grid place-items-center">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-[18px] h-[18px]"><circle cx="9" cy="7" r="3.2"/><path d="M3 21v-1a5 5 0 015-5h2a5 5 0 015 5v1"/></svg>
              </span>
            </div>
            <div class="text-[30px] font-extrabold leading-none tnum">{{ $stats['teachers'] }}</div>
            <div class="text-[13px] text-muted mt-1.5">Formateurs actifs</div>
          </div>
          <div class="card p-5">
            <div class="flex items-center justify-between mb-3">
              <span class="w-9 h-9 rounded-[10px] bg-surface-2 text-muted grid place-items-center">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-[18px] h-[18px]"><path d="M4 5.5A1.5 1.5 0 015.5 4H11a2 2 0 012 2v14a1.5 1.5 0 00-1.5-1.5H4zM20 5.5A1.5 1.5 0 0018.5 4H13a2 2 0 00-2 2v14a1.5 1.5 0 011.5-1.5H20z"/></svg>
              </span>
            </div>
            <div class="text-[30px] font-extrabold leading-none tnum">{{ $stats['courses'] }}</div>
            <div class="text-[13px] text-muted mt-1.5">Matières ouvertes</div>
          </div>
          <div class="card p-5">
            <div class="flex items-center justify-between mb-3">
              <span class="w-9 h-9 rounded-[10px] bg-surface-2 text-muted grid place-items-center">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-[18px] h-[18px]"><rect x="3" y="4" width="18" height="14" rx="2"/><path d="M3 8h18M7 21h10"/></svg>
              </span>
            </div>
            <div class="text-[30px] font-extrabold leading-none tnum">{{ $stats['classes'] }}</div>
            <div class="text-[13px] text-muted mt-1.5">Classes</div>
          </div>
        </div>

        {{-- Two columns --}}
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 items-start">

          {{-- Matières table --}}
          <section class="card xl:col-span-2 overflow-hidden">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-border">
              <h3 class="text-[15.5px] font-bold">Matières</h3>
              <span class="badge">{{ $stats['courses'] }}</span>
              <div class="ml-auto">
                <button class="btn btn-ghost btn-sm">Voir tout</button>
              </div>
            </div>
            @if($courses->isEmpty())
              <div class="px-5 py-10 text-center text-muted text-[14px]">Aucune matière pour le moment.</div>
            @else
            <div class="overflow-x-auto scroll-area">
              <table class="tbl min-w-[620px]">
                <thead>
                  <tr>
                    <th>Matière</th>
                    <th>Formateur</th>
                    <th>Classe</th>
                    <th class="text-right">Vidéos</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($courses as $course)
                  <tr>
                    <td>
                      <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-[9px] grid place-items-center shrink-0 bg-accent-soft text-accent-ink">
                          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-4 h-4" stroke-width="1.9"><path d="M4 5.5A1.5 1.5 0 015.5 4H11a2 2 0 012 2v14a1.5 1.5 0 00-1.5-1.5H4z"/><path d="M20 5.5A1.5 1.5 0 0018.5 4H13a2 2 0 00-2 2v14a1.5 1.5 0 011.5-1.5H20z"/></svg>
                        </span>
                        <div>
                          <div class="font-semibold leading-tight">{{ $course->title }}</div>
                          <div class="text-[12px] text-faint leading-tight tnum">{{ $course->video_count }} vidéo{{ $course->video_count != 1 ? 's' : '' }}</div>
                        </div>
                      </div>
                    </td>
                    <td>
                      @if($course->teacher)
                        <div class="flex items-center gap-2">
                          <span class="avatar !w-7 !h-7 !text-[11px]" style="background: {{ siraieColor($course->teacher->name) }}">{{ siraieInitials($course->teacher->name) }}</span>
                          <span class="text-[13.5px]">{{ $course->teacher->name }}</span>
                        </div>
                      @else
                        <span class="text-faint text-[13px]">—</span>
                      @endif
                    </td>
                    <td>
                      @if($course->schoolClass)
                        <span class="badge">{{ $course->schoolClass->name }}</span>
                      @else
                        <span class="text-faint text-[13px]">—</span>
                      @endif
                    </td>
                    <td class="text-right tnum text-muted text-[13.5px]">{{ $course->video_count }}</td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            @endif
          </section>

          {{-- Right column --}}
          <div class="space-y-6">

            {{-- Classes --}}
            <section class="card">
              <div class="flex items-center gap-3 px-5 py-4 border-b border-border">
                <h3 class="text-[15.5px] font-bold">Classes</h3>
                <span class="badge">{{ $classes->count() }}</span>
              </div>
              @if($classes->isEmpty())
                <div class="px-5 py-6 text-center text-muted text-[13px]">Aucune classe.</div>
              @else
              <div class="divide-y divide-border">
                @foreach($classes as $class)
                <div class="flex items-center gap-3 px-5 py-3.5 hover:bg-surface-2 transition cursor-pointer">
                  @php $abbr = mb_strtoupper(mb_substr(preg_replace('/[^A-Za-z0-9]/', '', $class->name), 0, 2)); @endphp
                  <span class="w-9 h-9 rounded-[10px] bg-surface-2 border border-border grid place-items-center font-bold text-[11px] text-muted">{{ $abbr }}</span>
                  <div class="flex-1 min-w-0">
                    <div class="font-semibold text-[14px] leading-tight">{{ $class->name }}</div>
                    <div class="text-[12px] text-faint tnum">{{ $class->students_count }} élève{{ $class->students_count != 1 ? 's' : '' }} · {{ $class->courses_count }} matière{{ $class->courses_count != 1 ? 's' : '' }}</div>
                  </div>
                </div>
                @endforeach
              </div>
              @endif
            </section>

            {{-- Activité récente --}}
            <section class="card p-5">
              <h3 class="text-[15.5px] font-bold mb-4">Activité récente</h3>
              @if($recentCourses->isEmpty())
                <p class="text-[13.5px] text-muted">Aucune activité récente.</p>
              @else
              <ol class="space-y-4">
                @foreach($recentCourses as $rc)
                <li class="flex gap-3">
                  <span class="w-7 h-7 rounded-full bg-accent-soft text-accent-ink grid place-items-center shrink-0 mt-0.5">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-3.5 h-3.5"><path d="M12 5v14M5 12h14" stroke-width="2.4" stroke-linecap="round"/></svg>
                  </span>
                  <p class="text-[13.5px] leading-snug">
                    <b>{{ $rc->teacher?->name ?? 'Un formateur' }}</b> a créé <b>{{ $rc->title }}</b>
                    @if($rc->schoolClass) pour <b>{{ $rc->schoolClass->name }}</b>@endif.
                    <span class="block text-[12px] text-faint mt-0.5">{{ $rc->created_at->diffForHumans() }}</span>
                  </p>
                </li>
                @endforeach
              </ol>
              @endif
            </section>

          </div>
        </div>

      </div>
    </main>
  </div>
</div>
@endsection
