@extends('layouts.sirae')
@section('title', 'Élèves')

@section('body')
<div class="h-screen flex overflow-hidden">
  @include('partials.admin-sidebar', ['activeNav' => 'students'])

  <div class="flex-1 flex flex-col min-w-0 h-full">
    <header class="shrink-0 h-[68px] border-b border-border topbar-blur flex items-center gap-3 px-5 sm:px-7">
      <button class="btn btn-ghost btn-icon btn-sm burger -ml-1" onclick="Sirae.toggleSidebar()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-5 h-5"><path d="M4 6h16M4 12h16M4 18h16" stroke-width="2" stroke-linecap="round"/></svg>
      </button>
      <h1 class="text-[19px] font-bold leading-tight">Élèves</h1>
      <div class="ml-auto flex items-center gap-2">
        <button class="btn btn-ghost btn-icon" onclick="Sirae.toggleTheme()">
          <svg data-theme-sun viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-5 h-5"><circle cx="12" cy="12" r="4.5"/><path d="M12 2v2M12 20v2M4 12H2M22 12h-2M5 5l1.5 1.5M17.5 17.5L19 19M19 5l-1.5 1.5M6.5 17.5L5 19" stroke-linecap="round"/></svg>
          <svg data-theme-moon viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-5 h-5" hidden><path d="M21 12.8A8.5 8.5 0 1111.2 3a6.6 6.6 0 009.8 9.8z" stroke-linejoin="round"/></svg>
        </button>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-ghost btn-sm text-muted">Déconnexion</button></form>
      </div>
    </header>

    <main class="flex-1 overflow-y-auto scroll-area">
      <div class="max-w-[960px] mx-auto px-5 sm:px-7 py-7">
        <section class="card overflow-hidden">
          <div class="flex items-center gap-3 px-5 py-4 border-b border-border">
            <h3 class="text-[15.5px] font-bold">Tous les élèves</h3>
            <span class="badge">{{ $students->total() }}</span>
          </div>
          @if($students->isEmpty())
            <div class="px-5 py-12 text-center text-muted text-[14px]">Aucun élève pour le moment.</div>
          @else
          <div class="divide-y divide-border">
            @foreach($students as $student)
            @php
              $colors = ['#3f6b8a','#7a5c46','#5a6b4a','#8a4f6b','#6b5a4a','#4a6b5a','#8a6b3a'];
              $sc = $colors[abs(crc32($student->name)) % count($colors)];
              $parts = preg_split('/\s+/', trim($student->name));
              $si = mb_strtoupper(mb_substr($parts[0], 0, 1)) . (count($parts) > 1 ? mb_strtoupper(mb_substr(end($parts), 0, 1)) : '');
            @endphp
            <div class="flex items-center gap-4 px-5 py-3.5 hover:bg-surface-2 transition">
              <span class="avatar" style="background: {{ $sc }}">{{ $si }}</span>
              <div class="flex-1 min-w-0">
                <div class="font-semibold text-[14px]">{{ $student->name }}</div>
                <div class="text-[12.5px] text-faint">{{ $student->email }}</div>
              </div>
              <div class="flex flex-wrap gap-1.5 justify-end">
                @forelse($student->classes as $class)
                  <span class="badge">{{ $class->name }}</span>
                @empty
                  <span class="text-[12.5px] text-faint">Aucune classe</span>
                @endforelse
              </div>
            </div>
            @endforeach
          </div>
          @if($students->hasPages())
            <div class="px-5 py-3 border-t border-border">{{ $students->links() }}</div>
          @endif
          @endif
        </section>
      </div>
    </main>
  </div>
</div>
@endsection
