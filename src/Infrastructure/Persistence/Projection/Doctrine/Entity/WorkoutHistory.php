<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Projection\Doctrine\Entity;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'workout_history')]
class WorkoutHistory
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: Types::GUID)]
        private string $id,
        #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
        private DateTimeImmutable $startedAt,
        #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
        private DateTimeImmutable $completedAt,
        #[ORM\Column(type: Types::INTEGER)]
        private int $durationMinutes,
        #[ORM\Column(type: Types::INTEGER)]
        private int $totalSetsCount,
        #[ORM\Column(type: Types::FLOAT)]
        private float $totalVolume,
        #[ORM\Column(type: Types::JSON)]
        private array $exercises,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getStartedAt(): DateTimeImmutable
    {
        return $this->startedAt;
    }

    public function getCompletedAt(): DateTimeImmutable
    {
        return $this->completedAt;
    }

    public function getDurationMinutes(): int
    {
        return $this->durationMinutes;
    }

    public function getTotalSetsCount(): int
    {
        return $this->totalSetsCount;
    }

    public function getTotalVolume(): float
    {
        return $this->totalVolume;
    }

    public function getExercises(): array
    {
        return $this->exercises;
    }
}
