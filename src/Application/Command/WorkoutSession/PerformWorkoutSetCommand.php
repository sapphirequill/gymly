<?php

declare(strict_types=1);

namespace App\Application\Command\WorkoutSession;

use App\Domain\Shared\ValueObject\ExerciseCode;
use App\Domain\WorkoutSession\ValueObject\Weight;

final readonly class PerformWorkoutSetCommand
{
    public function __construct(
        public ExerciseCode $exerciseCode,
        public int $repetitions,
        public ?Weight $weight,
    ) {
    }
}
