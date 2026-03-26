<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LearningSpaceController extends Controller
{
    public function admin(): View
    {
        $courses = Course::query()
            ->with(['teacher:id,name', 'schoolClass:id,name'])
            ->latest()
            ->take(20)
            ->get();

        return view('spaces.admin', [
            'courses' => $courses,
            'videos' => $this->videos(),
        ]);
    }

    public function prof(Request $request): View
    {
        $teacher = $request->user();

        $courses = Course::query()
            ->with(['schoolClass:id,name'])
            ->where('teacher_id', $teacher->id)
            ->latest()
            ->get();

        $classes = SchoolClass::query()
            ->where('teacher_id', $teacher->id)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('spaces.prof', [
            'courses' => $courses,
            'classes' => $classes,
            'videos' => $this->videos(),
        ]);
    }

    public function eleve(Request $request): View
    {
        $student = $request->user();
        $classIds = $student->classes()->pluck('school_classes.id');

        $courses = Course::query()
            ->with(['teacher:id,name', 'schoolClass:id,name'])
            ->whereIn('school_class_id', $classIds)
            ->latest()
            ->get();

        return view('spaces.eleve', [
            'courses' => $courses,
            'videos' => $this->videos(),
        ]);
    }

    public function storeCourse(Request $request): RedirectResponse
    {
        $teacher = $request->user();

        if (!$teacher || !$teacher->isTeacher()) {
            abort(403, 'Seuls les profs peuvent ajouter des cours.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'school_class_id' => ['required', 'integer', 'exists:school_classes,id'],
        ]);

        $classOwned = SchoolClass::query()
            ->whereKey($validated['school_class_id'])
            ->where('teacher_id', $teacher->id)
            ->exists();

        if (!$classOwned) {
            return back()->withErrors([
                'school_class_id' => 'Tu peux ajouter un cours seulement dans une de tes classes.',
            ])->withInput();
        }

        Course::query()->create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'school_class_id' => $validated['school_class_id'],
            'teacher_id' => $teacher->id,
        ]);

        return redirect()->route('space.prof')->with('status', 'Cours ajouté avec succès.');
    }

    /**
     * @return array<int, array{file:string,label:string,link:string}>
     */
    private function videos(): array
    {
        return [
            ['file' => 'test.mp4', 'label' => 'Test', 'link' => route('video.player', 'test.mp4')],
            ['file' => 'Laravel.mp4', 'label' => 'Laravel', 'link' => route('video.player', 'Laravel.mp4')],
        ];
    }
}
