<?php

declare(strict_types=1);

namespace App\Domain\WorkoutSession\Exception;

use App\Domain\Shared\Exception\DomainException;

final class EmptyWorkoutSessionException extends DomainException
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function create(): self
    {
        return new self('Cannot complete an empty workout session.');
    }
}
