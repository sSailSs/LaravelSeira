<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\LearningSpaceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('role.home');
    }
    return app(\App\Http\Controllers\HomeController::class)->welcome();
})->name('home');
Route::get('/project', [HomeController::class, 'projectOverview'])->name('project.overview');
Route::get('/docs', [HomeController::class, 'docsRedirect'])->name('docs.redirect');

Route::get('/dashboard', function () {
    return redirect()->route('role.home');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/space', function () {
        $user = auth()->user();
        if ($user?->isAdmin())   return redirect()->route('space.admin');
        if ($user?->isTeacher()) return redirect()->route('space.prof');
        return redirect()->route('space.eleve');
    })->name('role.home');

    /* Admin */
    Route::get('/space/admin', [LearningSpaceController::class, 'admin'])
        ->middleware('role:admin')
        ->name('space.admin');

    /* Admin sub-pages */
    Route::get('/space/admin/courses', [LearningSpaceController::class, 'adminCourses'])
        ->middleware('role:admin')->name('space.admin.courses');
    Route::post('/space/admin/courses', [LearningSpaceController::class, 'storeAdminCourse'])
        ->middleware('role:admin')->name('space.admin.courses.store');
    Route::get('/space/admin/classes', [LearningSpaceController::class, 'adminClasses'])
        ->middleware('role:admin')->name('space.admin.classes');
    Route::get('/space/admin/teachers', [LearningSpaceController::class, 'adminTeachers'])
        ->middleware('role:admin')->name('space.admin.teachers');
    Route::get('/space/admin/students', [LearningSpaceController::class, 'adminStudents'])
        ->middleware('role:admin')->name('space.admin.students');

    /* Prof */
    Route::get('/space/prof', [LearningSpaceController::class, 'prof'])
        ->middleware('role:prof')
        ->name('space.prof');

    Route::get('/space/prof/courses/{course}', [LearningSpaceController::class, 'profCourse'])
        ->middleware('role:prof')
        ->name('space.prof.course');

    Route::post('/space/prof/courses', [LearningSpaceController::class, 'storeCourse'])
        ->middleware('role:prof')
        ->name('space.prof.courses.store');

    Route::post('/space/prof/courses/{course}/chapters', [LearningSpaceController::class, 'storeChapter'])
        ->middleware('role:prof')
        ->name('space.prof.chapters.store');

    Route::patch('/space/prof/courses/{course}', [LearningSpaceController::class, 'updateCourse'])
        ->middleware('role:prof')->name('space.prof.course.update');
    Route::delete('/space/prof/courses/{course}', [LearningSpaceController::class, 'destroyCourse'])
        ->middleware('role:prof')->name('space.prof.course.destroy');

    Route::patch('/space/prof/courses/{course}/contents/{content}', [LearningSpaceController::class, 'updateContent'])
        ->middleware('role:prof')->name('space.prof.content.update');
    Route::delete('/space/prof/courses/{course}/contents/{content}', [LearningSpaceController::class, 'destroyContent'])
        ->middleware('role:prof')->name('space.prof.content.destroy');

    Route::post('/space/prof/courses/{course}/chapters/{chapter}/contents', [LearningSpaceController::class, 'storeContent'])
        ->middleware('role:prof')
        ->name('space.prof.contents.store');

    Route::post('/space/prof/courses/{course}/reorder', [LearningSpaceController::class, 'reorderContents'])
        ->middleware('role:prof')
        ->name('space.prof.contents.reorder');

    /* Élève */
    Route::get('/space/eleve', [LearningSpaceController::class, 'eleve'])
        ->middleware('role:eleve')
        ->name('space.eleve');

    Route::get('/space/eleve/courses/{course}', [LearningSpaceController::class, 'eleveCourse'])
        ->middleware('role:eleve')
        ->name('space.eleve.course');

    Route::get('/space/eleve/courses/{course}/contents/{content}', [LearningSpaceController::class, 'elevePlayer'])
        ->middleware('role:eleve')
        ->name('space.eleve.player');

    Route::post('/space/eleve/progress', [LearningSpaceController::class, 'saveProgress'])
        ->middleware('role:eleve')
        ->name('space.eleve.progress');

    Route::get('/space/eleve/progression', [LearningSpaceController::class, 'eleveProgression'])
        ->middleware('role:eleve')
        ->name('space.eleve.progression');

    /* Legacy video routes (kept for compatibility) */
    Route::get('/video-list', [HomeController::class, 'videoList'])->name('video.list');
    Route::get('/video-player/{videoFile}', [HomeController::class, 'videoPlayer'])->name('video.player');

    /* Profile */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /* Files */
    Route::get('/files/{file}/download', [FileController::class, 'download'])->name('files.download');
    Route::get('/files/{file}/view', [FileController::class, 'view'])->name('files.view');
});

require __DIR__.'/auth.php';
