<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Projection\Projector;

use App\Domain\TrainingPlan\DomainEvent\ExerciseAddedToTrainingPlan;
use App\Domain\TrainingPlan\DomainEvent\ExerciseRemovedFromTrainingPlan;
use App\Domain\TrainingPlan\DomainEvent\TrainingPlanCreated;
use App\Domain\TrainingPlan\DomainEvent\TrainingPlanDeleted;
use App\Domain\TrainingPlan\ValueObject\ExerciseRequirement;
use App\Infrastructure\Persistence\Projection\Doctrine\Entity\AvailableTrainingPlan;
use App\Infrastructure\Persistence\Projection\Doctrine\Repository\AvailableTrainingPlanRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Webmozart\Assert\Assert;

final readonly class AvailableTrainingPlanProjector implements EventSubscriberInterface
{
    public function __construct(private AvailableTrainingPlanRepository $repository)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            TrainingPlanCreated::class => 'onCreated',
            ExerciseAddedToTrainingPlan::class => 'onExerciseAdded',
            ExerciseRemovedFromTrainingPlan::class => 'onExerciseRemoved',
            TrainingPlanDeleted::class => 'onDeleted',
        ];
    }

    public function onCreated(TrainingPlanCreated $event): void
    {
        $requirements = \array_map(static fn (ExerciseRequirement $requirement): array => [
            'exerciseCode' => $requirement->exerciseCode->code,
            'minSets' => $requirement->minSets,
        ], $event->exerciseRequirements);

        $entity = new AvailableTrainingPlan((string) $event->trainingPlanId, $event->name, $requirements);
        $this->repository->save($entity);
    }

    public function onExerciseAdded(ExerciseAddedToTrainingPlan $event): void
    {
        $entity = $this->repository->find((string) $event->trainingPlanId);

        Assert::notNull($entity);

        $entity->addRequirement($event->exerciseCode->code, $event->minSets);
        $this->repository->save($entity);
    }

    public function onExerciseRemoved(ExerciseRemovedFromTrainingPlan $event): void
    {
        $entity = $this->repository->find((string) $event->trainingPlanId);

        Assert::notNull($entity);

        $entity->removeRequirement($event->exerciseCode->code);
        $this->repository->save($entity);
    }

    public function onDeleted(TrainingPlanDeleted $event): void
    {
        $entity = $this->repository->find((string) $event->trainingPlanId);

        Assert::notNull($entity);

        $this->repository->remove($entity);
    }
}
