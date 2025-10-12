<?php

declare(strict_types=1);

namespace App\Domain\WorkoutSession\Exception;

use App\Domain\Shared\Exception\DomainException;

final class FinishedWorkoutSessionException extends DomainException
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function createForWorkoutSetAttempt(): self
    {
        return new self('Cannot add a performed set to a finished workout session.');
    }
}
