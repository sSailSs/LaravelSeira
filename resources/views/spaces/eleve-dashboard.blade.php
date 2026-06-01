<div class="container mx-auto px-4 py-8">
    <h1 class="text-4xl font-bold mb-8">📚 Mon Espace Étudiant</h1>

    <!-- Statistiques de l'élève -->
    <div class="grid grid-cols-3 gap-4 mb-8">
        <div class="bg-blue-500 text-white p-6 rounded-lg shadow">
            <div class="text-3xl font-bold">{{ $courses->count() }}</div>
            <div class="text-sm">Cours Accessibles</div>
        </div>
        <div class="bg-green-500 text-white p-6 rounded-lg shadow">
            <div class="text-3xl font-bold">{{ $completed_count ?? 0 }}</div>
            <div class="text-sm">Contenus Terminés</div>
        </div>
        <div class="bg-purple-500 text-white p-6 rounded-lg shadow">
            <div class="text-3xl font-bold">{{ round($completion_percentage ?? 0) }}%</div>
            <div class="text-sm">Progression Globale</div>
        </div>
    </div>

    <!-- Mes Cours -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold mb-4">🎓 Mes Cours</h2>
        
        @forelse($courses as $course)
            <div class="border rounded-lg p-4 mb-4 bg-gray-50 hover:bg-gray-100">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <h3 class="text-xl font-bold">{{ $course->title }}</h3>
                        <p class="text-gray-600">👨‍🏫 {{ $course->teacher->name ?? 'N/A' }}</p>
                        <p class="text-gray-600">📍 {{ $course->schoolClass->name ?? 'N/A' }}</p>
                        @if($course->description)
                            <p class="text-gray-700 mt-2">{{ Str::limit($course->description, 150) }}</p>
                        @endif
                        
                        <!-- Chapitres du cours -->
                        <div class="mt-4">
                            <div class="font-bold text-sm mb-2">📖 Chapitres:</div>
                            <div class="space-y-1">
                                @forelse($course->chapters as $chapter)
                                    <div class="text-sm bg-white p-2 rounded border ml-4">
                                        <span class="font-semibold">{{ $chapter->title }}</span>
                                        <div class="text-xs text-gray-600 mt-1">
                                            📹 {{ $chapter->contents()->count() }} contenus
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-xs text-gray-500 ml-4">Aucun chapitre</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Progression du cours -->
                        @php
                            $courseProgress = $progresses->where('chapterContent.chapter.course_id', $course->id);
                            $courseTotal = $course->chapters()->with('contents')->get()->sum(fn($ch) => $ch->contents->count());
                            $courseCompleted = $courseProgress->where('is_completed', true)->count();
                            $coursePercent = $courseTotal > 0 ? round(($courseCompleted / $courseTotal) * 100) : 0;
                        @endphp
                        <div class="mt-3">
                            <div class="text-xs text-gray-600 mb-1">Progression: {{ $courseCompleted }}/{{ $courseTotal }}</div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-green-500 h-3 rounded-full transition-all" 
                                     style="width: {{ $coursePercent }}%">
                                </div>
                            </div>
                            <div class="text-xs text-gray-600 mt-1">{{ $coursePercent }}% complété</div>
                        </div>
                    </div>
                    <div class="ml-4 flex flex-col gap-2">
                        <a href="#course-{{ $course->id }}" 
                           onclick="toggleCourse('course-{{ $course->id }}')"
                           class="bg-blue-500 text-white px-3 py-2 rounded text-sm hover:bg-blue-600">
                            📺 Voir Contenus
                        </a>
                    </div>
                </div>

                <!-- Contenu du cours (caché par défaut) -->
                <div id="course-{{ $course->id }}" class="hidden mt-4 pt-4 border-t">
                    <h4 class="font-bold mb-2">📺 Contenus du cours:</h4>
                    @forelse($course->chapters as $chapter)
                        <div class="ml-4 mb-3">
                            <div class="font-semibold text-sm mb-1">{{ $chapter->title }}</div>
                            @forelse($chapter->contents as $content)
                                @php
                                    $contentProgress = $progresses->firstWhere('chapter_content_id', $content->id);
                                @endphp
                                <div class="bg-white p-3 rounded border mb-2 ml-2 text-sm">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            @if($content->content_type === 'video')
                                                📹
                                            @else
                                                📄
                                            @endif
                                            {{ $content->title }}
                                            @if($content->duration_seconds)
                                                <span class="text-xs text-gray-500">({{ gmdate('i:s', $content->duration_seconds) }})</span>
                                            @endif
                                        </div>
                                        @if($contentProgress?->is_completed)
                                            <span class="bg-green-200 text-green-800 px-2 py-1 rounded text-xs">✅ Terminé</span>
                                        @elseif($contentProgress)
                                            <span class="bg-blue-200 text-blue-800 px-2 py-1 rounded text-xs">⏳ En cours</span>
                                        @else
                                            <span class="bg-gray-200 text-gray-800 px-2 py-1 rounded text-xs">⭕ Non démarré</span>
                                        @endif
                                    </div>
                                    @if($contentProgress && $content->duration_seconds)
                                        <div class="mt-2 w-full bg-gray-200 rounded-full h-1">
                                            <div class="bg-blue-500 h-1 rounded-full" 
                                                 style="width: {{ min(($contentProgress->progress_seconds / $content->duration_seconds) * 100, 100) }}%">
                                            </div>
                                        </div>
                                        <div class="text-xs text-gray-600 mt-1">
                                            {{ gmdate('i:s', min($contentProgress->progress_seconds, $content->duration_seconds)) }} / {{ gmdate('i:s', $content->duration_seconds) }}
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <p class="text-xs text-gray-500 ml-2">Aucun contenu</p>
                            @endforelse
                        </div>
                    @empty
                        <p class="text-gray-500">Aucun chapitre disponible</p>
                    @endforelse
                </div>
            </div>
        @empty
            <p class="text-gray-500 text-center py-8">Vous n'êtes enrôlé dans aucun cours</p>
        @endforelse
    </div>
</div>

<script>
    function toggleCourse(id) {
        const elem = document.getElementById(id);
        elem.classList.toggle('hidden');
    }
</script>

<style>
    .container { max-width: 1200px; }
    .grid { display: grid; }
    .grid-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    .gap-4 { gap: 1rem; }
    .gap-2 { gap: 0.5rem; }
    .mb-8 { margin-bottom: 2rem; }
    .mb-6 { margin-bottom: 1.5rem; }
    .mb-4 { margin-bottom: 1rem; }
    .mb-3 { margin-bottom: 0.75rem; }
    .mb-2 { margin-bottom: 0.5rem; }
    .mb-1 { margin-bottom: 0.25rem; }
    .mt-2 { margin-top: 0.5rem; }
    .mt-3 { margin-top: 0.75rem; }
    .mt-4 { margin-top: 1rem; }
    .mx-auto { margin-left: auto; margin-right: auto; }
    .ml-4 { margin-left: 1rem; }
    .ml-2 { margin-left: 0.5rem; }
    .pt-4 { padding-top: 1rem; }
    .px-4 { padding-left: 1rem; padding-right: 1rem; }
    .px-3 { padding-left: 0.75rem; padding-right: 0.75rem; }
    .px-2 { padding-left: 0.5rem; padding-right: 0.5rem; }
    .py-8 { padding-top: 2rem; padding-bottom: 2rem; }
    .py-6 { padding-top: 1.5rem; padding-bottom: 1.5rem; }
    .py-4 { padding-top: 1rem; padding-bottom: 1rem; }
    .py-3 { padding-top: 0.75rem; padding-bottom: 0.75rem; }
    .py-2 { padding-top: 0.5rem; padding-bottom: 0.5rem; }
    .py-1 { padding-top: 0.25rem; padding-bottom: 0.25rem; }
    .p-6 { padding: 1.5rem; }
    .p-4 { padding: 1rem; }
    .p-3 { padding: 0.75rem; }
    .p-2 { padding: 0.5rem; }
    .h-3 { height: 0.75rem; }
    .h-1 { height: 0.25rem; }
    .w-full { width: 100%; }
    .text-4xl { font-size: 2.25rem; font-weight: 700; }
    .text-3xl { font-size: 1.875rem; font-weight: 700; }
    .text-2xl { font-size: 1.5rem; font-weight: 700; }
    .text-xl { font-size: 1.25rem; font-weight: 700; }
    .text-sm { font-size: 0.875rem; }
    .text-xs { font-size: 0.75rem; }
    .font-bold { font-weight: 700; }
    .font-semibold { font-weight: 600; }
    .block { display: block; }
    .flex { display: flex; }
    .justify-between { justify-content: space-between; }
    .items-start { align-items: flex-start; }
    .items-center { align-items: center; }
    .flex-1 { flex: 1; }
    .hidden { display: none; }
    .border { border: 1px solid #d1d5db; }
    .border-t { border-top: 1px solid #d1d5db; }
    .rounded { border-radius: 0.375rem; }
    .rounded-lg { border-radius: 0.5rem; }
    .rounded-full { border-radius: 9999px; }
    .shadow { box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
    .bg-white { background-color: white; }
    .bg-gray-50 { background-color: #f9fafb; }
    .bg-gray-100 { background-color: #f3f4f6; }
    .bg-gray-200 { background-color: #e5e7eb; }
    .bg-blue-500 { background-color: #3b82f6; }
    .bg-blue-200 { background-color: #bfdbfe; }
    .bg-green-500 { background-color: #10b981; }
    .bg-green-200 { background-color: #d1fae5; }
    .bg-purple-500 { background-color: #a855f7; }
    .text-white { color: white; }
    .text-gray-500 { color: #6b7280; }
    .text-gray-600 { color: #4b5563; }
    .text-gray-700 { color: #374151; }
    .text-gray-800 { color: #1f2937; }
    .text-blue-800 { color: #1e40af; }
    .text-green-800 { color: #166534; }
    .text-center { text-align: center; }
    .hover\:bg-gray-100:hover { background-color: #f3f4f6; }
    .hover\:bg-blue-600:hover { background-color: #2563eb; }
    .transition-all { transition: all 0.3s ease; }
</style>
