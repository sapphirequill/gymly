<?php

declare(strict_types=1);

namespace App\Domain\WorkoutSession\ValueObject;

use App\Domain\Shared\ValueObject\ExerciseCode;
use Webmozart\Assert\Assert;

final readonly class WorkoutSet
{
    public function __construct(public ExerciseCode $exerciseCode, public int $repetitions, public Weight $weight)
    {
        Assert::greaterThan($this->repetitions, 0);
    }
}
