<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\EventStore;

use App\Domain\Shared\AggregateRoot;
use App\Domain\Shared\DomainEvent;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final readonly class StoredEventFactory
{
    public function __construct(private NormalizerInterface $normalizer)
    {
    }

    public function createFromDomainEventForAggregate(DomainEvent $domainEvent, AggregateRoot $aggregateRoot, int $version): StoredEvent
    {
        return new StoredEvent(
            Uuid::uuid4(),
            $aggregateRoot->getId(),
            $aggregateRoot::class,
            $domainEvent::class,
            $this->normalizer->normalize($domainEvent),
            $version,
            $domainEvent->occurredAt,
        );
    }
}
