<?php

namespace App\Providers;

use App\Models\Book;
use App\Models\Chapter;
use App\Models\ChapterContent;
use App\Models\Course;
use App\Models\SchoolClass;
use App\Models\User;
use App\Models\UserContentProgress;
use App\Policies\BookPolicy;
use App\Policies\ChapterContentPolicy;
use App\Policies\ChapterPolicy;
use App\Policies\CoursePolicy;
use App\Policies\SchoolClassPolicy;
use App\Policies\UserContentProgressPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Book::class, BookPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(SchoolClass::class, SchoolClassPolicy::class);
        Gate::policy(Course::class, CoursePolicy::class);
        Gate::policy(Chapter::class, ChapterPolicy::class);
        Gate::policy(ChapterContent::class, ChapterContentPolicy::class);
        Gate::policy(UserContentProgress::class, UserContentProgressPolicy::class);

        Gate::before(function (?User $user) {
            if ($user?->isAdmin()) {
                return true;
            }

            return null;
        });
    }
}
