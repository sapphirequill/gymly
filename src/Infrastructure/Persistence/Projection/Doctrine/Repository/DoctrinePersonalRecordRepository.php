<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Projection\Doctrine\Repository;

use App\Infrastructure\Persistence\Projection\Doctrine\Entity\PersonalRecord;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DoctrinePersonalRecordRepository implements PersonalRecordRepository
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function findByExerciseCode(string $exerciseCode): ?PersonalRecord
    {
        return $this->em->getRepository(PersonalRecord::class)->findOneBy(['exerciseCode' => $exerciseCode]);
    }

    /** @return PersonalRecord[] */
    public function findAll(): array
    {
        return $this->em->getRepository(PersonalRecord::class)->findAll();
    }

    public function save(PersonalRecord $personalRecord): void
    {
        $this->em->persist($personalRecord);
    }
}
