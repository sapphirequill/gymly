<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Finder\Doctrine;

use App\Application\Port\WorkoutHistoryFinder;
use App\Application\ReadModel\WeightReadModel;
use App\Application\ReadModel\WorkoutHistoryExerciseReadModel;
use App\Application\ReadModel\WorkoutHistoryExerciseSetReadModel;
use App\Application\ReadModel\WorkoutHistoryReadModel;
use App\Infrastructure\Persistence\Projection\Doctrine\Repository\WorkoutHistoryRepository;

final readonly class DoctrineWorkoutHistoryFinder implements WorkoutHistoryFinder
{
    public function __construct(private WorkoutHistoryRepository $repo)
    {
    }

    /** @return WorkoutHistoryReadModel[] */
    public function all(): array
    {
        $result = [];
        foreach ($this->repo->findAll() as $entity) {
            $exercises = [];
            foreach ($entity->getExercises() as $exercise) {
                $sets = [];
                foreach ($exercise['sets'] as $set) {
                    $weight = null;
                    if (isset($set['weight'])) {
                        $weight = new WeightReadModel((float) $set['weight']['value'], (string) $set['weight']['unit']);
                    }

                    $sets[] = new WorkoutHistoryExerciseSetReadModel((int) $set['repetitions'], $weight);
                }

                $exercises[] = new WorkoutHistoryExerciseReadModel($exercise['exerciseCode'], $sets);
            }

            $result[] = new WorkoutHistoryReadModel(
                $entity->getId(),
                $entity->getStartedAt(),
                $entity->getCompletedAt(),
                $entity->getDurationMinutes(),
                $entity->getTotalSetsCount(),
                $entity->getTotalVolume(),
                $exercises,
            );
        }

        return $result;
    }
}
