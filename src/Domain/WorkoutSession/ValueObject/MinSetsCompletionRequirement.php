<?php

declare(strict_types=1);

namespace App\Domain\WorkoutSession\ValueObject;

use App\Domain\Shared\ValueObject\ExerciseCode;
use Webmozart\Assert\Assert;

final readonly class MinSetsCompletionRequirement
{
    public function __construct(public ExerciseCode $exerciseCode, public int $minSets)
    {
        Assert::greaterThanEq($minSets, 1);
    }
}
