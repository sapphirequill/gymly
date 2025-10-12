<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\WorkoutSession\ValueObject;

use App\Domain\Shared\ValueObject\ExerciseCode;
use App\Domain\WorkoutSession\ValueObject\Weight;
use App\Domain\WorkoutSession\ValueObject\WeightUnit;
use App\Domain\WorkoutSession\ValueObject\WorkoutSet;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Webmozart\Assert\InvalidArgumentException;

final class WorkoutSetTest extends TestCase
{
    public function testItConstructsWhenRepetitionsArePositive(): void
    {
        // Given
        $code = ExerciseCode::fromCode('SQUAT');
        $reps = 10;
        $weight = new Weight(100.0, WeightUnit::KG);

        // When
        $set = new WorkoutSet($code, $reps, $weight);

        // Then
        $this->assertSame($code, $set->exerciseCode);
        $this->assertSame($reps, $set->repetitions);
        $this->assertSame($weight, $set->weight);
    }

    #[DataProvider('provideNonPositiveReps')]
    public function testItThrowsExceptionWhenRepetitionsAreNotPositive(int $reps): void
    {
        // Given
        $code = ExerciseCode::fromCode('SQUAT');
        $weight = new Weight(50.0, WeightUnit::KG);

        // Expect
        $this->expectException(InvalidArgumentException::class);

        // When
        new WorkoutSet($code, $reps, $weight);
    }

    public static function provideNonPositiveReps(): array
    {
        return [
            [0],
            [-1],
        ];
    }
}
