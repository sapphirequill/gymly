<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Projection\Doctrine\Repository;

use App\Infrastructure\Persistence\Projection\Doctrine\Entity\CurrentWorkout;

interface CurrentWorkoutRepository
{
    public function find(): ?CurrentWorkout;

    public function save(CurrentWorkout $currentWorkout): void;

    public function clear(): void;
}
