<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\WorkoutSession\ValueObject;

use App\Domain\Shared\ValueObject\ExerciseCode;
use App\Domain\WorkoutSession\ValueObject\CompletionRequirements;
use App\Domain\WorkoutSession\ValueObject\MinSetsCompletionRequirement;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class CompletionRequirementsTest extends TestCase
{
    public function testItContainsExerciseWhenCodeIsInRequirements(): void
    {
        // Given
        $squat = ExerciseCode::fromCode('SQUAT');
        $bench = ExerciseCode::fromCode('BENCH_PRESS');
        $requirements = new CompletionRequirements([
            new MinSetsCompletionRequirement($squat, 2),
            new MinSetsCompletionRequirement($bench, 1),
        ]);

        // When
        $contains = $requirements->containsExercise($squat);

        // Then
        $this->assertTrue($contains);
    }

    public function testItDoesNotContainExerciseWhenCodeIsNotInRequirements(): void
    {
        // Given
        $squat = ExerciseCode::fromCode('SQUAT');
        $bench = ExerciseCode::fromCode('BENCH_PRESS');
        $deadlift = ExerciseCode::fromCode('DEADLIFT');
        $requirements = new CompletionRequirements([
            new MinSetsCompletionRequirement($squat, 2),
            new MinSetsCompletionRequirement($bench, 1),
        ]);

        // When
        $contains = $requirements->containsExercise($deadlift);

        // Then
        $this->assertFalse($contains);
    }

    public function testItReturnsMissingExerciseCodesWhenSomeAreNotPerformed(): void
    {
        // Given
        $squat = ExerciseCode::fromCode('SQUAT');
        $bench = ExerciseCode::fromCode('BENCH_PRESS');
        $requirements = new CompletionRequirements([
            new MinSetsCompletionRequirement($squat, 1),
            new MinSetsCompletionRequirement($bench, 1),
        ]);

        // When
        $missing = $requirements->getUnsatisfiedExerciseCodes([$squat]);

        // Then
        $this->assertCount(1, $missing);
        $this->assertTrue($bench->equals($missing[0]));
    }

    #[DataProvider('provideIsMinimumSetsSatisfiedCases')]
    public function testItChecksIfMinimumSetsAreSatisfied(string $code, int $minSets, int $performedSets, bool $expected): void
    {
        // Given
        $squat = ExerciseCode::fromCode('SQUAT');
        $bench = ExerciseCode::fromCode('BENCH_PRESS');
        $requirements = new CompletionRequirements([
            new MinSetsCompletionRequirement($squat, 2),
            new MinSetsCompletionRequirement($bench, 1),
        ]);

        // When
        $result = $requirements->isMinimumSetsSatisfied(ExerciseCode::fromCode($code), $performedSets);

        // Then
        $this->assertSame($expected, $result);
    }

    public static function provideIsMinimumSetsSatisfiedCases(): array
    {
        return [
            ['SQUAT', 2, 2, true],
            ['SQUAT', 2, 3, true],
            ['SQUAT', 2, 1, false],
            ['BENCH_PRESS', 1, 0, false],
            ['DEADLIFT', 1, 5, false], // not in requirements
        ];
    }
}
