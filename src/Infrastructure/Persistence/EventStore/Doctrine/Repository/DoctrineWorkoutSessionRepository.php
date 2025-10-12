<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\EventStore\Doctrine\Repository;

use App\Application\Port\WorkoutSessionRepository;
use App\Domain\WorkoutSession\Exception\WorkoutSessionNotFoundException;
use App\Domain\WorkoutSession\WorkoutSession;
use App\Infrastructure\Persistence\EventStore\EventStore;
use App\Infrastructure\Persistence\EventStore\StoredEvent;
use App\Infrastructure\Persistence\EventStore\StoredEventFactory;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final readonly class DoctrineWorkoutSessionRepository implements WorkoutSessionRepository
{
    public function __construct(
        private EventStore $eventStore,
        private StoredEventFactory $storedEventFactory,
        private DenormalizerInterface $denormalizer,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function save(WorkoutSession $workoutSession): void
    {
        $latestVersion = $this->eventStore->getLatestVersionForAggregateId($workoutSession->getId());

        foreach ($workoutSession->getRecordedEvents() as $event) {
            $this->eventStore->append(
                $this->storedEventFactory->createFromDomainEventForAggregate(
                    $event,
                    $workoutSession,
                    ++$latestVersion
                )
            );

            $this->eventDispatcher->dispatch($event);
        }

        $workoutSession->clearRecordedEvents();
    }

    public function getById(UuidInterface $id): WorkoutSession
    {
        $storedEvents = $this->eventStore->getAllForAggregateIdOrderedByVersionAsc($id);

        if ([] === $storedEvents) {
            throw WorkoutSessionNotFoundException::create();
        }

        $domainEvents = \array_map(fn (StoredEvent $storedEvent): mixed => $this->denormalizer->denormalize($storedEvent->payload, $storedEvent->eventType), $storedEvents);

        return WorkoutSession::reconstituteFromHistory($id, $domainEvents);
    }
}
