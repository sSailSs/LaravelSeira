<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Espace Prof - Mes Cours & Videos
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="grid gap-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100 space-y-4">
                        <p class="text-lg font-semibold">Bienvenue, {{ auth()->user()->name }}.</p>
                        <p>Depuis cette page, tu peux voir tes cours, ajouter un nouveau cours et accéder au mode vidéo.</p>

                        @if (session('status'))
                            <div class="rounded border border-emerald-500/40 bg-emerald-500/10 px-3 py-2 text-sm text-emerald-700 dark:text-emerald-300">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('space.prof.courses.store') }}" class="grid gap-3 md:grid-cols-2">
                            @csrf
                            <div class="md:col-span-2">
                                <label for="title" class="block text-sm mb-1">Titre du cours</label>
                                <input id="title" name="title" value="{{ old('title') }}" required class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900" />
                                @error('title')<p class="text-sm text-red-500 mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm mb-1">Description</label>
                                <textarea id="description" name="description" rows="3" class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900">{{ old('description') }}</textarea>
                                @error('description')<p class="text-sm text-red-500 mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="school_class_id" class="block text-sm mb-1">Classe</label>
                                <select id="school_class_id" name="school_class_id" required class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                                    <option value="">Selectionner une classe</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" @selected((string) old('school_class_id') === (string) $class->id)>
                                            {{ $class->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('school_class_id')<p class="text-sm text-red-500 mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div class="flex items-end">
                                <button type="submit" class="px-4 py-2 rounded bg-indigo-600 text-white w-full">Ajouter le cours</button>
                            </div>
                        </form>

                        <div class="flex flex-wrap gap-3 pt-2">
                            <a class="px-4 py-2 rounded bg-blue-600 text-white" href="{{ route('project.overview') }}">Vue projet</a>
                            <a class="px-4 py-2 rounded bg-emerald-600 text-white" href="{{ route('video.list') }}">Mode vidéo</a>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold mb-4">Mes Cours</h3>
                        <div class="space-y-3">
                            @forelse($courses as $course)
                                <div class="p-3 rounded border border-gray-200 dark:border-gray-700">
                                    <p class="font-semibold">{{ $course->title }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">
                                        Classe: {{ $course->schoolClass?->name ?? 'N/A' }}
                                    </p>
                                </div>
                            @empty
                                <p class="text-sm text-gray-600 dark:text-gray-300">Tu n'as pas encore de cours. Ajoute-en un ci-dessus.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
