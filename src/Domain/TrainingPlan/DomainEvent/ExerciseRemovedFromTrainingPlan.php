<?php

declare(strict_types=1);

namespace App\Domain\TrainingPlan\DomainEvent;

use App\Domain\Shared\DomainEvent;
use App\Domain\Shared\ValueObject\ExerciseCode;
use DateTimeImmutable;
use Ramsey\Uuid\UuidInterface;

final readonly class ExerciseRemovedFromTrainingPlan extends DomainEvent
{
    public function __construct(
        public UuidInterface $trainingPlanId,
        public ExerciseCode $exerciseCode,
        DateTimeImmutable $occurredAt,
    ) {
        parent::__construct($occurredAt);
    }
}
