<?php

namespace App\Observers;

use App\Events\ContentProgressUpdated;
use App\Models\UserContentProgress;

class UserContentProgressObserver
{
    /**
     * Handle the UserContentProgress "created" event.
     */
    public function created(UserContentProgress $progress): void
    {
        // First progress record is "in_progress"
        ContentProgressUpdated::dispatch($progress, 'not_started', 'in_progress');
    }

    /**
     * Handle the UserContentProgress "updated" event.
     */
    public function updated(UserContentProgress $progress): void
    {
        // Determine status change
        $previousStatus = $this->getStatus($progress->getOriginal('progress_seconds'), $progress->getOriginal('is_completed'));
        $newStatus = $this->getStatus($progress->progress_seconds, $progress->is_completed);

        if ($previousStatus !== $newStatus) {
            ContentProgressUpdated::dispatch($progress, $previousStatus, $newStatus);
        }
    }

    /**
     * Determine progress status from seconds and completion flag.
     */
    private function getStatus(int|null $seconds, bool|null $isCompleted): string
    {
        if ($isCompleted) {
            return 'completed';
        }

        if (($seconds ?? 0) > 0) {
            return 'in_progress';
        }

        return 'not_started';
    }
}
