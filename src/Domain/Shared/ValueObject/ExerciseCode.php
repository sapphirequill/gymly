<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObject;

use Stringable;
use Webmozart\Assert\Assert;

final readonly class ExerciseCode implements Stringable
{
    private function __construct(public string $code)
    {
        Assert::true(self::isValid($code), 'Invalid exercise code.');
    }

    public static function isValid(string $code): bool
    {
        return '' !== $code && '0' !== $code && \preg_match('/^[A-Z_]+$/', $code);
    }

    public static function fromCode(string $code): self
    {
        return new self($code);
    }

    public function equals(self $other): bool
    {
        return $this->code === $other->code;
    }

    public function __toString(): string
    {
        return $this->code;
    }
}
