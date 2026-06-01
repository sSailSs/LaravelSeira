<?php

namespace App\Events;

use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserEnrolledInClass
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;

    public SchoolClass $class;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, SchoolClass $class)
    {
        $this->user = $user;
        $this->class = $class;
    }
}
