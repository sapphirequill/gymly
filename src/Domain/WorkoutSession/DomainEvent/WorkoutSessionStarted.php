<?php

declare(strict_types=1);

namespace App\Domain\WorkoutSession\DomainEvent;

use App\Domain\Shared\DomainEvent;
use App\Domain\WorkoutSession\ValueObject\CompletionRequirements;
use DateTimeImmutable;
use Ramsey\Uuid\UuidInterface;

final readonly class WorkoutSessionStarted extends DomainEvent
{
    public function __construct(
        public UuidInterface $workoutSessionId,
        public ?UuidInterface $trainingPlanId,
        public ?CompletionRequirements $completionRequirements,
        DateTimeImmutable $occurredAt,
    ) {
        parent::__construct($occurredAt);
    }
}
