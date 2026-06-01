<div class="container mx-auto px-4 py-8">
    <h1 class="text-4xl font-bold mb-8">🎓 Espace Admin</h1>

    <div class="grid grid-cols-4 gap-4 mb-8">
        <div class="bg-blue-500 text-white p-6 rounded-lg shadow">
            <div class="text-3xl font-bold">{{ $stats['total_users'] ?? 0 }}</div>
            <div class="text-sm">Utilisateurs</div>
        </div>
        <div class="bg-green-500 text-white p-6 rounded-lg shadow">
            <div class="text-3xl font-bold">{{ $stats['total_courses'] ?? 0 }}</div>
            <div class="text-sm">Cours</div>
        </div>
        <div class="bg-purple-500 text-white p-6 rounded-lg shadow">
            <div class="text-3xl font-bold">{{ $stats['total_classes'] ?? 0 }}</div>
            <div class="text-sm">Classes</div>
        </div>
        <div class="bg-yellow-500 text-white p-6 rounded-lg shadow">
            <div class="text-3xl font-bold">{{ $stats['total_progress'] ?? 0 }}</div>
            <div class="text-sm">Progressions</div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-2xl font-bold mb-4">📊 Derniers Cours</h2>
        <table class="w-full text-left">
            <thead class="border-b-2">
                <tr>
                    <th class="pb-2">Cours</th>
                    <th class="pb-2">Professeur</th>
                    <th class="pb-2">Classe</th>
                    <th class="pb-2">Créé</th>
                </tr>
            </thead>
            <tbody>
                @forelse($courses as $course)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-3">{{ $course->title }}</td>
                        <td class="py-3">{{ $course->teacher->name ?? 'N/A' }}</td>
                        <td class="py-3">{{ $course->schoolClass->name ?? 'N/A' }}</td>
                        <td class="py-3 text-sm text-gray-500">{{ $course->created_at->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-4 text-center text-gray-500">Aucun cours</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="grid grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">👥 Derniers Utilisateurs</h2>
            <ul class="space-y-2">
                @forelse($users as $user)
                    <li class="flex justify-between items-center p-2 bg-gray-50 rounded">
                        <span>{{ $user->name }}</span>
                        <span class="text-xs bg-blue-200 text-blue-800 px-2 py-1 rounded">{{ $user->role }}</span>
                    </li>
                @empty
                    <li class="text-gray-500">Aucun utilisateur</li>
                @endforelse
            </ul>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">🏫 Classes</h2>
            <ul class="space-y-2">
                @forelse($classes as $class)
                    <li class="flex justify-between items-center p-2 bg-gray-50 rounded">
                        <span>{{ $class->name }}</span>
                        <span class="text-xs text-gray-600">{{ $class->students()->count() }} élèves</span>
                    </li>
                @empty
                    <li class="text-gray-500">Aucune classe</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>

<style>
    .container { max-width: 1200px; }
    .grid { display: grid; }
    .grid-cols-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    .grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .gap-4 { gap: 1rem; }
    .gap-6 { gap: 1.5rem; }
    .mb-8 { margin-bottom: 2rem; }
    .mb-6 { margin-bottom: 1.5rem; }
    .mb-4 { margin-bottom: 1rem; }
    .p-6 { padding: 1.5rem; }
    .px-4 { padding-left: 1rem; padding-right: 1rem; }
    .py-8 { padding-top: 2rem; padding-bottom: 2rem; }
    .px-2 { padding-left: 0.5rem; padding-right: 0.5rem; }
    .py-1 { padding-top: 0.25rem; padding-bottom: 0.25rem; }
    .py-2 { padding-top: 0.5rem; padding-bottom: 0.5rem; }
    .py-3 { padding-top: 0.75rem; padding-bottom: 0.75rem; }
    .py-4 { padding-top: 1rem; padding-bottom: 1rem; }
    .mx-auto { margin-left: auto; margin-right: auto; }
    .px-4 { padding-left: 1rem; padding-right: 1rem; }
    .text-center { text-align: center; }
    .text-left { text-align: left; }
    .text-right { text-align: right; }
    .text-4xl { font-size: 2.25rem; line-height: 2.5rem; }
    .text-3xl { font-size: 1.875rem; line-height: 2.25rem; }
    .text-2xl { font-size: 1.5rem; line-height: 2rem; }
    .text-xl { font-size: 1.25rem; line-height: 1.75rem; }
    .text-sm { font-size: 0.875rem; line-height: 1.25rem; }
    .text-xs { font-size: 0.75rem; line-height: 1rem; }
    .font-bold { font-weight: 700; }
    .bg-white { background-color: white; }
    .bg-blue-500 { background-color: #3b82f6; }
    .bg-blue-200 { background-color: #bfdbfe; }
    .bg-blue-800 { background-color: #1e40af; }
    .bg-green-500 { background-color: #10b981; }
    .bg-purple-500 { background-color: #a855f7; }
    .bg-yellow-500 { background-color: #eab308; }
    .bg-gray-50 { background-color: #f9fafb; }
    .text-white { color: white; }
    .text-gray-500 { color: #6b7280; }
    .text-gray-600 { color: #4b5563; }
    .text-blue-800 { color: #1e40af; }
    .rounded { border-radius: 0.375rem; }
    .rounded-lg { border-radius: 0.5rem; }
    .shadow { box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); }
    .border-b { border-bottom: 1px solid; }
    .border-b-2 { border-bottom: 2px solid; }
    .hover\:bg-gray-50:hover { background-color: #f9fafb; }
    .space-y-2 > * + * { margin-top: 0.5rem; }
    .flex { display: flex; }
    .justify-between { justify-content: space-between; }
    .items-center { align-items: center; }
    .w-full { width: 100%; }
</style>
