<?php

declare(strict_types=1);

namespace App\Application\Command\TrainingPlan;

use App\Domain\Shared\ValueObject\ExerciseCode;
use Ramsey\Uuid\UuidInterface;

final readonly class RemoveExerciseFromTrainingPlanCommand
{
    public function __construct(
        public UuidInterface $trainingPlanId,
        public ExerciseCode $exerciseCode,
    ) {
    }
}
