<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Projection\Doctrine\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'personal_record')]
class PersonalRecord
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: Types::GUID)]
        private string $id,
        #[ORM\Column(type: Types::STRING, length: 64, unique: true)]
        private string $exerciseCode,
        #[ORM\Column(type: Types::FLOAT)]
        private float $maxWeight,
        #[ORM\Column(type: Types::STRING)]
        private string $unit,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getExerciseCode(): string
    {
        return $this->exerciseCode;
    }

    public function getMaxWeight(): float
    {
        return $this->maxWeight;
    }

    public function setMaxWeight(float $weight): void
    {
        $this->maxWeight = $weight;
    }

    public function getUnit(): string
    {
        return $this->unit;
    }

    public function setUnit(string $unit): void
    {
        $this->unit = $unit;
    }
}
