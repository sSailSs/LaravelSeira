<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-4xl font-bold">👨‍🏫 Espace Professeur</h1>
        <button onclick="document.getElementById('createCourseForm').style.display = 'block'" 
                class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
            ➕ Créer un Cours
        </button>
    </div>

    <!-- Formulaire de création de cours -->
    <div id="createCourseForm" style="display:none;" class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-2xl font-bold mb-4">Créer un Nouveau Cours</h2>
        <form action="{{ route('space.prof.courses.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block font-bold mb-2">Titre du Cours</label>
                <input type="text" name="title" required class="w-full border rounded px-3 py-2" placeholder="Ex: Mathématiques - Algèbre">
            </div>
            <div>
                <label class="block font-bold mb-2">Description</label>
                <textarea name="description" class="w-full border rounded px-3 py-2 h-24" placeholder="Décrivez votre cours..."></textarea>
            </div>
            <div>
                <label class="block font-bold mb-2">Classe</label>
                <select name="school_class_id" required class="w-full border rounded px-3 py-2">
                    <option value="">Sélectionnez une classe</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-green-500 text-white px-6 py-2 rounded-lg hover:bg-green-600">
                    ✅ Créer
                </button>
                <button type="button" onclick="document.getElementById('createCourseForm').style.display = 'none'" 
                        class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600">
                    ❌ Annuler
                </button>
            </div>
        </form>
    </div>

    <!-- Liste des cours -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold mb-4">📚 Mes Cours ({{ $courses->count() }})</h2>
        
        @forelse($courses as $course)
            <div class="border rounded-lg p-4 mb-4 bg-gray-50 hover:bg-gray-100">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <h3 class="text-xl font-bold">{{ $course->title }}</h3>
                        <p class="text-gray-600">📍 {{ $course->schoolClass->name ?? 'N/A' }}</p>
                        @if($course->description)
                            <p class="text-gray-700 mt-2">{{ Str::limit($course->description, 100) }}</p>
                        @endif
                        <div class="mt-2 space-x-2">
                            <span class="text-sm bg-blue-200 text-blue-800 px-2 py-1 rounded">
                                📖 {{ $course->chapters()->count() }} chapitres
                            </span>
                            <span class="text-sm bg-purple-200 text-purple-800 px-2 py-1 rounded">
                                👥 {{ $course->schoolClass->students()->count() }} élèves
                            </span>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2 ml-4">
                        <a href="#" class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">
                            📝 Éditer
                        </a>
                        <a href="#" class="bg-green-500 text-white px-3 py-1 rounded text-sm hover:bg-green-600">
                            ➕ Chapitre
                        </a>
                        <a href="#" class="bg-purple-500 text-white px-3 py-1 rounded text-sm hover:bg-purple-600">
                            📊 Suivi
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-gray-500 text-center py-8">Vous n'avez pas encore créé de cours</p>
        @endforelse
    </div>

    <!-- Tableau de suivi des élèves -->
    <div class="bg-white rounded-lg shadow p-6 mt-8">
        <h2 class="text-2xl font-bold mb-4">📊 Suivi Global des Élèves</h2>
        <table class="w-full text-left text-sm">
            <thead class="border-b-2 bg-gray-100">
                <tr>
                    <th class="pb-2 px-3">Élève</th>
                    <th class="pb-2 px-3">Cours</th>
                    <th class="pb-2 px-3">Progression</th>
                    <th class="pb-2 px-3">Statut</th>
                </tr>
            </thead>
            <tbody>
                @forelse($progresses as $progress)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-3 px-3">{{ $progress->user->name }}</td>
                        <td class="py-3 px-3">{{ $progress->chapterContent->chapter->course->title ?? 'N/A' }}</td>
                        <td class="py-3 px-3">
                            <div class="w-32 bg-gray-200 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full" 
                                     style="width: {{ $progress->is_completed ? '100' : (min($progress->progress_seconds / ($progress->chapterContent->duration_seconds ?? 1) * 100, 99)) }}%">
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-3">
                            @if($progress->is_completed)
                                <span class="bg-green-200 text-green-800 px-2 py-1 rounded text-xs">✅ Terminé</span>
                            @elseif($progress->progress_seconds > 0)
                                <span class="bg-blue-200 text-blue-800 px-2 py-1 rounded text-xs">⏳ En cours</span>
                            @else
                                <span class="bg-gray-200 text-gray-800 px-2 py-1 rounded text-xs">⭕ Non démarré</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-4 text-center text-gray-500">Aucune progression</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
    .container { max-width: 1200px; }
    .flex { display: flex; }
    .justify-between { justify-content: space-between; }
    .items-center { align-items: center; }
    .items-start { align-items: flex-start; }
    .flex-col { flex-direction: column; }
    .flex-1 { flex: 1; }
    .gap-2 { gap: 0.5rem; }
    .gap-4 { gap: 1rem; }
    .space-y-4 > * + * { margin-top: 1rem; }
    .space-y-2 > * + * { margin-top: 0.5rem; }
    .space-x-2 > * + * { margin-left: 0.5rem; }
    .ml-4 { margin-left: 1rem; }
    .mb-8 { margin-bottom: 2rem; }
    .mb-6 { margin-bottom: 1.5rem; }
    .mb-4 { margin-bottom: 1rem; }
    .mb-2 { margin-bottom: 0.5rem; }
    .mt-2 { margin-top: 0.5rem; }
    .mt-8 { margin-top: 2rem; }
    .mx-auto { margin-left: auto; margin-right: auto; }
    .px-4 { padding-left: 1rem; padding-right: 1rem; }
    .px-3 { padding-left: 0.75rem; padding-right: 0.75rem; }
    .px-2 { padding-left: 0.5rem; padding-right: 0.5rem; }
    .px-6 { padding-left: 1.5rem; padding-right: 1.5rem; }
    .py-8 { padding-top: 2rem; padding-bottom: 2rem; }
    .py-2 { padding-top: 0.5rem; padding-bottom: 0.5rem; }
    .py-1 { padding-top: 0.25rem; padding-bottom: 0.25rem; }
    .py-3 { padding-top: 0.75rem; padding-bottom: 0.75rem; }
    .py-4 { padding-top: 1rem; padding-bottom: 1rem; }
    .py-8 { padding-top: 2rem; padding-bottom: 2rem; }
    .pb-2 { padding-bottom: 0.5rem; }
    .p-4 { padding: 1rem; }
    .p-6 { padding: 1.5rem; }
    .h-24 { height: 6rem; }
    .h-2 { height: 0.5rem; }
    .w-full { width: 100%; }
    .w-32 { width: 8rem; }
    .text-4xl { font-size: 2.25rem; font-weight: 700; }
    .text-2xl { font-size: 1.5rem; font-weight: 700; }
    .text-xl { font-size: 1.25rem; font-weight: 700; }
    .text-sm { font-size: 0.875rem; }
    .text-xs { font-size: 0.75rem; }
    .font-bold { font-weight: 700; }
    .block { display: block; }
    .border { border: 1px solid #d1d5db; }
    .border-b { border-bottom: 1px solid #d1d5db; }
    .border-b-2 { border-bottom: 2px solid #d1d5db; }
    .rounded { border-radius: 0.375rem; }
    .rounded-lg { border-radius: 0.5rem; }
    .rounded-full { border-radius: 9999px; }
    .shadow { box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
    .bg-white { background-color: white; }
    .bg-gray-50 { background-color: #f9fafb; }
    .bg-gray-100 { background-color: #f3f4f6; }
    .bg-gray-200 { background-color: #e5e7eb; }
    .bg-gray-500 { background-color: #6b7280; }
    .bg-blue-500 { background-color: #3b82f6; }
    .bg-blue-200 { background-color: #bfdbfe; }
    .bg-green-500 { background-color: #10b981; }
    .bg-green-200 { background-color: #d1fae5; }
    .bg-purple-500 { background-color: #a855f7; }
    .bg-purple-200 { background-color: #e9d5ff; }
    .text-white { color: white; }
    .text-gray-500 { color: #6b7280; }
    .text-gray-600 { color: #4b5563; }
    .text-gray-700 { color: #374151; }
    .text-gray-800 { color: #1f2937; }
    .text-blue-800 { color: #1e40af; }
    .text-green-800 { color: #166534; }
    .text-purple-800 { color: #6b21a8; }
    .text-center { text-align: center; }
    .text-left { text-align: left; }
    .hover\:bg-gray-50:hover { background-color: #f9fafb; }
    .hover\:bg-gray-600:hover { background-color: #4b5563; }
    .hover\:bg-blue-600:hover { background-color: #2563eb; }
    .hover\:bg-green-600:hover { background-color: #059669; }
    .hover\:bg-purple-600:hover { background-color: #9333ea; }
</style>
