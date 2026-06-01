<?php

namespace App\Providers;

use App\Events\ContentProgressUpdated;
use App\Events\CourseCompleted;
use App\Events\CourseCreated;
use App\Events\UserEnrolledInClass;
use App\Listeners\LogProgressListener;
use App\Listeners\SendNotificationListener;
use App\Listeners\UpdateStatisticsListener;
use App\Models\Course;
use App\Models\SchoolClass;
use App\Models\UserContentProgress;
use App\Observers\CourseObserver;
use App\Observers\SchoolClassObserver;
use App\Observers\UserContentProgressObserver;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        ContentProgressUpdated::class => [
            LogProgressListener::class,
            UpdateStatisticsListener::class,
        ],
        UserEnrolledInClass::class => [
            LogProgressListener::class,
            SendNotificationListener::class,
            UpdateStatisticsListener::class,
        ],
        CourseCreated::class => [
            LogProgressListener::class,
            SendNotificationListener::class,
        ],
        CourseCompleted::class => [
            LogProgressListener::class,
            UpdateStatisticsListener::class,
            SendNotificationListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        // Register model observers
        UserContentProgress::observe(UserContentProgressObserver::class);
        Course::observe(CourseObserver::class);
        SchoolClass::observe(SchoolClassObserver::class);
    }
}
