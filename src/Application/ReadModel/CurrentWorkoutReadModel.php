<?php

declare(strict_types=1);

namespace App\Application\ReadModel;

use DateTimeImmutable;

final readonly class CurrentWorkoutReadModel
{
    /**
     * @param MinSetsCompletionRequirementReadModel[]|null $completionRequirements
     * @param PerformedWorkoutSetReadModel[]               $performedWorkoutSets
     */
    public function __construct(
        public string $id,
        public DateTimeImmutable $startedAt,
        public ?string $trainingPlanId,
        public ?array $completionRequirements,
        public array $performedWorkoutSets,
    ) {
    }
}
