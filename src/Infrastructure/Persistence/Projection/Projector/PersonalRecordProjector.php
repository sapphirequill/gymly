<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Projection\Projector;

use App\Domain\WorkoutSession\DomainEvent\WorkoutSetPerformed;
use App\Domain\WorkoutSession\ValueObject\Weight;
use App\Infrastructure\Persistence\Projection\Doctrine\Entity\PersonalRecord;
use App\Infrastructure\Persistence\Projection\Doctrine\Repository\PersonalRecordRepository;
use Ramsey\Uuid\Uuid;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final readonly class PersonalRecordProjector implements EventSubscriberInterface
{
    public function __construct(private PersonalRecordRepository $repository)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            WorkoutSetPerformed::class => 'onSetPerformed',
        ];
    }

    public function onSetPerformed(WorkoutSetPerformed $event): void
    {
        if (!$event->weight instanceof Weight) {
            return;
        }

        $exerciseCode = $event->exerciseCode->code;
        $weight = $event->weight;
        $personalRecord = $this->repository->findByExerciseCode($exerciseCode);

        if (!$personalRecord instanceof PersonalRecord) {
            $personalRecord = new PersonalRecord(Uuid::uuid4()->toString(), $exerciseCode, $weight->value, $weight->unit->getLabel());
        } elseif ($weight->value > $personalRecord->getMaxWeight()) {
            $personalRecord->setMaxWeight($weight->value);
            $personalRecord->setUnit($weight->unit->getLabel());
        } else {
            return;
        }

        $this->repository->save($personalRecord);
    }
}
