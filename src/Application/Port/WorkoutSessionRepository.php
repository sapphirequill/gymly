<?php

declare(strict_types=1);

namespace App\Application\Port;

use App\Domain\WorkoutSession\Exception\WorkoutSessionNotFoundException;
use App\Domain\WorkoutSession\WorkoutSession;
use Ramsey\Uuid\UuidInterface;

interface WorkoutSessionRepository
{
    public function save(WorkoutSession $workoutSession): void;

    /** @throws WorkoutSessionNotFoundException */
    public function getById(UuidInterface $id): WorkoutSession;
}
