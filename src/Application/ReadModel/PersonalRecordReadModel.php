<?php

declare(strict_types=1);

namespace App\Application\ReadModel;

final readonly class PersonalRecordReadModel
{
    public function __construct(
        public string $id,
        public string $exerciseCode,
        public float $maxWeight,
        public string $unit,
    ) {
    }
}
