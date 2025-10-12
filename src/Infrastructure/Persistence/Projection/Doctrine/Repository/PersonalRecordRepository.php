<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Projection\Doctrine\Repository;

use App\Infrastructure\Persistence\Projection\Doctrine\Entity\PersonalRecord;

interface PersonalRecordRepository
{
    public function findByExerciseCode(string $exerciseCode): ?PersonalRecord;

    /** @return PersonalRecord[] */
    public function findAll(): array;

    public function save(PersonalRecord $personalRecord): void;
}
