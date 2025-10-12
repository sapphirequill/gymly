<?php

declare(strict_types=1);

namespace App\Domain\TrainingPlan\Exception;

use App\Domain\Shared\Exception\DomainException;
use App\Domain\Shared\ValueObject\ExerciseCode;

final class ExerciseNotInPlanException extends DomainException
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function create(ExerciseCode $exerciseCode): self
    {
        return new self(\sprintf('Exercise %s is not present in the training plan.', $exerciseCode->code));
    }
}
