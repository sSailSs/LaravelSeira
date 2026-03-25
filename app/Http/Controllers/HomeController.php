<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\ChapterContent;
use App\Models\Course;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function welcome(): View
    {
        return view('welcome', $this->buildPageData());
    }

    public function projectOverview(): View
    {
        $data = $this->buildPageData();

        return view('project-overview', array_merge($data, [
            'modelChecklist' => [
                'User',
                'SchoolClass',
                'Course',
                'Chapter',
                'ChapterContent',
                'UserContentProgress',
                'Book',
            ],
            'controllerChecklist' => [
                'HomeController (web pages)',
                'SchoolClassProcessor (custom students sync state)',
            ],
        ]));
    }

    public function docsRedirect(): RedirectResponse
    {
        return redirect($this->resolveApiPrefix().'/docs');
    }

    public function videoTest(): View
    {
        return view('video-test');
    }

    public function videoList(): View
    {
        return view('video-list');
    }

    public function videoPlayer(string $videoFile): View
    {
        // Sécurité: valider que le fichier demandé est bien une des vidéos disponibles
        $allowedVideos = ['test.mp4', 'Laravel.mp4'];
        if (!in_array($videoFile, $allowedVideos, true)) {
            abort(404, 'Vidéo non trouvée');
        }

        return view('video-player', [
            'videoFile' => $videoFile,
        ]);
    }

    /**
     * @return array{
     *   apiPrefix: string,
     *   stats: array{users:int,classes:int,courses:int,chapters:int,contents:int},
     *   mainEndpoints: list<string>,
     *   relationPayloadExample: array<string, string>
     * }
     */
    private function buildPageData(): array
    {
        $apiPrefix = $this->resolveApiPrefix();

        $stats = [
            'users' => 0,
            'classes' => 0,
            'courses' => 0,
            'chapters' => 0,
            'contents' => 0,
        ];

        try {
            $stats = [
                'users' => User::query()->count(),
                'classes' => SchoolClass::query()->count(),
                'courses' => Course::query()->count(),
                'chapters' => Chapter::query()->count(),
                'contents' => ChapterContent::query()->count(),
            ];
        } catch (\Throwable) {
            // Database may be unavailable before migrations; keep stats at zero.
        }

        return [
            'apiPrefix' => $apiPrefix,
            'stats' => $stats,
            'mainEndpoints' => [
                '/users',
                '/school_classes',
                '/courses',
                '/chapters',
                '/chapter_contents',
                '/docs',
            ],
            'relationPayloadExample' => [
                'title' => 'Histoire - 5A',
                'description' => "Cours d'histoire",
                'schoolClass' => $apiPrefix.'/school_classes/1',
                'teacher' => $apiPrefix.'/users/1',
            ],
        ];
    }

    private function resolveApiPrefix(): string
    {
        return '/'.ltrim((string) config('api-platform.defaults.route_prefix', '/api'), '/');
    }
}
