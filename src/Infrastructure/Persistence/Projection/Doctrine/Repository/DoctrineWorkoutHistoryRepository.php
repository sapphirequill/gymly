<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Projection\Doctrine\Repository;

use App\Infrastructure\Persistence\Projection\Doctrine\Entity\WorkoutHistory;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DoctrineWorkoutHistoryRepository implements WorkoutHistoryRepository
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function find(string $id): ?WorkoutHistory
    {
        return $this->em->find(WorkoutHistory::class, $id);
    }

    /** @return WorkoutHistory[] */
    public function findAll(): array
    {
        return $this->em->getRepository(WorkoutHistory::class)->findAll();
    }

    public function save(WorkoutHistory $history): void
    {
        $this->em->persist($history);
    }
}
