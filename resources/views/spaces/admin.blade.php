<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Espace Admin - Accueil Cours & Videos
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="grid gap-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100 space-y-4">
                        <p class="text-lg font-semibold">Bienvenue, {{ auth()->user()->name }}.</p>
                        <p>Vue globale: tu vois les cours de la plateforme et l'accès rapide au mode vidéo.</p>
                        <div class="flex flex-wrap gap-3">
                            <a class="px-4 py-2 rounded bg-blue-600 text-white" href="{{ route('project.overview') }}">Vue projet</a>
                            <a class="px-4 py-2 rounded bg-emerald-600 text-white" href="{{ route('video.list') }}">Mode vidéo</a>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold mb-4">Cours Disponibles</h3>
                        <div class="space-y-3">
                            @forelse($courses as $course)
                                <div class="p-3 rounded border border-gray-200 dark:border-gray-700">
                                    <p class="font-semibold">{{ $course->title }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">
                                        Classe: {{ $course->schoolClass?->name ?? 'N/A' }} | Prof: {{ $course->teacher?->name ?? 'N/A' }}
                                    </p>
                                </div>
                            @empty
                                <p class="text-sm text-gray-600 dark:text-gray-300">Aucun cours pour le moment.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
