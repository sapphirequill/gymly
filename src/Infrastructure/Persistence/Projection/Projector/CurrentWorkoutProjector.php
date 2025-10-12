<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Projection\Projector;

use App\Domain\WorkoutSession\DomainEvent\WorkoutSessionCancelled;
use App\Domain\WorkoutSession\DomainEvent\WorkoutSessionCompleted;
use App\Domain\WorkoutSession\DomainEvent\WorkoutSessionStarted;
use App\Domain\WorkoutSession\DomainEvent\WorkoutSetPerformed;
use App\Domain\WorkoutSession\ValueObject\CompletionRequirements;
use App\Domain\WorkoutSession\ValueObject\MinSetsCompletionRequirement;
use App\Infrastructure\Persistence\Projection\Doctrine\Entity\CurrentWorkout;
use App\Infrastructure\Persistence\Projection\Doctrine\Repository\CurrentWorkoutRepository;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Webmozart\Assert\Assert;

final readonly class CurrentWorkoutProjector implements EventSubscriberInterface
{
    public function __construct(private CurrentWorkoutRepository $repository)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            WorkoutSessionStarted::class => 'onStarted',
            WorkoutSetPerformed::class => 'onSetPerformed',
            WorkoutSessionCompleted::class => 'onCompleted',
            WorkoutSessionCancelled::class => 'onCancelled',
        ];
    }

    public function onStarted(WorkoutSessionStarted $event): void
    {
        $completionRequirements = null;

        if ($event->completionRequirements instanceof CompletionRequirements) {
            $completionRequirements = \array_map(static fn (MinSetsCompletionRequirement $requirement): array => [
                'exerciseCode' => $requirement->exerciseCode->code,
                'minSets' => $requirement->minSets,
            ], $event->completionRequirements->minSetsCompletionRequirements);
        }

        $entity = new CurrentWorkout(
            (string) $event->workoutSessionId,
            $event->occurredAt,
            $event->trainingPlanId instanceof UuidInterface ? (string) $event->trainingPlanId : null,
            $completionRequirements
        );

        $this->repository->save($entity);
    }

    public function onSetPerformed(WorkoutSetPerformed $event): void
    {
        $entity = $this->repository->find();

        Assert::notNull($entity);

        $entity->addPerformedSet(
            $event->exerciseCode->code,
            $event->repetitions,
            $event->weight?->value,
            $event->weight?->unit->getLabel(),
        );

        $this->repository->save($entity);
    }

    public function onCompleted(WorkoutSessionCompleted $event): void
    {
        $entity = $this->repository->find();

        Assert::notNull($entity);

        $this->repository->save($entity);
    }

    public function onCancelled(WorkoutSessionCancelled $event): void
    {
        $entity = $this->repository->find();

        Assert::notNull($entity);

        $this->repository->clear();
    }
}
