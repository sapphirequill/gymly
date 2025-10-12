<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\TrainingPlan\ValueObject;

use App\Domain\Shared\ValueObject\ExerciseCode;
use App\Domain\TrainingPlan\ValueObject\ExerciseRequirement;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Webmozart\Assert\InvalidArgumentException;

final class ExerciseRequirementTest extends TestCase
{
    public function testItConstructsWhenMinSetsIsAtLeastOne(): void
    {
        // Given
        $code = ExerciseCode::fromCode('BENCH_PRESS');
        $minSets = 3;

        // When
        $req = new ExerciseRequirement($code, $minSets);

        // Then
        $this->assertSame($code, $req->exerciseCode);
        $this->assertSame($minSets, $req->minSets);
    }

    #[DataProvider('provideInvalidMinSets')]
    public function testItThrowsExceptionWhenMinSetsIsLessThanOne(int $minSets): void
    {
        // Given
        $code = ExerciseCode::fromCode('BENCH_PRESS');

        // Expect
        $this->expectException(InvalidArgumentException::class);

        // When
        new ExerciseRequirement($code, $minSets);
    }

    public static function provideInvalidMinSets(): array
    {
        return [
            [0],
            [-1],
        ];
    }
}
