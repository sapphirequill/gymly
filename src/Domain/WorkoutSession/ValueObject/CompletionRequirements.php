<?php

declare(strict_types=1);

namespace App\Domain\WorkoutSession\ValueObject;

use App\Domain\Shared\ValueObject\ExerciseCode;
use Webmozart\Assert\Assert;

final readonly class CompletionRequirements
{
    public function __construct(
        /** @var MinSetsCompletionRequirement[] */
        public array $minSetsCompletionRequirements,
    ) {
        Assert::allIsInstanceOf($minSetsCompletionRequirements, MinSetsCompletionRequirement::class);
    }

    public function containsExercise(ExerciseCode $exerciseCode): bool
    {
        return \array_any($this->minSetsCompletionRequirements, fn ($minSetsCompletionRequirement): bool => $minSetsCompletionRequirement->exerciseCode->equals($exerciseCode));
    }

    /**
     * @param ExerciseCode[] $exerciseCodes
     *
     * @return ExerciseCode[]
     */
    public function getUnsatisfiedExerciseCodes(array $exerciseCodes): array
    {
        $missingExercises = [];

        foreach ($this->minSetsCompletionRequirements as $requirement) {
            $found = \array_any($exerciseCodes, fn ($code) => $requirement->exerciseCode->equals($code));
            if (!$found) {
                $missingExercises[] = $requirement->exerciseCode;
            }
        }

        return $missingExercises;
    }

    public function isMinimumSetsSatisfied(ExerciseCode $exerciseCode, int $sets): bool
    {
        foreach ($this->minSetsCompletionRequirements as $minSetsCompletionRequirement) {
            if ($minSetsCompletionRequirement->exerciseCode->equals($exerciseCode)) {
                return $sets >= $minSetsCompletionRequirement->minSets;
            }
        }

        return false;
    }
}
