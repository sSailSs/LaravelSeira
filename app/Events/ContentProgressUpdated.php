<?php

namespace App\Events;

use App\Models\UserContentProgress;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ContentProgressUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public UserContentProgress $progress;

    public string $previousStatus; // 'not_started', 'in_progress', 'completed'

    public string $newStatus;

    /**
     * Create a new event instance.
     */
    public function __construct(UserContentProgress $progress, string $previousStatus, string $newStatus)
    {
        $this->progress = $progress;
        $this->previousStatus = $previousStatus;
        $this->newStatus = $newStatus;
    }
}
