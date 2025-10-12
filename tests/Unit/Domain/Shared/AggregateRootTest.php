<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Shared;

use App\Domain\Shared\AggregateRoot;
use App\Domain\Shared\DomainEvent;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use RuntimeException;

readonly class DummyEvent extends DomainEvent
{
}

readonly class NoHandlerEvent extends DomainEvent
{
}

final class TestAggregate extends AggregateRoot
{
    public int $applied = 0;

    public function __construct(UuidInterface $id)
    {
        parent::__construct($id);
    }

    public function fireHandled(): void
    {
        $this->recordEvent(new DummyEvent(new DateTimeImmutable()));
    }

    public function fireUnhandled(): void
    {
        $this->recordEvent(new NoHandlerEvent(new DateTimeImmutable()));
    }

    protected function apply(DomainEvent $event): void
    {
        match (true) {
            $event instanceof DummyEvent => $this->applied++,
            default => throw new RuntimeException(\sprintf('Missing apply* handler for event %s', $event::class)),
        };
    }
}

final class AggregateRootTest extends TestCase
{
    public function testItAppliesEventWhenHandlerExistsAndRecordsIt(): void
    {
        // Given
        $aggregate = new TestAggregate(Uuid::uuid4());

        // When
        $aggregate->fireHandled();

        // Then
        $this->assertCount(1, $aggregate->getRecordedEvents());
        $this->assertSame(1, $aggregate->applied);
    }

    public function testItThrowsRuntimeExceptionWhenApplyMethodIsMissing(): void
    {
        // Given
        $aggregate = new TestAggregate(Uuid::uuid4());

        // Expect
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing apply* handler for event App\\Tests\\Unit\\Domain\\Shared\\NoHandlerEvent');

        // When
        $aggregate->fireUnhandled();
    }
}
