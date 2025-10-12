<?php

declare(strict_types=1);

namespace App\Domain\WorkoutSession;

enum WorkoutSessionStatus
{
    case STARTED;
    case COMPLETED;
    case CANCELLED;
}
