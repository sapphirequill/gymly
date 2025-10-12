<?php

declare(strict_types=1);

namespace App\Application\ReadModel;

final readonly class WorkoutHistoryExerciseSetReadModel
{
    public function __construct(
        public int $repetitions,
        public ?WeightReadModel $weight,
    ) {
    }
}
