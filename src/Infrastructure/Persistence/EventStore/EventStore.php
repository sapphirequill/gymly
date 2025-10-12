<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\EventStore;

use Ramsey\Uuid\UuidInterface;

interface EventStore
{
    public function append(StoredEvent $event): void;

    public function getLatestVersionForAggregateId(UuidInterface $id): int;

    /** @return StoredEvent[] */
    public function getAllForAggregateIdOrderedByVersionAsc(UuidInterface $id): array;
}
