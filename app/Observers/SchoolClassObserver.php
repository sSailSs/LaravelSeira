<?php

namespace App\Observers;

use App\Events\UserEnrolledInClass;
use App\Models\SchoolClass;

class SchoolClassObserver
{
    /**
     * Handle the SchoolClass "updated" event.
     * This fires when students are attached via attach/sync.
     * Note: Pivot events are not directly observable, so this works via custom events in StateProcessor.
     */
    public function updated(SchoolClass $class): void
    {
        // This observer is here for consistency, but the real enrollment tracking
        // happens in app/State/SchoolClassProcessor.php which handles the API Platform
        // state mutations and can fire UserEnrolledInClass events there.
    }
}
