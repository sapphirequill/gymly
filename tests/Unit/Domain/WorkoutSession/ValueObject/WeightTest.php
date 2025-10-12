<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\WorkoutSession\ValueObject;

use App\Domain\WorkoutSession\ValueObject\Weight;
use App\Domain\WorkoutSession\ValueObject\WeightUnit;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Webmozart\Assert\InvalidArgumentException;

final class WeightTest extends TestCase
{
    public function testItConstructsWhenValueIsPositive(): void
    {
        // Given
        $value = 10.0;
        $unit = WeightUnit::KG;

        // When
        $weight = new Weight($value, $unit);

        // Then
        $this->assertSame($value, $weight->value);
        $this->assertSame($unit, $weight->unit);
    }

    #[DataProvider('provideNonPositiveValues')]
    public function testItThrowsExceptionWhenValueIsNotPositive(float $value): void
    {
        // Expect
        $this->expectException(InvalidArgumentException::class);

        // when
        new Weight($value, WeightUnit::KG);
    }

    public static function provideNonPositiveValues(): array
    {
        return [
            [0.0],
            [-0.0001],
            [-1.0],
        ];
    }
}
