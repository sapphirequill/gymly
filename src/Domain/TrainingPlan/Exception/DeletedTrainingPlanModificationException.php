<?php

declare(strict_types=1);

namespace App\Domain\TrainingPlan\Exception;

use App\Domain\Shared\Exception\DomainException;

final class DeletedTrainingPlanModificationException extends DomainException
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function createForAddAttempt(): self
    {
        return new self('Cannot add exercises to a deleted training plan.');
    }

    public static function createForRemoveAttempt(): self
    {
        return new self('Cannot remove exercises from a deleted training plan.');
    }
}
