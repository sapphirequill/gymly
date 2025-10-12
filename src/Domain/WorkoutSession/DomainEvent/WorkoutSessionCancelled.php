<?php

declare(strict_types=1);

namespace App\Domain\WorkoutSession\DomainEvent;

use App\Domain\Shared\DomainEvent;
use DateTimeImmutable;
use Ramsey\Uuid\UuidInterface;

final readonly class WorkoutSessionCancelled extends DomainEvent
{
    public function __construct(public UuidInterface $workoutSessionId, DateTimeImmutable $occurredAt)
    {
        parent::__construct($occurredAt);
    }
}
