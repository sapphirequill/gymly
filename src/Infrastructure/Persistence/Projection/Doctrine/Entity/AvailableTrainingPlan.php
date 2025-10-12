<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Projection\Doctrine\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'available_training_plan')]
class AvailableTrainingPlan
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: Types::GUID)]
        private string $id,
        #[ORM\Column(type: Types::STRING, length: 255)]
        private string $name,
        #[ORM\Column(type: Types::JSON)]
        private array $exerciseRequirements,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getExerciseRequirements(): array
    {
        return $this->exerciseRequirements;
    }

    public function getExercisesCount(): int
    {
        return \count($this->exerciseRequirements);
    }

    public function addRequirement(string $exerciseCode, int $minSets): void
    {
        $this->exerciseRequirements[] = [
            'exerciseCode' => $exerciseCode,
            'minSets' => $minSets,
        ];
    }

    public function removeRequirement(string $exerciseCode): void
    {
        $this->exerciseRequirements = \array_values(
            \array_filter(
                $this->exerciseRequirements,
                static fn (array $requirement): bool => $requirement['exerciseCode'] !== $exerciseCode
            )
        );
    }
}
