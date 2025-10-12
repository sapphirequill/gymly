<?php

declare(strict_types=1);

namespace App\Domain\WorkoutSession\DomainEvent;

use App\Domain\Shared\DomainEvent;
use App\Domain\Shared\ValueObject\ExerciseCode;
use App\Domain\WorkoutSession\ValueObject\Weight;
use DateTimeImmutable;
use Ramsey\Uuid\UuidInterface;

final readonly class WorkoutSetPerformed extends DomainEvent
{
    public function __construct(
        public UuidInterface $workoutSessionId,
        public ExerciseCode $exerciseCode,
        public int $repetitions,
        public ?Weight $weight,
        DateTimeImmutable $occurredAt,
    ) {
        parent::__construct($occurredAt);
    }
}
