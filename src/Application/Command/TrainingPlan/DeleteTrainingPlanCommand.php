<?php

declare(strict_types=1);

namespace App\Application\Command\TrainingPlan;

use Ramsey\Uuid\UuidInterface;

final readonly class DeleteTrainingPlanCommand
{
    public function __construct(public UuidInterface $id)
    {
    }
}
