<?php

declare(strict_types=1);

namespace App\Domain\TrainingPlan\DomainEvent;

use App\Domain\Shared\DomainEvent;
use App\Domain\TrainingPlan\ValueObject\ExerciseRequirement;
use DateTimeImmutable;
use Ramsey\Uuid\UuidInterface;

final readonly class TrainingPlanCreated extends DomainEvent
{
    /** @param ExerciseRequirement[] $exerciseRequirements */
    public function __construct(
        public UuidInterface $trainingPlanId,
        public string $name,
        public array $exerciseRequirements,
        DateTimeImmutable $occurredAt,
    ) {
        parent::__construct($occurredAt);
    }
}
