<?php

declare(strict_types=1);

namespace App\Application\Command\WorkoutSession;

use DateTimeImmutable;
use Ramsey\Uuid\UuidInterface;

final readonly class StartWorkoutSessionCommand
{
    public function __construct(
        public UuidInterface $id,
        public ?UuidInterface $trainingPlanId,
        public DateTimeImmutable $startedAt,
    ) {
    }
}
