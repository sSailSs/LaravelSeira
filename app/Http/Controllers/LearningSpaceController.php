<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\ChapterContent;
use App\Models\Course;
use App\Models\SchoolClass;
use App\Models\User;
use App\Models\UserContentProgress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LearningSpaceController extends Controller
{
    public function admin(): View
    {
        $stats = [
            'students' => User::where('role', User::ROLE_STUDENT)->count(),
            'teachers' => User::where('role', User::ROLE_TEACHER)->count(),
            'courses'  => Course::count(),
            'classes'  => SchoolClass::count(),
        ];

        $courses = Course::query()
            ->with([
                'teacher:id,name',
                'schoolClass:id,name',
                'chapters' => fn ($q) => $q->select('id', 'course_id')
                    ->with('contents:id,chapter_id,content_type'),
            ])
            ->latest()
            ->take(10)
            ->get()
            ->each(function (Course $course) {
                $course->video_count = $course->chapters->sum(
                    fn ($ch) => $ch->contents->where('content_type', 'video')->count()
                );
            });

        $classes = SchoolClass::withCount(['students', 'courses'])
            ->with(['teacher:id,name'])
            ->orderBy('name')
            ->get();

        $recentCourses = Course::with(['teacher:id,name', 'schoolClass:id,name'])
            ->latest()
            ->take(5)
            ->get();

        return view('spaces.admin', compact('stats', 'courses', 'classes', 'recentCourses'));
    }

    public function adminCourses(): View
    {
        $courses = Course::query()
            ->with([
                'teacher:id,name',
                'schoolClass:id,name',
                'chapters' => fn ($q) => $q->select('id', 'course_id')
                    ->with('contents:id,chapter_id,content_type'),
            ])
            ->latest()
            ->paginate(25);

        $courses->each(function (Course $c) {
            $c->video_count = $c->chapters->sum(fn ($ch) => $ch->contents->where('content_type', 'video')->count());
        });

        $teachers = User::where('role', User::ROLE_TEACHER)->orderBy('name')->get(['id', 'name']);
        $allClasses = SchoolClass::orderBy('name')->get(['id', 'name']);

        return view('spaces.admin-courses', compact('courses', 'teachers', 'allClasses'));
    }

    public function storeAdminCourse(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'           => ['required', 'string', 'max:255'],
            'description'     => ['nullable', 'string'],
            'school_class_id' => ['required', 'integer', 'exists:school_classes,id'],
            'teacher_id'      => ['required', 'integer', 'exists:users,id'],
        ]);

        Course::create($validated);

        return redirect()->route('space.admin.courses')->with('status', 'Matière créée.');
    }

    public function adminClasses(): View
    {
        $classes = SchoolClass::withCount(['students', 'courses'])
            ->with(['teacher:id,name'])
            ->orderBy('name')
            ->get();

        $teachers = User::where('role', User::ROLE_TEACHER)->orderBy('name')->get(['id', 'name']);

        return view('spaces.admin-classes', compact('classes', 'teachers'));
    }

    public function adminTeachers(): View
    {
        $teachers = User::where('role', User::ROLE_TEACHER)
            ->withCount(['taughtCourses', 'classesAsTeacher'])
            ->orderBy('name')
            ->get();

        return view('spaces.admin-teachers', compact('teachers'));
    }

    public function adminStudents(): View
    {
        $students = User::where('role', User::ROLE_STUDENT)
            ->with(['classes:id,name'])
            ->orderBy('name')
            ->paginate(30);

        $allClasses = SchoolClass::orderBy('name')->get(['id', 'name']);

        return view('spaces.admin-students', compact('students', 'allClasses'));
    }

    public function prof(Request $request): View|RedirectResponse
    {
        $teacher = $request->user();

        $courses = Course::where('teacher_id', $teacher->id)
            ->orderBy('created_at', 'desc')
            ->get(['id', 'title', 'school_class_id']);

        if ($courses->isNotEmpty()) {
            return redirect()->route('space.prof.course', $courses->first());
        }

        $classes = SchoolClass::where('teacher_id', $teacher->id)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('spaces.prof', compact('courses', 'classes'));
    }

    public function profCourse(Request $request, Course $course): View|RedirectResponse
    {
        $teacher = $request->user();

        if ($course->teacher_id !== $teacher->id) {
            abort(403);
        }

        $course->load([
            'schoolClass:id,name',
            'chapters' => fn ($q) => $q->orderBy('position')
                ->with(['contents' => fn ($q) => $q->orderBy('position')]),
        ]);

        $teacherCourses = Course::where('teacher_id', $teacher->id)
            ->orderBy('created_at', 'desc')
            ->get(['id', 'title']);

        $teacherClasses = SchoolClass::where('teacher_id', $teacher->id)
            ->orderBy('name')
            ->get(['id', 'name']);

        $videoCount = $course->chapters->sum(
            fn ($ch) => $ch->contents->where('content_type', 'video')->count()
        );

        $allClasses = $course->schoolClass ? collect([$course->schoolClass]) : collect();

        return view('spaces.prof-course', compact('course', 'teacherCourses', 'teacherClasses', 'videoCount', 'allClasses'));
    }

    public function storeCourse(Request $request): RedirectResponse
    {
        $teacher = $request->user();

        $validated = $request->validate([
            'title'           => ['required', 'string', 'max:255'],
            'description'     => ['nullable', 'string'],
            'school_class_id' => ['required', 'integer', 'exists:school_classes,id'],
        ]);

        $classOwned = SchoolClass::whereKey($validated['school_class_id'])
            ->where('teacher_id', $teacher->id)
            ->exists();

        if (!$classOwned) {
            return back()->withErrors([
                'school_class_id' => 'Tu peux ajouter un cours seulement dans une de tes classes.',
            ])->withInput();
        }

        $course = Course::create([
            'title'           => $validated['title'],
            'description'     => $validated['description'] ?? null,
            'school_class_id' => $validated['school_class_id'],
            'teacher_id'      => $teacher->id,
        ]);

        return redirect()->route('space.prof.course', $course)->with('status', 'Matière créée.');
    }

    public function storeChapter(Request $request, Course $course): RedirectResponse
    {
        if ($course->teacher_id !== $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        $position = $course->chapters()->max('position') + 1;

        $course->chapters()->create([
            'title'    => $validated['title'],
            'position' => $position,
        ]);

        return redirect()->route('space.prof.course', $course)->with('status', 'Module créé.');
    }

    public function storeContent(Request $request, Course $course, Chapter $chapter): RedirectResponse
    {
        if ($course->teacher_id !== $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'title'            => ['nullable', 'string', 'max:255'],
            'content_type'     => ['required', 'in:video,text'],
            'video_url'        => ['nullable', 'required_if:content_type,video', 'url', 'max:2048'],
            'duration_seconds' => ['nullable', 'integer', 'min:1'],
            'content'          => ['nullable', 'string'],
        ]);

        $position = $chapter->contents()->max('position') + 1;

        $chapter->contents()->create([
            'title'            => $validated['title'] ?? null,
            'content_type'     => $validated['content_type'],
            'video_url'        => $validated['video_url'] ?? null,
            'duration_seconds' => $validated['duration_seconds'] ?? null,
            'content'          => $validated['content'] ?? '',
            'position'         => $position,
        ]);

        return redirect()->route('space.prof.course', $course)->with('status', 'Vidéo ajoutée.');
    }

    public function reorderContents(Request $request, Course $course): JsonResponse
    {
        if ($course->teacher_id !== $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'ids'   => ['required', 'array'],
            'ids.*' => ['integer'],
        ]);

        foreach ($validated['ids'] as $position => $id) {
            ChapterContent::where('id', $id)->update(['position' => $position + 1]);
        }

        return response()->json(['ok' => true]);
    }

    public function eleve(Request $request): View
    {
        $student = $request->user();
        $classIds = $student->classes()->pluck('school_classes.id');

        $courses = Course::query()
            ->with([
                'teacher:id,name',
                'schoolClass:id,name',
                'chapters' => fn ($q) => $q->select('id', 'course_id')
                    ->with('contents:id,chapter_id,content_type,duration_seconds'),
            ])
            ->whereIn('school_class_id', $classIds)
            ->latest()
            ->get();

        $allContentIds = $courses->flatMap(
            fn ($c) => $c->chapters->flatMap(fn ($ch) => $ch->contents->pluck('id'))
        );

        $progressRecords = UserContentProgress::where('user_id', $student->id)
            ->whereIn('chapter_content_id', $allContentIds)
            ->get()
            ->keyBy('chapter_content_id');

        $courses->each(function (Course $course) use ($progressRecords) {
            $videos = $course->chapters->flatMap(fn ($ch) => $ch->contents->where('content_type', 'video'));
            $total = $videos->count();
            $done  = $videos->filter(fn ($c) => isset($progressRecords[$c->id]) && $progressRecords[$c->id]->is_completed)->count();
            $course->total_videos    = $total;
            $course->completed_videos = $done;
            $course->progress_pct    = $total > 0 ? (int) round($done / $total * 100) : 0;
        });

        $lastProgress = UserContentProgress::where('user_id', $student->id)
            ->whereNotNull('last_watched_at')
            ->orderBy('last_watched_at', 'desc')
            ->with(['chapterContent.chapter.course'])
            ->first();

        $totalVideos     = $courses->sum('total_videos');
        $completedVideos = $progressRecords->where('is_completed', true)->count();
        $overallPct      = $totalVideos > 0 ? (int) round($completedVideos / $totalVideos * 100) : 0;

        $studentClasses = $student->classes()->get(['school_classes.id', 'school_classes.name']);

        return view('spaces.eleve', compact(
            'courses', 'lastProgress', 'totalVideos', 'completedVideos', 'overallPct', 'studentClasses'
        ));
    }

    public function eleveCourse(Request $request, Course $course): RedirectResponse
    {
        $student = $request->user();
        $classIds = $student->classes()->pluck('school_classes.id');

        if (!$classIds->contains($course->school_class_id)) {
            abort(403);
        }

        $lastProgress = UserContentProgress::where('user_id', $student->id)
            ->whereHas('chapterContent.chapter', fn ($q) => $q->where('course_id', $course->id))
            ->orderBy('last_watched_at', 'desc')
            ->first();

        if ($lastProgress) {
            return redirect()->route('space.eleve.player', [
                'course'  => $course,
                'content' => $lastProgress->chapter_content_id,
            ]);
        }

        $firstContent = Chapter::where('course_id', $course->id)
            ->orderBy('position')
            ->with(['contents' => fn ($q) => $q->orderBy('position')->where('content_type', 'video')])
            ->get()
            ->flatMap(fn ($ch) => $ch->contents)
            ->first();

        if (!$firstContent) {
            return redirect()->route('space.eleve')->with('error', 'Ce cours ne contient pas encore de vidéos.');
        }

        return redirect()->route('space.eleve.player', ['course' => $course, 'content' => $firstContent]);
    }

    public function elevePlayer(Request $request, Course $course, ChapterContent $content): View
    {
        $student = $request->user();
        $classIds = $student->classes()->pluck('school_classes.id');

        if (!$classIds->contains($course->school_class_id)) {
            abort(403);
        }

        $course->load([
            'teacher:id,name',
            'schoolClass:id,name',
            'chapters' => fn ($q) => $q->orderBy('position')
                ->with(['contents' => fn ($q) => $q->orderBy('position')]),
        ]);

        $allContentIds = $course->chapters->flatMap(fn ($ch) => $ch->contents->pluck('id'));

        $progressRecords = UserContentProgress::where('user_id', $student->id)
            ->whereIn('chapter_content_id', $allContentIds)
            ->get()
            ->keyBy('chapter_content_id');

        $videoContents   = $course->chapters->flatMap(fn ($ch) => $ch->contents->where('content_type', 'video'));
        $totalVideos     = $videoContents->count();
        $completedVideos = $progressRecords->where('is_completed', true)->count();
        $coursePct       = $totalVideos > 0 ? (int) round($completedVideos / $totalVideos * 100) : 0;

        return view('spaces.eleve-player', compact(
            'course', 'content', 'progressRecords', 'totalVideos', 'completedVideos', 'coursePct'
        ));
    }

    public function updateCourse(Request $request, Course $course): RedirectResponse
    {
        if ($course->teacher_id !== $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $course->update($validated);

        return redirect()->route('space.prof.course', $course)->with('status', 'Matière mise à jour.');
    }

    public function destroyCourse(Request $request, Course $course): RedirectResponse
    {
        if ($course->teacher_id !== $request->user()->id) {
            abort(403);
        }

        $chapterIds = $course->chapters()->pluck('id');
        $contentIds = ChapterContent::whereIn('chapter_id', $chapterIds)->pluck('id');

        UserContentProgress::whereIn('chapter_content_id', $contentIds)->delete();
        ChapterContent::whereIn('chapter_id', $chapterIds)->delete();
        $course->chapters()->delete();
        $course->delete();

        return redirect()->route('space.prof')->with('status', 'Matière supprimée.');
    }

    public function updateContent(Request $request, Course $course, ChapterContent $content): RedirectResponse
    {
        if ($course->teacher_id !== $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'title'            => ['nullable', 'string', 'max:255'],
            'video_url'        => ['nullable', 'url', 'max:2048'],
            'duration_seconds' => ['nullable', 'integer', 'min:1'],
        ]);

        $content->update($validated);

        return redirect()->route('space.prof.course', $course)->with('status', 'Vidéo mise à jour.');
    }

    public function destroyContent(Request $request, Course $course, ChapterContent $content): RedirectResponse
    {
        if ($course->teacher_id !== $request->user()->id) {
            abort(403);
        }

        $content->delete();

        return redirect()->route('space.prof.course', $course)->with('status', 'Vidéo supprimée.');
    }

    public function eleveProgression(Request $request): View
    {
        $student = $request->user();
        $classIds = $student->classes()->pluck('school_classes.id');

        $courses = Course::query()
            ->with([
                'teacher:id,name',
                'chapters' => fn ($q) => $q->orderBy('position')
                    ->with(['contents' => fn ($q) => $q->orderBy('position')->where('content_type', 'video')]),
            ])
            ->whereIn('school_class_id', $classIds)
            ->latest()
            ->get();

        $allContentIds = $courses->flatMap(
            fn ($c) => $c->chapters->flatMap(fn ($ch) => $ch->contents->pluck('id'))
        );

        $progressRecords = UserContentProgress::where('user_id', $student->id)
            ->whereIn('chapter_content_id', $allContentIds)
            ->get()
            ->keyBy('chapter_content_id');

        $courses->each(function (Course $course) use ($progressRecords) {
            $course->chapters->each(function (Chapter $chapter) use ($progressRecords) {
                $total = $chapter->contents->count();
                $done  = $chapter->contents->filter(fn ($c) => $progressRecords[$c->id]?->is_completed ?? false)->count();
                $chapter->total_videos     = $total;
                $chapter->completed_videos = $done;
                $chapter->progress_pct     = $total > 0 ? (int) round($done / $total * 100) : 0;
            });
            $total = $course->chapters->sum('total_videos');
            $done  = $course->chapters->sum('completed_videos');
            $course->total_videos     = $total;
            $course->completed_videos = $done;
            $course->progress_pct     = $total > 0 ? (int) round($done / $total * 100) : 0;
        });

        $totalVideos     = $courses->sum('total_videos');
        $completedVideos = $progressRecords->where('is_completed', true)->count();
        $overallPct      = $totalVideos > 0 ? (int) round($completedVideos / $totalVideos * 100) : 0;

        $studentClasses = $student->classes()->get(['school_classes.id', 'school_classes.name']);

        return view('spaces.eleve-progression', compact(
            'courses', 'progressRecords', 'totalVideos', 'completedVideos', 'overallPct', 'studentClasses'
        ));
    }

    public function saveProgress(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'chapter_content_id' => ['required', 'integer', 'exists:chapter_contents,id'],
            'progress_seconds'   => ['required', 'integer', 'min:0'],
            'is_completed'       => ['boolean'],
        ]);

        $student = $request->user();

        $progress = UserContentProgress::updateOrCreate(
            [
                'user_id'            => $student->id,
                'chapter_content_id' => $validated['chapter_content_id'],
            ],
            [
                'progress_seconds' => $validated['progress_seconds'],
                'is_completed'     => $validated['is_completed'] ?? false,
                'last_watched_at'  => now(),
            ]
        );

        return response()->json(['ok' => true, 'id' => $progress->id]);
    }
}
