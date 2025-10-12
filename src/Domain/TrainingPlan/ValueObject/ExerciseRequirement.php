<?php

declare(strict_types=1);

namespace App\Domain\TrainingPlan\ValueObject;

use App\Domain\Shared\ValueObject\ExerciseCode;
use Webmozart\Assert\Assert;

final readonly class ExerciseRequirement
{
    public function __construct(public ExerciseCode $exerciseCode, public int $minSets)
    {
        Assert::greaterThanEq($minSets, 1);
    }
}
