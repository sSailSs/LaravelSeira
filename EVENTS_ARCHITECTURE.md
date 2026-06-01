# Events & Listeners Architecture

This document describes the event-driven features of the DevOps Learning Platform.

## Events

### 1. `ContentProgressUpdated`
**File:** `app/Events/ContentProgressUpdated.php`

**Triggered when:**
- A user creates a new progress record (status: not_started → in_progress)
- A user updates their progress on a video/content (status: in_progress → in_progress/completed)

**Event Data:**
- `UserContentProgress $progress` - The progress record
- `string $previousStatus` - Previous status (not_started, in_progress, completed)
- `string $newStatus` - New status

**Listeners:**
- `LogProgressListener` - Logs progress updates for audit trails
- `UpdateStatisticsListener` - Tracks engagement metrics and completion rates

---

### 2. `UserEnrolledInClass`
**File:** `app/Events/UserEnrolledInClass.php`

**Triggered when:**
- A student is added to a class (via API or manually)

**Event Data:**
- `User $user` - The user being enrolled
- `SchoolClass $class` - The class they're joining

**Listeners:**
- `LogProgressListener` - Logs enrollment actions
- `SendNotificationListener` - Could send welcome notifications (extensible)
- `UpdateStatisticsListener` - Tracks enrollment metrics

---

### 3. `CourseCreated`
**File:** `app/Events/CourseCreated.php`

**Triggered when:**
- A teacher or admin creates a new course

**Event Data:**
- `Course $course` - The newly created course

**Listeners:**
- `LogProgressListener` - Logs course creation
- `SendNotificationListener` - Could notify the teacher

---

### 4. `CourseCompleted`
**File:** `app/Events/CourseCompleted.php`

**Triggered when:**
- A student completes a full course (via API call)

**Event Data:**
- `User $user` - Student who completed
- `Course $course` - Course completed
- `int $completionPercentage` - Completion percentage (default 100)

**Listeners:**
- `LogProgressListener` - Logs completion
- `UpdateStatisticsListener` - Records achievement
- `SendNotificationListener` - Could send certificate/badge notifications

---

## Listeners

### 1. `LogProgressListener`
**File:** `app/Listeners/LogProgressListener.php`

**Responsibilities:**
- Logs all progress-related events to `storage/logs/laravel.log`
- Provides audit trail for analytics and debugging
- Handlers:
  - `handleContentProgressUpdated()` - Logs video progress changes
  - `handleUserEnrolledInClass()` - Logs enrollment
  - `handleCourseCreated()` - Logs course creation
  - `handleCourseCompleted()` - Logs course completion

**Example Log Output:**
```
[2026-03-11 10:30:45] local.INFO: Content progress updated {"user_id":5,"chapter_content_id":12,"previous_status":"not_started","new_status":"in_progress","progress_seconds":150,"is_completed":false}
```

---

### 2. `UpdateStatisticsListener`
**File:** `app/Listeners/UpdateStatisticsListener.php`

**Responsibilities:**
- Tracks engagement metrics in Redis Cache
- Calculates completion rates per course
- Tracks enrollment trends
- Handlers:
  - `handleContentProgressUpdated()` - Increments content views, tracks completion rate
  - `handleUserEnrolledInClass()` - Tracks enrollments per class
  - `handleCourseCompleted()` - Records course completions

**Metrics Tracked:**
```
user:{id}:content_views          # Total content views by user
user:{id}:last_activity          # Last activity timestamp
user:{id}:courses_completed      # Total courses completed
user:{id}:course:{id}:completed  # Specific course completion flag

course:{id}:views                # Total views of course content
course:{id}:completions          # Total completions of course
course:{id}:student_completions  # Count of students who completed

class:{id}:enrollments           # Total enrollments in class
class:{id}:last_enrollment       # Last enrollment timestamp
```

---

### 3. `SendNotificationListener`
**File:** `app/Listeners/SendNotificationListener.php`

**Responsibilities:**
- Extensible notification system
- Currently logs via `activity()` (requires `spatie/laravel-activity`)
- Can be extended to send emails, SMS, in-app notifications
- Handlers:
  - `handleUserEnrolledInClass()` - Could send welcome email
  - `handleCourseCreated()` - Could notify teacher

**Future Enhancement:**
```php
// Example: Send email notification
Notification::send($event->user, new EnrolledInClassNotification($event->class));
```

---

## Observers

### 1. `UserContentProgressObserver`
**File:** `app/Observers/UserContentProgressObserver.php`

**Observes:** `UserContentProgress` model

**Methods:**
- `created()` - Dispatches `ContentProgressUpdated` event when progress record is created
- `updated()` - Dispatches `ContentProgressUpdated` event when progress changes

**Logic:**
- Determines previous and new status from `progress_seconds` and `is_completed`
- Only dispatches event if status actually changed

---

### 2. `CourseObserver`
**File:** `app/Observers/CourseObserver.php`

**Observes:** `Course` model

**Methods:**
- `created()` - Dispatches `CourseCreated` event when course is created

---

### 3. `SchoolClassObserver`
**File:** `app/Observers/SchoolClassObserver.php`

**Observes:** `SchoolClass` model

**Notes:**
- Placeholder for consistency
- Real enrollment events are dispatched in `SchoolClassProcessor` (API Platform state handler)

---

## Event Registration

**File:** `app/Providers/EventServiceProvider.php`

```php
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
```

---

## Usage Examples

### Manually Dispatching Events

```php
use App\Events\CourseCompleted;

// Dispatch course completion event
CourseCompleted::dispatch($student, $course, 100);

// Dispatch user enrollment
UserEnrolledInClass::dispatch($student, $class);
```

### Testing Events

```php
use Illuminate\Support\Facades\Event;

Event::fake();

// Perform action that triggers event
$progress->update(['is_completed' => true]);

// Assert event was dispatched
Event::assertDispatched(ContentProgressUpdated::class);
```

---

## Architecture Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                      User Action                             │
│  (Update Progress / Create Course / Enroll Student)         │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
        ┌────────────────────────────┐
        │      Model Observer        │
        │  (e.g., ProgressObserver)  │
        └────────┬───────────────────┘
                 │
                 ▼
        ┌────────────────────────┐
        │ Dispatch Event         │
        │ (ContentProgressUpdated)│
        └────────┬───────────────┘
                 │
     ┌───────────┼───────────┐
     │           │           │
     ▼           ▼           ▼
LogProgress  UpdateStats  SendNotif
 Listener    Listener     Listener
     │           │           │
     ├─ Log to   ├─ Cache    ├─ Email
     │  File    │  Metrics   │  (future)
     │          │            │
     └──────────┴────────────┘
           │
           ▼
    Application State
    (Logs, Cache, Notifications)
```

---

## Testing

Tests are located in:
- `tests/Feature/EventsTest.php` - Event dispatching tests
- `tests/Unit/ListenersTest.php` - Listener handler tests

Run tests:
```bash
php artisan test tests/Feature/EventsTest.php
php artisan test tests/Unit/ListenersTest.php
```

---

## Future Enhancements

1. **Queue Events** - Dispatch listeners to queue for better performance
2. **Webhooks** - Send events to external systems
3. **Real-time Notifications** - Use Pusher/Laravel Reverb for live updates
4. **Analytics** - Integrate with analytics platforms
5. **Email Campaigns** - Send automated emails based on events
6. **Achievements/Badges** - Trigger achievements on course completion
