<?php

declare(strict_types=1);

namespace App\Domain\WorkoutSession\ValueObject;

use Webmozart\Assert\Assert;

final readonly class Weight
{
    public function __construct(public float $value, public WeightUnit $unit)
    {
        Assert::greaterThan($this->value, 0);
    }
}
