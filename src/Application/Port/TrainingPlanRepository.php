<?php

declare(strict_types=1);

namespace App\Application\Port;

use App\Domain\TrainingPlan\Exception\TrainingPlanNotFoundException;
use App\Domain\TrainingPlan\TrainingPlan;
use Ramsey\Uuid\UuidInterface;

interface TrainingPlanRepository
{
    public function save(TrainingPlan $trainingPlan): void;

    /** @throws TrainingPlanNotFoundException */
    public function getById(UuidInterface $id): TrainingPlan;
}
