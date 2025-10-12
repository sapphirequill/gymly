<?php

declare(strict_types=1);

namespace App\Application\ReadModel;

use DateTimeImmutable;

final readonly class WorkoutHistoryReadModel
{
    /** @param WorkoutHistoryExerciseReadModel[] $exercises */
    public function __construct(
        public string $id,
        public DateTimeImmutable $startedAt,
        public DateTimeImmutable $completedAt,
        public int $durationMinutes,
        public int $totalSetsCount,
        public float $totalVolume,
        public array $exercises,
    ) {
    }
}
