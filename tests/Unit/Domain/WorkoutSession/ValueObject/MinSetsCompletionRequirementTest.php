<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\WorkoutSession\ValueObject;

use App\Domain\Shared\ValueObject\ExerciseCode;
use App\Domain\WorkoutSession\ValueObject\MinSetsCompletionRequirement;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Webmozart\Assert\InvalidArgumentException;

final class MinSetsCompletionRequirementTest extends TestCase
{
    public function testItConstructsWhenMinSetsIsAtLeastOne(): void
    {
        // Given
        $code = ExerciseCode::fromCode('SQUAT');
        $minSets = 3;

        // When
        $req = new MinSetsCompletionRequirement($code, $minSets);

        // Then
        $this->assertSame($code, $req->exerciseCode);
        $this->assertSame($minSets, $req->minSets);
    }

    #[DataProvider('provideInvalidMinSets')]
    public function testItThrowsExceptionWhenMinSetsIsLessThanOne(int $minSets): void
    {
        // Given
        $code = ExerciseCode::fromCode('SQUAT');

        // Expect
        $this->expectException(InvalidArgumentException::class);

        // When
        new MinSetsCompletionRequirement($code, $minSets);
    }

    public static function provideInvalidMinSets(): array
    {
        return [
            [0],
            [-1],
            [-10],
        ];
    }
}
