<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Projection\Doctrine\Repository;

use App\Infrastructure\Persistence\Projection\Doctrine\Entity\WorkoutHistory;

interface WorkoutHistoryRepository
{
    public function find(string $id): ?WorkoutHistory;

    /** @return WorkoutHistory[] */
    public function findAll(): array;

    public function save(WorkoutHistory $history): void;
}
