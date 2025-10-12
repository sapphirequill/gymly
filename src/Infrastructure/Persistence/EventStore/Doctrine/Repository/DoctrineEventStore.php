<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\EventStore\Doctrine\Repository;

use App\Infrastructure\Persistence\EventStore\Doctrine\Entity\StoredEvent as StoredEventEntity;
use App\Infrastructure\Persistence\EventStore\EventStore;
use App\Infrastructure\Persistence\EventStore\StoredEvent as StoredEventVO;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\UuidInterface;

final readonly class DoctrineEventStore implements EventStore
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function append(StoredEventVO $event): void
    {
        $entity = new StoredEventEntity(
            $event->id,
            $event->aggregateId,
            $event->aggregateType,
            $event->eventType,
            $event->payload,
            $event->version,
            $event->occurredAt,
        );

        $this->entityManager->persist($entity);
    }

    public function getLatestVersionForAggregateId(UuidInterface $id): int
    {
        $result = $this->entityManager->createQueryBuilder()
            ->select('MAX(e.version)')
            ->from(StoredEventEntity::class, 'e')
            ->where('e.aggregateId = :aggregateId')
            ->setParameter('aggregateId', $id)
            ->getQuery()
            ->getSingleScalarResult();

        return (int) ($result ?? 0);
    }

    /** @return StoredEventVO[] */
    public function getAllForAggregateIdOrderedByVersionAsc(UuidInterface $id): array
    {
        $entities = $this->entityManager->createQueryBuilder()
            ->select('e')
            ->from(StoredEventEntity::class, 'e')
            ->where('e.aggregateId = :aggregateId')
            ->setParameter('aggregateId', $id)
            ->orderBy('e.version', 'ASC')
            ->getQuery()
            ->getResult();

        return \array_map(static fn (StoredEventEntity $e): StoredEventVO => new StoredEventVO(
            $e->getId(),
            $e->getAggregateId(),
            $e->getAggregateType(),
            $e->getEventType(),
            $e->getPayload(),
            $e->getVersion(),
            $e->getOccurredAt(),
        ), $entities);
    }
}
