<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\EventStore\Doctrine\Entity;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity]
#[ORM\Table(name: 'event_store')]
#[ORM\Index(name: 'idx_aggregate_id', columns: ['aggregate_id'])]
#[ORM\Index(name: 'idx_aggregate_type', columns: ['aggregate_type'])]
#[ORM\Index(name: 'idx_occurred_at', columns: ['occurred_at'])]
#[ORM\UniqueConstraint(
    name: 'uniq_aggregate_version',
    columns: ['aggregate_id', 'version']
)]
class StoredEvent
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'uuid')]
        private UuidInterface $id,
        #[ORM\Column(type: 'uuid')]
        private UuidInterface $aggregateId,
        #[ORM\Column(type: Types::STRING, length: 255)]
        private string $aggregateType,
        #[ORM\Column(type: Types::STRING, length: 255)]
        private string $eventType,
        #[ORM\Column(type: Types::JSON)]
        private array $payload,
        #[ORM\Column(type: Types::INTEGER)]
        private int $version,
        #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
        private DateTimeImmutable $occurredAt,
    ) {
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getAggregateId(): UuidInterface
    {
        return $this->aggregateId;
    }

    public function getAggregateType(): string
    {
        return $this->aggregateType;
    }

    public function getEventType(): string
    {
        return $this->eventType;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function getOccurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
