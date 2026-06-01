@extends('layouts.sirae')
@section('title', $content->title ?: 'Lecteur')

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

// Build flat ordered list of video contents for JS
$playlistData = [];
foreach ($course->chapters as $chapter) {
    foreach ($chapter->contents->where('content_type', 'video') as $c) {
        $prog = $progressRecords[$c->id] ?? null;
        $playlistData[] = [
            'id'               => $c->id,
            'title'            => $c->title ?: 'Vidéo sans titre',
            'chapter_title'    => $chapter->title,
            'duration_seconds' => $c->duration_seconds,
            'video_url'        => $c->video_url,
            'is_completed'     => $prog?->is_completed ?? false,
            'progress_seconds' => $prog?->progress_seconds ?? 0,
        ];
    }
}
$currentIndex = array_search($content->id, array_column($playlistData, 'id'));
if ($currentIndex === false) $currentIndex = 0;

$currentProg = $progressRecords[$content->id] ?? null;
$videoPct = ($content->duration_seconds && $currentProg?->progress_seconds)
    ? min(100, (int)round($currentProg->progress_seconds / $content->duration_seconds * 100))
    : 0;
@endphp

@section('body')
<div class="h-screen flex flex-col overflow-hidden">

  {{-- Slim topbar --}}
  <header class="shrink-0 h-[60px] border-b border-border bg-surface flex items-center gap-3 px-4 sm:px-6">
    <a href="{{ route('space.eleve') }}" class="btn btn-ghost btn-sm">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M19 12H5M11 6l-6 6 6 6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
      Mes cours
    </a>
    <div class="h-5 w-px bg-border mx-1"></div>
    <div class="min-w-0">
      <div class="text-[12px] text-faint leading-tight">{{ $course->title }}</div>
      <div class="text-[14.5px] font-bold leading-tight truncate">{{ $content->chapter?->title ?? 'Chapitre' }}</div>
    </div>
    <div class="ml-auto flex items-center gap-2">
      <div class="hidden sm:flex items-center gap-2 mr-1">
        <span class="text-[12.5px] text-muted">Cours</span>
        <div class="track w-28"><i id="courseBar" style="width: {{ $coursePct }}%"></i></div>
        <span class="text-[12.5px] font-semibold tnum" id="coursePct">{{ $coursePct }}%</span>
      </div>
      <button class="btn btn-ghost btn-icon" onclick="Sirae.toggleTheme()">
        <svg data-theme-sun viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-5 h-5"><circle cx="12" cy="12" r="4.5"/><path d="M12 2v2M12 20v2M4 12H2M22 12h-2M5 5l1.5 1.5M17.5 17.5L19 19M19 5l-1.5 1.5M6.5 17.5L5 19" stroke-linecap="round"/></svg>
        <svg data-theme-moon viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-5 h-5" hidden><path d="M21 12.8A8.5 8.5 0 1111.2 3a6.6 6.6 0 009.8 9.8z" stroke-linejoin="round"/></svg>
      </button>
    </div>
  </header>

  <div class="flex-1 flex overflow-hidden">

    {{-- Player column --}}
    <main class="flex-1 overflow-y-auto scroll-area min-w-0">
      <div class="max-w-[860px] mx-auto px-4 sm:px-6 py-6">

        {{-- Native video --}}
        <div class="card overflow-hidden bg-black">
          <video id="player" class="w-full block aspect-video bg-black" controls preload="metadata"
                 src="{{ $content->video_url ?? '' }}">
            Votre navigateur ne prend pas en charge la lecture vidéo.
          </video>
        </div>

        {{-- Title + actions --}}
        <div class="mt-5 flex flex-wrap items-start gap-4">
          <div class="flex-1 min-w-[220px]">
            <div class="text-[12.5px] text-faint mb-1" id="vidEyebrow">
              {{ $content->chapter?->title ?? '' }} · Vidéo {{ $currentIndex + 1 }} sur {{ count($playlistData) }}
            </div>
            <h1 class="text-[21px] font-extrabold leading-tight" id="vidTitle">{{ $content->title ?: 'Vidéo sans titre' }}</h1>
            @if($course->teacher)
            <div class="flex items-center gap-2 mt-2.5 text-[13px] text-muted">
              <span class="avatar !w-6 !h-6 !text-[10px]" style="background: {{ siraieColor($course->teacher->name) }}">{{ siraieInitials($course->teacher->name) }}</span>
              {{ $course->teacher->name }}
              @if($content->duration_seconds)
                <span class="text-faint">·</span>
                <span class="tnum" id="vidDur">{{ gmdate('i:s', $content->duration_seconds) }}</span>
              @endif
            </div>
            @endif
          </div>
          <div class="flex items-center gap-2">
            <button class="btn btn-secondary btn-icon" id="prevBtn" onclick="navRel(-1)" aria-label="Précédent">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M19 12H5M11 6l-6 6 6 6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <button class="btn btn-primary" id="completeBtn" onclick="markComplete()">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M20 6L9 17l-5-5" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
              <span id="completeBtnLabel">Marquer terminé</span>
            </button>
            <button class="btn btn-secondary" id="nextBtn" onclick="navRel(1)">
              Suivant
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M5 12h14M13 6l6 6-6 6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
          </div>
        </div>

        {{-- Live progress --}}
        <div class="card p-4 mt-5">
          <div class="flex items-center justify-between text-[13px] mb-2">
            <span class="text-muted">Progression de cette vidéo</span>
            <span class="font-semibold tnum" id="vidPct">{{ $videoPct }}%</span>
          </div>
          <div class="track"><i id="vidBar" style="width: {{ $videoPct }}%"></i></div>
          <div class="flex items-center justify-between text-[12px] text-faint tnum mt-2">
            <span id="curTime">{{ $currentProg ? gmdate('i:s', $currentProg->progress_seconds) : '00:00' }}</span>
            <span id="totTime">{{ $content->duration_seconds ? gmdate('i:s', $content->duration_seconds) : '--:--' }}</span>
          </div>
        </div>

        {{-- Description --}}
        @if($content->content && $content->content_type !== 'video')
        <div class="card p-5 mt-5">
          <h3 class="text-[14px] font-bold mb-2">Description</h3>
          <div class="text-[14px] text-muted leading-relaxed">{!! nl2br(e($content->content)) !!}</div>
        </div>
        @endif

      </div>
    </main>

    {{-- Playlist --}}
    <aside class="w-[340px] shrink-0 border-l border-border bg-surface hidden lg:flex flex-col h-full">
      <div class="shrink-0 px-5 py-4 border-b border-border">
        <h2 class="text-[15px] font-bold">Contenu du cours</h2>
        <p class="text-[12.5px] text-faint mt-0.5 tnum">
          <span id="doneCount">{{ $completedVideos }}</span> / {{ $totalVideos }} vidéo{{ $totalVideos != 1 ? 's' : '' }}
        </p>
      </div>
      <div class="flex-1 overflow-y-auto scroll-area py-2" id="playlist">
        @foreach($course->chapters as $chapter)
          @php
            $chapterVideos = $chapter->contents->where('content_type', 'video');
            $chapterDone = $chapterVideos->filter(fn($c) => isset($progressRecords[$c->id]) && $progressRecords[$c->id]->is_completed)->count();
          @endphp
          @if($chapterVideos->isNotEmpty())
          <div class="px-5 pt-3 pb-1.5 flex items-center gap-2">
            <span class="text-[12px] font-bold uppercase tracking-wide text-faint">{{ $chapter->title }}</span>
            <span class="ml-auto text-[11.5px] text-faint tnum">{{ $chapterDone }}/{{ $chapterVideos->count() }}</span>
          </div>
          @foreach($chapterVideos as $ci => $cv)
          @php
            $cprog = $progressRecords[$cv->id] ?? null;
            $isActive = $cv->id === $content->id;
            $isDone   = $cprog?->is_completed ?? false;
          @endphp
          <div class="lesson {{ $isActive ? 'active' : ($isDone ? 'done' : '') }} px-5 py-3" data-id="{{ $cv->id }}">
            <button class="w-full text-left flex gap-3 items-center" onclick="navigateTo({{ $cv->id }})">
              <span class="stat w-6 h-6 rounded-full border border-border grid place-items-center text-[11px] font-bold text-muted shrink-0 {{ $isActive ? 'bg-accent text-white border-transparent' : '' }}">
                @if($isActive)
                  <svg viewBox="0 0 24 24" fill="currentColor" class="w-3 h-3 ml-0.5"><path d="M8 5v14l11-7z"/></svg>
                @elseif($isDone)
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-3.5 h-3.5"><path d="M20 6L9 17l-5-5" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                @else
                  {{ $ci + 1 }}
                @endif
              </span>
              <span class="flex-1 min-w-0">
                <span class="block text-[13.5px] {{ $isActive ? 'font-semibold text-accent-ink' : 'font-medium' }} leading-snug truncate">{{ $cv->title ?: 'Vidéo sans titre' }}</span>
                <span class="block text-[12px] {{ $isActive ? 'text-accent-ink/70' : 'text-faint' }} tnum mt-0.5">
                  @if($isActive) En cours ·
                  @elseif($isDone) Terminé ·
                  @endif
                  {{ $cv->duration_seconds ? gmdate('i:s', $cv->duration_seconds) : 'Vidéo' }}
                </span>
              </span>
            </button>
            @if($isActive && $cprog?->progress_seconds && $cv->duration_seconds)
            <div class="track mt-2.5 ml-9" style="background:rgba(255,255,255,.25)">
              <i class="lesson-bar" style="width: {{ min(100, (int)round($cprog->progress_seconds / $cv->duration_seconds * 100)) }}%"></i>
            </div>
            @endif
          </div>
          @endforeach
          @endif
        @endforeach
      </div>
      <div class="shrink-0 px-5 py-3 border-t border-border flex items-center gap-2 text-[12px] text-faint">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-4 h-4"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Reprise automatique à votre dernière position
      </div>
    </aside>

  </div>
</div>

@push('scripts')
<script>
const COURSE_ID   = {{ $course->id }};
const CONTENT_ID  = {{ $content->id }};
const CSRF_TOKEN  = document.querySelector('meta[name=csrf-token]').content;
const PLAYLIST    = @json($playlistData);
const TOTAL       = {{ $totalVideos }};

let activeIndex   = PLAYLIST.findIndex(c => c.id === CONTENT_ID);
if (activeIndex < 0) activeIndex = 0;
let completedSet  = new Set(PLAYLIST.filter(c => c.is_completed).map(c => c.id));
let saveTimer     = null;

const player      = document.getElementById('player');
const vidBar      = document.getElementById('vidBar');
const vidPct      = document.getElementById('vidPct');
const curTime     = document.getElementById('curTime');
const totTime     = document.getElementById('totTime');
const courseBar   = document.getElementById('courseBar');
const coursePctEl = document.getElementById('coursePct');
const doneCount   = document.getElementById('doneCount');

function fmt(t) {
    if (!isFinite(t) || isNaN(t)) return '--:--';
    const m = Math.floor(t / 60), s = Math.floor(t % 60);
    return (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
}

function updateCourseProgress() {
    const pct = TOTAL > 0 ? Math.round(completedSet.size / TOTAL * 100) : 0;
    if (courseBar) courseBar.style.width = pct + '%';
    if (coursePctEl) coursePctEl.textContent = pct + '%';
    if (doneCount) doneCount.textContent = completedSet.size;
}

function saveProgress(isCompleted) {
    clearTimeout(saveTimer);
    const current = PLAYLIST[activeIndex];
    if (!current) return;
    fetch('{{ route('space.eleve.progress') }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
        body: JSON.stringify({
            chapter_content_id: current.id,
            progress_seconds: Math.floor(player.currentTime || 0),
            is_completed: isCompleted ? 1 : 0,
        }),
    });
}

function scheduleSave() {
    clearTimeout(saveTimer);
    saveTimer = setTimeout(() => saveProgress(false), 5000);
}

player.addEventListener('timeupdate', function () {
    if (!player.duration) return;
    const frac = player.currentTime / player.duration;
    vidBar.style.width = Math.round(frac * 100) + '%';
    vidPct.textContent = Math.round(frac * 100) + '%';
    curTime.textContent = fmt(player.currentTime);
    scheduleSave();
});

player.addEventListener('loadedmetadata', function () {
    totTime.textContent = fmt(player.duration);
    const current = PLAYLIST[activeIndex];
    if (current && current.progress_seconds > 0) {
        player.currentTime = current.progress_seconds;
    }
});

player.addEventListener('ended', function () {
    markComplete();
});

function markComplete() {
    const current = PLAYLIST[activeIndex];
    if (!current) return;
    if (!completedSet.has(current.id)) {
        completedSet.add(current.id);
    }
    current.is_completed = true;
    current.progress_seconds = current.duration_seconds || 0;
    saveProgress(true);
    updateCourseProgress();

    // Update playlist UI for this lesson
    const lessonEl = document.querySelector('.lesson[data-id="' + current.id + '"]');
    if (lessonEl) {
        lessonEl.classList.add('done');
        const stat = lessonEl.querySelector('.stat');
        if (stat) {
            stat.className = 'stat w-6 h-6 rounded-full border border-border grid place-items-center text-[11px] font-bold text-muted shrink-0';
            stat.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-3.5 h-3.5"><path d="M20 6L9 17l-5-5" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"/></svg>';
        }
    }

    // Update complete button
    const btn = document.getElementById('completeBtnLabel');
    if (btn) btn.textContent = 'Terminé ✓';

    // Auto-advance after 1.5s
    if (activeIndex + 1 < PLAYLIST.length) {
        setTimeout(() => navigateTo(PLAYLIST[activeIndex + 1].id), 1500);
    }
}

function navRel(d) {
    const j = activeIndex + d;
    if (j >= 0 && j < PLAYLIST.length) {
        navigateTo(PLAYLIST[j].id);
    }
}

function navigateTo(contentId) {
    window.location.href = `/space/eleve/courses/${COURSE_ID}/contents/${contentId}`;
}

// Init prev/next buttons visibility
function updateNavButtons() {
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    if (prevBtn) prevBtn.disabled = activeIndex <= 0;
    if (nextBtn) nextBtn.disabled = activeIndex >= PLAYLIST.length - 1;
}
updateNavButtons();
updateCourseProgress();
</script>
@endpush
@endsection
