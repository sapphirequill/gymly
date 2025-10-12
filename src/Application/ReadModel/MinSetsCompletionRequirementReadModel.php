<?php

declare(strict_types=1);

namespace App\Application\ReadModel;

final readonly class MinSetsCompletionRequirementReadModel
{
    public function __construct(
        public string $exerciseCode,
        public int $minSets,
    ) {
    }
}
