<?php

declare(strict_types=1);

namespace App\Application\ReadModel;

final readonly class PerformedWorkoutSetReadModel
{
    public function __construct(
        public string $exerciseCode,
        public int $repetitions,
        public ?WeightReadModel $weight,
    ) {
    }
}
