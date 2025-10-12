<?php

declare(strict_types=1);

namespace App\Domain\WorkoutSession\Exception;

use App\Domain\Shared\Exception\DomainException;

final class WorkoutSessionNotFoundException extends DomainException
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function create(): self
    {
        return new self('Workout session not found.');
    }

    public static function createForCurrent(): self
    {
        return new self('Current workout session not found.');
    }
}
