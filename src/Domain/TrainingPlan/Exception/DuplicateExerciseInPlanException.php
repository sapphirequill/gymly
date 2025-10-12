<?php

declare(strict_types=1);

namespace App\Domain\TrainingPlan\Exception;

use App\Domain\Shared\Exception\DomainException;
use App\Domain\Shared\ValueObject\ExerciseCode;

final class DuplicateExerciseInPlanException extends DomainException
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function create(ExerciseCode $exerciseCode): self
    {
        return new self(\sprintf('Exercise %s is already in the training plan.', $exerciseCode->code));
    }
}
