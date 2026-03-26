<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\LearningSpaceController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'welcome'])->name('home');

Route::get('/project', [HomeController::class, 'projectOverview'])->name('project.overview');
Route::get('/docs', [HomeController::class, 'docsRedirect'])->name('docs.redirect');

Route::get('/dashboard', function () {
    return redirect()->route('role.home');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/space', function () {
        $user = auth()->user();

        if ($user?->isAdmin()) {
            return redirect()->route('space.admin');
        }

        if ($user?->isTeacher()) {
            return redirect()->route('space.prof');
        }

        return redirect()->route('space.eleve');
    })->name('role.home');

    Route::get('/space/admin', [LearningSpaceController::class, 'admin'])
        ->middleware('role:admin')
        ->name('space.admin');

    Route::get('/space/prof', [LearningSpaceController::class, 'prof'])
        ->middleware('role:prof')
        ->name('space.prof');
    Route::post('/space/prof/courses', [LearningSpaceController::class, 'storeCourse'])
        ->middleware('role:prof')
        ->name('space.prof.courses.store');

    Route::get('/space/eleve', [LearningSpaceController::class, 'eleve'])
        ->middleware('role:eleve')
        ->name('space.eleve');

    Route::get('/video-test', function () {
        return redirect()->route('role.home');
    })->name('video.test');
    Route::get('/video-list', [HomeController::class, 'videoList'])->name('video.list');
    Route::get('/video-player/{videoFile}', [HomeController::class, 'videoPlayer'])->name('video.player');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
