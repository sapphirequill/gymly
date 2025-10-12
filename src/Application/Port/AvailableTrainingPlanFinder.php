<?php

declare(strict_types=1);

namespace App\Application\Port;

use App\Application\ReadModel\AvailableTrainingPlanReadModel;

interface AvailableTrainingPlanFinder
{
    /** @return AvailableTrainingPlanReadModel[] */
    public function all(): array;
}
