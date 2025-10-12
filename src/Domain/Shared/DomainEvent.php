<?php

declare(strict_types=1);

namespace App\Domain\Shared;

use DateTimeImmutable;

abstract readonly class DomainEvent
{
    public function __construct(public DateTimeImmutable $occurredAt)
    {
    }
}
