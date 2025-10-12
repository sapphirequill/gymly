<?php

declare(strict_types=1);

namespace App\Domain\WorkoutSession\ValueObject;

use InvalidArgumentException;

enum WeightUnit
{
    case KG;

    public function getLabel(): string
    {
        return match ($this) {
            self::KG => 'kg',
        };
    }

    public static function fromLabel(string $label): self
    {
        return match ($label) {
            'kg' => self::KG,
            default => throw new InvalidArgumentException(\sprintf('Invalid weight unit label: %s', $label)),
        };
    }
}
