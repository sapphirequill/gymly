<?php

declare(strict_types=1);

namespace App\Application\ReadModel;

final readonly class AvailableTrainingPlanReadModel
{
    /** @param ExerciseRequirementReadModel[] $exerciseRequirements */
    public function __construct(
        public string $id,
        public string $name,
        public array $exerciseRequirements,
    ) {
    }
}
