<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Projection\Doctrine\Repository;

use App\Infrastructure\Persistence\Projection\Doctrine\Entity\AvailableTrainingPlan;

interface AvailableTrainingPlanRepository
{
    public function find(string $id): ?AvailableTrainingPlan;

    /** @return AvailableTrainingPlan[] */
    public function findAll(): array;

    public function save(AvailableTrainingPlan $trainingPlan): void;

    public function remove(AvailableTrainingPlan $trainingPlan): void;
}
