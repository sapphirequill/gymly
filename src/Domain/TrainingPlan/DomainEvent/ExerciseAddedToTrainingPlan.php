<?php

declare(strict_types=1);

namespace App\Domain\TrainingPlan\DomainEvent;

use App\Domain\Shared\DomainEvent;
use App\Domain\Shared\ValueObject\ExerciseCode;
use DateTimeImmutable;
use Ramsey\Uuid\UuidInterface;

final readonly class ExerciseAddedToTrainingPlan extends DomainEvent
{
    public function __construct(
        public UuidInterface $trainingPlanId,
        public ExerciseCode $exerciseCode,
        public int $minSets,
        DateTimeImmutable $occurredAt,
    ) {
        parent::__construct($occurredAt);
    }
}
