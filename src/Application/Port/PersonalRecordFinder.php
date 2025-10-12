<?php

declare(strict_types=1);

namespace App\Application\Port;

use App\Application\ReadModel\PersonalRecordReadModel;

interface PersonalRecordFinder
{
    /** @return PersonalRecordReadModel[] */
    public function all(): array;
}
