<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Projection\Doctrine\Entity;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'current_workout')]
class CurrentWorkout
{
    #[ORM\Column(type: Types::JSON)]
    private array $performedWorkoutSets = [];

    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: Types::GUID)]
        private string $id,
        #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
        private DateTimeImmutable $startedAt,
        #[ORM\Column(type: Types::GUID, nullable: true)]
        private ?string $trainingPlanId,
        #[ORM\Column(type: Types::JSON, nullable: true)]
        private ?array $completionRequirements,
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

    public function getTrainingPlanId(): ?string
    {
        return $this->trainingPlanId;
    }

    public function getCompletionRequirements(): ?array
    {
        return $this->completionRequirements;
    }

    public function getPerformedWorkoutSets(): array
    {
        return $this->performedWorkoutSets;
    }

    public function addPerformedSet(string $exerciseCode, int $repetitions, ?float $weightValue, ?string $weightUnit): void
    {
        $performedWorkoutSet = [
            'exerciseCode' => $exerciseCode,
            'repetitions' => $repetitions,
        ];

        if (null !== $weightValue && null !== $weightUnit) {
            $performedWorkoutSet['weight'] = [
                'value' => $weightValue,
                'unit' => $weightUnit,
            ];
        }

        $this->performedWorkoutSets[] = $performedWorkoutSet;
    }
}
