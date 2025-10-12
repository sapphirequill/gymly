<?php

declare(strict_types=1);

namespace App\Domain\TrainingPlan\DomainEvent;

use App\Domain\Shared\DomainEvent;
use DateTimeImmutable;
use Ramsey\Uuid\UuidInterface;

final readonly class TrainingPlanDeleted extends DomainEvent
{
    public function __construct(public UuidInterface $trainingPlanId, DateTimeImmutable $occurredAt)
    {
        parent::__construct($occurredAt);
    }
}
