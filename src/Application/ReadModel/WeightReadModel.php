<?php

declare(strict_types=1);

namespace App\Application\ReadModel;

final readonly class WeightReadModel
{
    public function __construct(
        public float $value,
        public string $unit,
    ) {
    }
}
