<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\EventStore\Doctrine\Repository;

use App\Application\Port\TrainingPlanRepository;
use App\Domain\TrainingPlan\Exception\TrainingPlanNotFoundException;
use App\Domain\TrainingPlan\TrainingPlan;
use App\Infrastructure\Persistence\EventStore\EventStore;
use App\Infrastructure\Persistence\EventStore\StoredEvent;
use App\Infrastructure\Persistence\EventStore\StoredEventFactory;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final readonly class DoctrineTrainingPlanRepository implements TrainingPlanRepository
{
    public function __construct(
        private EventStore $eventStore,
        private StoredEventFactory $storedEventFactory,
        private DenormalizerInterface $denormalizer,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function save(TrainingPlan $trainingPlan): void
    {
        $latestVersion = $this->eventStore->getLatestVersionForAggregateId($trainingPlan->getId());

        foreach ($trainingPlan->getRecordedEvents() as $event) {
            $this->eventStore->append(
                $this->storedEventFactory->createFromDomainEventForAggregate(
                    $event,
                    $trainingPlan,
                    ++$latestVersion
                )
            );

            $this->eventDispatcher->dispatch($event);
        }

        $trainingPlan->clearRecordedEvents();
    }

    public function getById(UuidInterface $id): TrainingPlan
    {
        $storedEvents = $this->eventStore->getAllForAggregateIdOrderedByVersionAsc($id);

        if ([] === $storedEvents) {
            throw TrainingPlanNotFoundException::create();
        }

        $domainEvents = \array_map(fn (StoredEvent $storedEvent): mixed => $this->denormalizer->denormalize($storedEvent->payload, $storedEvent->eventType), $storedEvents);

        return TrainingPlan::reconstituteFromHistory($id, $domainEvents);
    }
}
