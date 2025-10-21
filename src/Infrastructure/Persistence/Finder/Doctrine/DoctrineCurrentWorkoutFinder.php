<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Finder\Doctrine;

use App\Application\Port\CurrentWorkoutFinder;
use App\Application\ReadModel\CurrentWorkoutReadModel;
use App\Application\ReadModel\MinSetsCompletionRequirementReadModel;
use App\Application\ReadModel\PerformedWorkoutSetReadModel;
use App\Application\ReadModel\WeightReadModel;
use App\Infrastructure\Persistence\Projection\Doctrine\Entity\CurrentWorkout;
use App\Infrastructure\Persistence\Projection\Doctrine\Repository\CurrentWorkoutRepository;

final readonly class DoctrineCurrentWorkoutFinder implements CurrentWorkoutFinder
{
    public function __construct(private CurrentWorkoutRepository $repository)
    {
    }

    public function get(): ?CurrentWorkoutReadModel
    {
        $entity = $this->repository->find();

        if (!$entity instanceof CurrentWorkout) {
            return null;
        }

        $requirements = null;
        if (null !== $entity->getCompletionRequirements()) {
            $requirements = [];

            foreach ($entity->getCompletionRequirements() as $req) {
                $requirements[] = new MinSetsCompletionRequirementReadModel(
                    $req['exerciseCode'],
                    (int) $req['minSets'],
                );
            }
        }

        $performed = [];

        foreach ($entity->getPerformedWorkoutSets() as $set) {
            $weight = null;
            if (isset($set['weight'])) {
                $weight = new WeightReadModel((float) $set['weight']['value'], (string) $set['weight']['unit']);
            }

            $performed[] = new PerformedWorkoutSetReadModel(
                $set['exerciseCode'],
                (int) $set['repetitions'],
                $weight,
            );
        }

        return new CurrentWorkoutReadModel(
            $entity->getId(),
            $entity->getStartedAt(),
            $entity->getTrainingPlanId(),
            $requirements,
            $performed,
        );
    }
}
