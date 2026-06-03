@extends('layouts.sirae')
@section('title', 'Mes matières')

@section('body')
<div class="h-screen flex overflow-hidden">

  {{-- SIDEBAR --}}
  <aside class="sidebar w-[264px] shrink-0 h-full flex flex-col" data-sidebar>
    <div class="px-4 pt-4 pb-3">
      <div class="flex items-center gap-3 p-2">
        <span class="w-9 h-9 rounded-[11px] bg-accent grid place-items-center text-white font-extrabold text-[15px] shrink-0">S</span>
        <span class="min-w-0 flex-1">
          <span class="block text-[14.5px] font-bold leading-tight">Monto</span>
          <span class="block text-[12px] text-faint leading-tight">Espace formateur</span>
        </span>
      </div>
    </div>
    <nav class="flex-1 overflow-y-auto scroll-area px-3 pb-3 space-y-5">
      <div class="space-y-0.5">
        <a href="{{ route('space.prof') }}" class="nav-item active">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 5.5A1.5 1.5 0 015.5 4H11a2 2 0 012 2v14a1.5 1.5 0 00-1.5-1.5H4zM20 5.5A1.5 1.5 0 0018.5 4H13a2 2 0 00-2 2v14a1.5 1.5 0 011.5-1.5H20z"/></svg>
          Mes matières <span class="ml-auto badge">{{ $courses->count() }}</span>
        </a>
        <a href="{{ route('profile.edit') }}" class="nav-item">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
          Profil
        </a>
      </div>
    </nav>
    <div class="p-3 border-t border-border">
      <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 p-2 rounded-xl hover:bg-surface-2 transition">
        <span class="avatar" style="background:#3f6b8a">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 2)) }}</span>
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
      <h1 class="text-[19px] font-bold leading-tight">Mes matières</h1>
      <div class="ml-auto flex items-center gap-2">
        <button class="btn btn-primary" onclick="document.getElementById('createModal').hidden=false">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 5v14M5 12h14" stroke-width="2.2" stroke-linecap="round"/></svg>
          Créer une matière
        </button>
        <button class="btn btn-ghost btn-icon" onclick="Sirae.toggleTheme()">
          <svg data-theme-sun viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-5 h-5"><circle cx="12" cy="12" r="4.5"/><path d="M12 2v2M12 20v2M4 12H2M22 12h-2M5 5l1.5 1.5M17.5 17.5L19 19M19 5l-1.5 1.5M6.5 17.5L5 19" stroke-linecap="round"/></svg>
          <svg data-theme-moon viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-5 h-5" hidden><path d="M21 12.8A8.5 8.5 0 1111.2 3a6.6 6.6 0 009.8 9.8z" stroke-linejoin="round"/></svg>
        </button>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-ghost btn-sm text-muted">Déconnexion</button></form>
      </div>
    </header>

    <main class="flex-1 overflow-y-auto scroll-area">
      <div class="max-w-[860px] mx-auto px-5 sm:px-7 py-12">

        @if(session('status'))
          <div class="card p-4 mb-6 border-success text-success text-[14px] font-medium">{{ session('status') }}</div>
        @endif

        <div class="text-center py-12">
          <div class="w-16 h-16 rounded-2xl bg-accent-soft text-accent-ink grid place-items-center mx-auto mb-5">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-8 h-8" stroke-width="1.5"><path d="M4 5.5A1.5 1.5 0 015.5 4H11a2 2 0 012 2v14a1.5 1.5 0 00-1.5-1.5H4zM20 5.5A1.5 1.5 0 0018.5 4H13a2 2 0 00-2 2v14a1.5 1.5 0 011.5-1.5H20z"/></svg>
          </div>
          <h2 class="text-[22px] font-extrabold mb-2">Aucune matière pour le moment</h2>
          <p class="text-muted text-[14px] mb-6 max-w-[42ch] mx-auto leading-relaxed">Créez votre première matière pour commencer à organiser vos vidéos en séquence pédagogique.</p>
          <button onclick="document.getElementById('createModal').hidden=false" class="btn btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 5v14M5 12h14" stroke-width="2.2" stroke-linecap="round"/></svg>
            Créer une matière
          </button>
        </div>

      </div>
    </main>
  </div>
</div>

{{-- Créer matière modal --}}
<div id="createModal" hidden class="fixed inset-0 z-[60] flex items-end sm:items-center justify-center p-0 sm:p-6">
  <div class="absolute inset-0 bg-black/45" onclick="document.getElementById('createModal').hidden=true"></div>
  <form method="POST" action="{{ route('space.prof.courses.store') }}" class="relative card shadow-soft w-full sm:max-w-[480px] rounded-b-none sm:rounded-2xl p-6">
    @csrf
    <div class="flex items-center justify-between mb-5">
      <h3 class="text-[17px] font-bold">Créer une matière</h3>
      <button type="button" class="btn btn-ghost btn-icon btn-sm" onclick="document.getElementById('createModal').hidden=true">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-5 h-5"><path d="M6 6l12 12M18 6L6 18" stroke-width="2" stroke-linecap="round"/></svg>
      </button>
    </div>
    <label class="block text-[13px] font-semibold mb-1.5">Titre</label>
    <input name="title" required class="w-full h-10 px-3 rounded-[10px] bg-surface border border-border-strong outline-none focus-ring text-[14px] mb-4" placeholder="Ex. Développement Web">
    @error('title')<p class="text-[12px] text-red-500 -mt-3 mb-3">{{ $message }}</p>@enderror
    <label class="block text-[13px] font-semibold mb-1.5">Classe</label>
    <select name="school_class_id" required class="w-full h-10 px-3 rounded-[10px] bg-surface border border-border-strong outline-none focus-ring text-[14px] mb-4">
      <option value="">Choisir une classe…</option>
      @foreach($classes as $class)
        <option value="{{ $class->id }}">{{ $class->name }}</option>
      @endforeach
    </select>
    @error('school_class_id')<p class="text-[12px] text-red-500 -mt-3 mb-3">{{ $message }}</p>@enderror
    <label class="block text-[13px] font-semibold mb-1.5">Description <span class="font-normal text-faint">(optionnel)</span></label>
    <textarea name="description" rows="2" class="w-full px-3 py-2 rounded-[10px] bg-surface border border-border-strong outline-none focus-ring text-[14px] mb-5 resize-none" placeholder="Objectifs, contenus…"></textarea>
    <div class="flex justify-end gap-2">
      <button type="button" class="btn btn-secondary" onclick="document.getElementById('createModal').hidden=true">Annuler</button>
      <button type="submit" class="btn btn-primary">Créer</button>
    </div>
  </form>
</div>
@endsection
