<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Finder\Doctrine;

use App\Application\Port\AvailableTrainingPlanFinder;
use App\Application\ReadModel\AvailableTrainingPlanReadModel;
use App\Application\ReadModel\ExerciseRequirementReadModel;
use App\Infrastructure\Persistence\Projection\Doctrine\Repository\AvailableTrainingPlanRepository;

final readonly class DoctrineAvailableTrainingPlanFinder implements AvailableTrainingPlanFinder
{
    public function __construct(private AvailableTrainingPlanRepository $repo)
    {
    }

    /** @return AvailableTrainingPlanReadModel[] */
    public function all(): array
    {
        $result = [];
        foreach ($this->repo->findAll() as $entity) {
            $requirements = [];
            foreach ($entity->getExerciseRequirements() as $req) {
                $requirements[] = new ExerciseRequirementReadModel(
                    $req['exerciseCode'],
                    (int) $req['minSets'],
                );
            }

            $result[] = new AvailableTrainingPlanReadModel(
                $entity->getId(),
                $entity->getName(),
                $requirements,
            );
        }

        return $result;
    }
}
