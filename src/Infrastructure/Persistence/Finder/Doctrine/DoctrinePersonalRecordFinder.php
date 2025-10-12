<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Finder\Doctrine;

use App\Application\Port\PersonalRecordFinder;
use App\Application\ReadModel\PersonalRecordReadModel;
use App\Infrastructure\Persistence\Projection\Doctrine\Repository\PersonalRecordRepository;

final readonly class DoctrinePersonalRecordFinder implements PersonalRecordFinder
{
    public function __construct(private PersonalRecordRepository $repo)
    {
    }

    /** @return PersonalRecordReadModel[] */
    public function all(): array
    {
        $result = [];
        foreach ($this->repo->findAll() as $entity) {
            $result[] = new PersonalRecordReadModel(
                $entity->getId(),
                $entity->getExerciseCode(),
                $entity->getMaxWeight(),
                $entity->getUnit(),
            );
        }

        return $result;
    }
}
