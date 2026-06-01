@extends('layouts.sirae')
@section('title', 'Matières')

@section('body')
<div class="h-screen flex overflow-hidden">
  @include('partials.admin-sidebar', ['activeNav' => 'courses'])

  <div class="flex-1 flex flex-col min-w-0 h-full">
    <header class="shrink-0 h-[68px] border-b border-border topbar-blur flex items-center gap-3 px-5 sm:px-7">
      <button class="btn btn-ghost btn-icon btn-sm burger -ml-1" onclick="Sirae.toggleSidebar()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-5 h-5"><path d="M4 6h16M4 12h16M4 18h16" stroke-width="2" stroke-linecap="round"/></svg>
      </button>
      <h1 class="text-[19px] font-bold leading-tight">Matières</h1>
      <div class="ml-auto flex items-center gap-2">
        <button onclick="document.getElementById('createModal').hidden=false" class="btn btn-primary">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 5v14M5 12h14" stroke-width="2.2" stroke-linecap="round"/></svg>
          Nouvelle matière
        </button>
        <button class="btn btn-ghost btn-icon" onclick="Sirae.toggleTheme()">
          <svg data-theme-sun viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-5 h-5"><circle cx="12" cy="12" r="4.5"/><path d="M12 2v2M12 20v2M4 12H2M22 12h-2M5 5l1.5 1.5M17.5 17.5L19 19M19 5l-1.5 1.5M6.5 17.5L5 19" stroke-linecap="round"/></svg>
          <svg data-theme-moon viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-5 h-5" hidden><path d="M21 12.8A8.5 8.5 0 1111.2 3a6.6 6.6 0 009.8 9.8z" stroke-linejoin="round"/></svg>
        </button>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-ghost btn-sm text-muted">Déconnexion</button></form>
      </div>
    </header>

    <main class="flex-1 overflow-y-auto scroll-area">
      <div class="max-w-[1200px] mx-auto px-5 sm:px-7 py-7">

        @if(session('status'))
          <div class="card p-3 mb-5 bg-success-soft text-success text-[13.5px] font-medium border-0">{{ session('status') }}</div>
        @endif

        <section class="card overflow-hidden">
          <div class="flex items-center gap-3 px-5 py-4 border-b border-border">
            <h3 class="text-[15.5px] font-bold">Toutes les matières</h3>
            <span class="badge">{{ $courses->total() }}</span>
          </div>
          @if($courses->isEmpty())
            <div class="px-5 py-12 text-center text-muted text-[14px]">Aucune matière pour le moment.</div>
          @else
          <div class="overflow-x-auto scroll-area">
            <table class="tbl min-w-[640px]">
              <thead>
                <tr><th>Matière</th><th>Formateur</th><th>Classe</th><th class="text-right">Vidéos</th><th class="text-right">Créée le</th></tr>
              </thead>
              <tbody>
                @foreach($courses as $course)
                <tr>
                  <td>
                    <a href="{{ route('space.admin') }}" class="font-semibold hover:text-accent-ink">{{ $course->title }}</a>
                    @if($course->description)
                      <div class="text-[12px] text-faint truncate max-w-[220px]">{{ $course->description }}</div>
                    @endif
                  </td>
                  <td class="text-[13.5px]">{{ $course->teacher?->name ?? '—' }}</td>
                  <td>
                    @if($course->schoolClass)
                      <span class="badge">{{ $course->schoolClass->name }}</span>
                    @else <span class="text-faint">—</span> @endif
                  </td>
                  <td class="text-right tnum text-muted text-[13.5px]">{{ $course->video_count }}</td>
                  <td class="text-right tnum text-faint text-[12.5px]">{{ $course->created_at->format('d/m/Y') }}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          @if($courses->hasPages())
            <div class="px-5 py-3 border-t border-border">{{ $courses->links() }}</div>
          @endif
          @endif
        </section>

      </div>
    </main>
  </div>
</div>

{{-- Créer une matière modal --}}
<div id="createModal" hidden class="fixed inset-0 z-[60] flex items-end sm:items-center justify-center p-0 sm:p-6">
  <div class="absolute inset-0 bg-black/45" onclick="document.getElementById('createModal').hidden=true"></div>
  <form method="POST" action="{{ route('space.admin.courses.store') }}" class="relative card shadow-soft w-full sm:max-w-[500px] rounded-b-none sm:rounded-2xl p-6">
    @csrf
    <div class="flex items-center justify-between mb-5">
      <h3 class="text-[17px] font-bold">Nouvelle matière</h3>
      <button type="button" class="btn btn-ghost btn-icon btn-sm" onclick="document.getElementById('createModal').hidden=true">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-5 h-5"><path d="M6 6l12 12M18 6L6 18" stroke-width="2" stroke-linecap="round"/></svg>
      </button>
    </div>
    <label class="block text-[13px] font-semibold mb-1.5">Titre</label>
    <input name="title" required class="w-full h-10 px-3 rounded-[10px] bg-surface border border-border-strong outline-none focus-ring text-[14px] mb-4" placeholder="Ex. Mathématiques — 5A">
    @error('title')<p class="text-[12px] text-red-500 -mt-3 mb-3">{{ $message }}</p>@enderror

    <div class="grid grid-cols-2 gap-3 mb-4">
      <div>
        <label class="block text-[13px] font-semibold mb-1.5">Formateur</label>
        <select name="teacher_id" required class="w-full h-10 px-3 rounded-[10px] bg-surface border border-border-strong outline-none focus-ring text-[14px]">
          <option value="">Choisir…</option>
          @foreach($teachers as $t)
            <option value="{{ $t->id }}">{{ $t->name }}</option>
          @endforeach
        </select>
        @error('teacher_id')<p class="text-[12px] text-red-500 mt-1">{{ $message }}</p>@enderror
      </div>
      <div>
        <label class="block text-[13px] font-semibold mb-1.5">Classe</label>
        <select name="school_class_id" required class="w-full h-10 px-3 rounded-[10px] bg-surface border border-border-strong outline-none focus-ring text-[14px]">
          <option value="">Choisir…</option>
          @foreach($allClasses as $c)
            <option value="{{ $c->id }}">{{ $c->name }}</option>
          @endforeach
        </select>
        @error('school_class_id')<p class="text-[12px] text-red-500 mt-1">{{ $message }}</p>@enderror
      </div>
    </div>
    <label class="block text-[13px] font-semibold mb-1.5">Description <span class="font-normal text-faint">(optionnel)</span></label>
    <textarea name="description" rows="2" class="w-full px-3 py-2 rounded-[10px] bg-surface border border-border-strong outline-none focus-ring text-[14px] mb-5 resize-none"></textarea>
    <div class="flex justify-end gap-2">
      <button type="button" class="btn btn-secondary" onclick="document.getElementById('createModal').hidden=true">Annuler</button>
      <button type="submit" class="btn btn-primary">Créer</button>
    </div>
  </form>
</div>
@endsection
