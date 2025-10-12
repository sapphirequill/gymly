<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\EventStore;

use DateTimeImmutable;
use Ramsey\Uuid\UuidInterface;

final readonly class StoredEvent
{
    /** @phpstan-ignore missingType.iterableValue */
    public function __construct(
        public UuidInterface $id,
        public UuidInterface $aggregateId,
        public string $aggregateType,
        public string $eventType,
        public array $payload,
        public int $version,
        public DateTimeImmutable $occurredAt,
    ) {
    }
}
