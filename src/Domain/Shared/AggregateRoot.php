<?php

declare(strict_types=1);

namespace App\Domain\Shared;

use Ramsey\Uuid\UuidInterface;

abstract class AggregateRoot
{
    /** @var DomainEvent[] */
    private array $recordedEvents = [];

    protected function __construct(protected readonly UuidInterface $id)
    {
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    protected function recordEvent(DomainEvent $event): void
    {
        $this->recordedEvents[] = $event;
        $this->apply($event);
    }

    /** @return DomainEvent[] */
    public function getRecordedEvents(): array
    {
        return $this->recordedEvents;
    }

    public function clearRecordedEvents(): void
    {
        $this->recordedEvents = [];
    }

    abstract protected function apply(DomainEvent $event): void;

    /** @param DomainEvent[] $events */
    public static function reconstituteFromHistory(UuidInterface $id, array $events): static
    {
        /** @phpstan-ignore new.static */
        $self = new static($id);

        foreach ($events as $event) {
            $self->apply($event);
        }

        $self->clearRecordedEvents();

        return $self;
    }
}
