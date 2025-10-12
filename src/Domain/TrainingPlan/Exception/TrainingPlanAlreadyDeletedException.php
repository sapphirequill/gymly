<?php

declare(strict_types=1);

namespace App\Domain\TrainingPlan\Exception;

use App\Domain\Shared\Exception\DomainException;

final class TrainingPlanAlreadyDeletedException extends DomainException
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function create(): self
    {
        return new self('Training plan has already been deleted.');
    }
}
