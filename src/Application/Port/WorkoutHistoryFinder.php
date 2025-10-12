<?php

declare(strict_types=1);

namespace App\Application\Port;

use App\Application\ReadModel\WorkoutHistoryReadModel;

interface WorkoutHistoryFinder
{
    /** @return WorkoutHistoryReadModel[] */
    public function all(): array;
}
