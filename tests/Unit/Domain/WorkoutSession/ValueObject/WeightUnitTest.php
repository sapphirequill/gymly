<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\WorkoutSession\ValueObject;

use App\Domain\WorkoutSession\ValueObject\WeightUnit;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class WeightUnitTest extends TestCase
{
    #[DataProvider('provideUnitsAndLabels')]
    public function testGetLabelReturnsExpectedString(WeightUnit $unit, string $label): void
    {
        // When
        $result = $unit->getLabel();

        // Then
        $this->assertSame($label, $result);
    }

    #[DataProvider('provideUnitsAndLabels')]
    public function testFromLabelReturnsExpectedUnit(WeightUnit $unit, string $label): void
    {
        // When
        $result = WeightUnit::fromLabel($label);

        // Then
        $this->assertSame($unit, $result);
    }

    #[DataProvider('provideUnits')]
    public function testRoundTripFromUnitToLabelAndBackReturnsSameUnit(WeightUnit $unit): void
    {
        // When
        $label = $unit->getLabel();
        $roundTrip = WeightUnit::fromLabel($label);

        // Then
        $this->assertSame($unit, $roundTrip);
    }

    #[DataProvider('provideInvalidLabels')]
    public function testFromLabelThrowsForInvalidLabel(string $invalid): void
    {
        // Expect
        $this->expectException(InvalidArgumentException::class);

        // When
        WeightUnit::fromLabel($invalid);
    }

    public static function provideUnitsAndLabels(): array
    {
        return [
            [WeightUnit::KG, 'kg'],
        ];
    }

    public static function provideUnits(): array
    {
        return [
            [WeightUnit::KG],
        ];
    }

    public static function provideInvalidLabels(): array
    {
        return [
            [''],
            ['stone'],
            ['kg '],
            ['KG'],
            ['Kg'],
        ];
    }
}
