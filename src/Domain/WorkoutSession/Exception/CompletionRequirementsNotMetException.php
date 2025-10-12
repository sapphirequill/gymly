<?php

declare(strict_types=1);

namespace App\Domain\WorkoutSession\Exception;

use App\Domain\Shared\Exception\DomainException;
use App\Domain\Shared\ValueObject\ExerciseCode;

final class CompletionRequirementsNotMetException extends DomainException
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function createForRedundantExerciseCode(ExerciseCode $exerciseCode): self
    {
        return new self(\sprintf('Exercise with code "%s" is not in the completion requirements.', $exerciseCode->code));
    }

    public static function createForInsufficientSetsForExercise(ExerciseCode $exerciseCode): self
    {
        return new self(\sprintf('Insufficient sets performed for `%s`.', $exerciseCode->code));
    }

    /** @param ExerciseCode[] $exerciseCodes */
    public static function createForMissingExerciseCodes(array $exerciseCodes): self
    {
        return new self(
            \sprintf(
                'Missing exercises: %s.', \implode(
                    ', ',
                    \array_map(
                        static fn (ExerciseCode $exerciseCode): string => $exerciseCode->code,
                        $exerciseCodes
                    )
                )
            )
        );
    }
}
