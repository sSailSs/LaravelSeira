<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'welcome'])->name('home');

Route::get('/project', [HomeController::class, 'projectOverview'])->name('project.overview');
Route::get('/docs', [HomeController::class, 'docsRedirect'])->name('docs.redirect');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/video-test', [HomeController::class, 'videoTest'])->name('video.test');
    Route::get('/video-list', [HomeController::class, 'videoList'])->name('video.list');
    Route::get('/video-player/{videoFile}', [HomeController::class, 'videoPlayer'])->name('video.player');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
