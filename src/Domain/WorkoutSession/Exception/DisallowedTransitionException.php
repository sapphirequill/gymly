<?php

declare(strict_types=1);

namespace App\Domain\WorkoutSession\Exception;

use App\Domain\Shared\Exception\DomainException;

final class DisallowedTransitionException extends DomainException
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function createForCompleted(): self
    {
        return new self('A workout session can be completed only when it is started.');
    }

    public static function createForCancelled(): self
    {
        return new self('A workout session can be cancelled only when it is started.');
    }
}
