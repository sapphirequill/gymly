<?php

declare(strict_types=1);

namespace App\Application\Port;

use App\Application\ReadModel\CurrentWorkoutReadModel;

interface CurrentWorkoutFinder
{
    public function get(): ?CurrentWorkoutReadModel;
}
