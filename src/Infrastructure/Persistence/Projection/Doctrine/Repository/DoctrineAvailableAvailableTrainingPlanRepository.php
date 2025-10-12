<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Projection\Doctrine\Repository;

use App\Infrastructure\Persistence\Projection\Doctrine\Entity\AvailableTrainingPlan;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DoctrineAvailableAvailableTrainingPlanRepository implements AvailableTrainingPlanRepository
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function find(string $id): ?AvailableTrainingPlan
    {
        return $this->em->find(AvailableTrainingPlan::class, $id);
    }

    /** @return AvailableTrainingPlan[] */
    public function findAll(): array
    {
        return $this->em->getRepository(AvailableTrainingPlan::class)->findAll();
    }

    public function save(AvailableTrainingPlan $trainingPlan): void
    {
        $this->em->persist($trainingPlan);
    }

    public function remove(AvailableTrainingPlan $trainingPlan): void
    {
        $this->em->remove($trainingPlan);
    }
}
