<?php

declare(strict_types=1);

namespace App\Application\ReadModel;

final readonly class WorkoutHistoryExerciseReadModel
{
    /** @param WorkoutHistoryExerciseSetReadModel[] $sets */
    public function __construct(
        public string $exerciseCode,
        public array $sets,
    ) {
    }
}
