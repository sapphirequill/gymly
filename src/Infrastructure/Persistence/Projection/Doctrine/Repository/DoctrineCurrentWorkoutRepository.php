<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Projection\Doctrine\Repository;

use App\Infrastructure\Persistence\Projection\Doctrine\Entity\CurrentWorkout;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DoctrineCurrentWorkoutRepository implements CurrentWorkoutRepository
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function find(): ?CurrentWorkout
    {
        $results = $this->em->getRepository(CurrentWorkout::class)->findAll();

        return $results[0] ?? null;
    }

    public function save(CurrentWorkout $currentWorkout): void
    {
        $this->em->persist($currentWorkout);
    }

    public function clear(): void
    {
        $this->em->createQuery(\sprintf('DELETE FROM %s c', CurrentWorkout::class))->execute();
    }
}
