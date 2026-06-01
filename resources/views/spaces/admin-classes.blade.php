@extends('layouts.sirae')
@section('title', 'Classes')

@section('body')
<div class="h-screen flex overflow-hidden">
  @include('partials.admin-sidebar', ['activeNav' => 'classes'])

  <div class="flex-1 flex flex-col min-w-0 h-full">
    <header class="shrink-0 h-[68px] border-b border-border topbar-blur flex items-center gap-3 px-5 sm:px-7">
      <button class="btn btn-ghost btn-icon btn-sm burger -ml-1" onclick="Sirae.toggleSidebar()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-5 h-5"><path d="M4 6h16M4 12h16M4 18h16" stroke-width="2" stroke-linecap="round"/></svg>
      </button>
      <h1 class="text-[19px] font-bold leading-tight">Classes</h1>
      <div class="ml-auto flex items-center gap-2">
        <button class="btn btn-ghost btn-icon" onclick="Sirae.toggleTheme()">
          <svg data-theme-sun viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-5 h-5"><circle cx="12" cy="12" r="4.5"/><path d="M12 2v2M12 20v2M4 12H2M22 12h-2M5 5l1.5 1.5M17.5 17.5L19 19M19 5l-1.5 1.5M6.5 17.5L5 19" stroke-linecap="round"/></svg>
          <svg data-theme-moon viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-5 h-5" hidden><path d="M21 12.8A8.5 8.5 0 1111.2 3a6.6 6.6 0 009.8 9.8z" stroke-linejoin="round"/></svg>
        </button>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-ghost btn-sm text-muted">Déconnexion</button></form>
      </div>
    </header>

    <main class="flex-1 overflow-y-auto scroll-area">
      <div class="max-w-[1000px] mx-auto px-5 sm:px-7 py-7">
        <section class="card overflow-hidden">
          <div class="flex items-center gap-3 px-5 py-4 border-b border-border">
            <h3 class="text-[15.5px] font-bold">Toutes les classes</h3>
            <span class="badge">{{ $classes->count() }}</span>
          </div>
          @if($classes->isEmpty())
            <div class="px-5 py-12 text-center text-muted text-[14px]">Aucune classe pour le moment.</div>
          @else
          <div class="overflow-x-auto scroll-area">
            <table class="tbl min-w-[560px]">
              <thead>
                <tr><th>Classe</th><th>Formateur référent</th><th class="text-right">Élèves</th><th class="text-right">Matières</th></tr>
              </thead>
              <tbody>
                @foreach($classes as $class)
                <tr>
                  <td>
                    <div class="flex items-center gap-3">
                      @php $abbr = mb_strtoupper(mb_substr(preg_replace('/[^A-Za-z0-9]/', '', $class->name), 0, 2)); @endphp
                      <span class="w-9 h-9 rounded-[10px] bg-surface-2 border border-border grid place-items-center font-bold text-[12px] text-muted shrink-0">{{ $abbr }}</span>
                      <div>
                        <div class="font-semibold">{{ $class->name }}</div>
                        @if($class->level) <div class="text-[12px] text-faint">{{ $class->level }}</div> @endif
                      </div>
                    </div>
                  </td>
                  <td class="text-[13.5px]">{{ $class->teacher?->name ?? '—' }}</td>
                  <td class="text-right tnum text-muted text-[13.5px]">{{ $class->students_count }}</td>
                  <td class="text-right tnum text-muted text-[13.5px]">{{ $class->courses_count }}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          @endif
        </section>
      </div>
    </main>
  </div>
</div>
@endsection
