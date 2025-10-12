<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Shared\ValueObject;

use App\Domain\Shared\ValueObject\ExerciseCode;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Webmozart\Assert\InvalidArgumentException;

final class ExerciseCodeTest extends TestCase
{
    #[DataProvider('provideValidCodes')]
    public function testItConstructsWhenCodeIsValid(string $code): void
    {
        // When
        $exerciseCode = ExerciseCode::fromCode($code);

        // Then
        $this->assertSame($code, $exerciseCode->code);
    }

    #[DataProvider('provideInvalidCodes')]
    public function testItThrowsExceptionWhenCodeIsInvalid(string $code): void
    {
        // Expect
        $this->expectException(InvalidArgumentException::class);

        // when
        ExerciseCode::fromCode($code);
    }

    public function testItEqualsWhenCodesAreTheSame(): void
    {
        // Given
        $a = ExerciseCode::fromCode('PUSH_UP');
        $b = ExerciseCode::fromCode('PUSH_UP');

        // When
        $equals = $a->equals($b);

        // Then
        $this->assertTrue($equals);
    }

    public static function provideValidCodes(): array
    {
        return [
            ['SQUAT'],
            ['BENCH_PRESS'],
            ['PULL_UP'],
            ['DEADLIFT'],
        ];
    }

    public static function provideInvalidCodes(): array
    {
        return [
            [''],
            ['squat'],
            ['Bench_Press'],
            ['WITH-DASH'],
            ['with space'],
            ['123'],
        ];
    }
}
