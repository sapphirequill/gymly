<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\WorkoutSession;

use App\Domain\Shared\ValueObject\ExerciseCode;
use App\Domain\WorkoutSession\DomainEvent\WorkoutSessionCancelled;
use App\Domain\WorkoutSession\DomainEvent\WorkoutSessionCompleted;
use App\Domain\WorkoutSession\DomainEvent\WorkoutSessionStarted;
use App\Domain\WorkoutSession\DomainEvent\WorkoutSetPerformed;
use App\Domain\WorkoutSession\Exception\CompletionRequirementsNotMetException;
use App\Domain\WorkoutSession\Exception\DisallowedTransitionException;
use App\Domain\WorkoutSession\Exception\EmptyWorkoutSessionException;
use App\Domain\WorkoutSession\Exception\FinishedWorkoutSessionException;
use App\Domain\WorkoutSession\ValueObject\CompletionRequirements;
use App\Domain\WorkoutSession\ValueObject\MinSetsCompletionRequirement;
use App\Domain\WorkoutSession\ValueObject\Weight;
use App\Domain\WorkoutSession\ValueObject\WeightUnit;
use App\Domain\WorkoutSession\WorkoutSession;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class WorkoutSessionTest extends TestCase
{
    public function testItStartsWithoutPlanWhenRequested(): void
    {
        // Given
        $id = Uuid::uuid4();
        $startedAt = new DateTimeImmutable('-1 minute');

        // When
        $session = WorkoutSession::startWithoutPlan($id, $startedAt);

        // Then
        $events = $session->getRecordedEvents();
        $this->assertInstanceOf(WorkoutSessionStarted::class, $events[0]);
        $this->assertNull($events[0]->trainingPlanId);
        $this->assertNull($events[0]->completionRequirements);
    }

    public function testItStartsWithPlanWhenRequirementsProvided(): void
    {
        // Given
        $id = Uuid::uuid4();
        $planId = Uuid::uuid4();
        $squat = ExerciseCode::fromCode('SQUAT');
        $requirements = new CompletionRequirements([
            new MinSetsCompletionRequirement($squat, 1),
        ]);
        $startedAt = new DateTimeImmutable('-1 minute');

        // When
        $session = WorkoutSession::startWithPlan($id, $planId, $requirements, $startedAt);

        // Then
        $events = $session->getRecordedEvents();
        $this->assertInstanceOf(WorkoutSessionStarted::class, $events[0]);
        $this->assertTrue($planId->equals($events[0]->trainingPlanId));
        $this->assertSame($requirements, $events[0]->completionRequirements);
    }

    public function testItPerformsWorkoutSetWhenStarted(): void
    {
        // Given
        $session = WorkoutSession::startWithoutPlan(Uuid::uuid4(), new DateTimeImmutable('-1 minute'));
        $code = ExerciseCode::fromCode('SQUAT');

        // When
        $session->performWorkoutSet($code, 5, new Weight(100.0, WeightUnit::KG));

        // Then
        $events = $session->getRecordedEvents();
        $this->assertInstanceOf(WorkoutSetPerformed::class, \end($events));
    }

    public function testItThrowsExceptionWhenPerformingWorkoutSetAfterCompletion(): void
    {
        // Given
        $session = WorkoutSession::startWithoutPlan(Uuid::uuid4(), new DateTimeImmutable('-1 minute'));
        $code = ExerciseCode::fromCode('SQUAT');
        $session->performWorkoutSet($code, 5, new Weight(100.0, WeightUnit::KG));
        $session->complete();

        // Expect
        $this->expectException(FinishedWorkoutSessionException::class);

        // When
        $session->performWorkoutSet($code, 3, new Weight(60.0, WeightUnit::KG));
    }

    public function testItThrowsExceptionWhenPerformingWorkoutSetWithRedundantExerciseCode(): void
    {
        // Given
        $id = Uuid::uuid4();
        $planId = Uuid::uuid4();
        $squat = ExerciseCode::fromCode('SQUAT');
        $requirements = new CompletionRequirements([
            new MinSetsCompletionRequirement($squat, 1),
        ]);
        $session = WorkoutSession::startWithPlan($id, $planId, $requirements, new DateTimeImmutable('-1 minute'));
        $bench = ExerciseCode::fromCode('BENCH_PRESS');

        // Expect
        $this->expectException(CompletionRequirementsNotMetException::class);

        // When
        $session->performWorkoutSet($bench, 10, new Weight(80.0, WeightUnit::KG));
    }

    public function testItThrowsExceptionWhenCompletingEmptySession(): void
    {
        // Given
        $session = WorkoutSession::startWithoutPlan(Uuid::uuid4(), new DateTimeImmutable('-1 minute'));

        // Expect
        $this->expectException(EmptyWorkoutSessionException::class);

        // When
        $session->complete();
    }

    public function testItThrowsExceptionWhenCompletingWhenMissingRequiredExercises(): void
    {
        // Given
        $id = Uuid::uuid4();
        $planId = Uuid::uuid4();
        $squat = ExerciseCode::fromCode('SQUAT');
        $bench = ExerciseCode::fromCode('BENCH_PRESS');
        $requirements = new CompletionRequirements([
            new MinSetsCompletionRequirement($squat, 1),
            new MinSetsCompletionRequirement($bench, 1),
        ]);
        $session = WorkoutSession::startWithPlan($id, $planId, $requirements, new DateTimeImmutable('-1 minute'));
        $session->performWorkoutSet($squat, 3, new Weight(100.0, WeightUnit::KG));

        // Expect
        $this->expectException(CompletionRequirementsNotMetException::class);

        // When
        $session->complete();
    }

    public function testItThrowsExceptionWhenCompletingWhenInsufficientSetsForExercise(): void
    {
        // Given
        $id = Uuid::uuid4();
        $planId = Uuid::uuid4();
        $squat = ExerciseCode::fromCode('SQUAT');
        $requirements = new CompletionRequirements([
            new MinSetsCompletionRequirement($squat, 2),
        ]);
        $session = WorkoutSession::startWithPlan($id, $planId, $requirements, new DateTimeImmutable('-1 minute'));
        $session->performWorkoutSet($squat, 1, new Weight(100.0, WeightUnit::KG));

        // Expect
        $this->expectException(CompletionRequirementsNotMetException::class);

        // When
        $session->complete();
    }

    public function testItCompletesWhenRequirementsAreMet(): void
    {
        // Given
        $id = Uuid::uuid4();
        $planId = Uuid::uuid4();
        $squat = ExerciseCode::fromCode('SQUAT');
        $bench = ExerciseCode::fromCode('BENCH_PRESS');
        $requirements = new CompletionRequirements([
            new MinSetsCompletionRequirement($squat, 2),
            new MinSetsCompletionRequirement($bench, 1),
        ]);
        $session = WorkoutSession::startWithPlan($id, $planId, $requirements, new DateTimeImmutable('-1 minute'));

        // When
        $session->performWorkoutSet($squat, 5, new Weight(100.0, WeightUnit::KG));
        $session->performWorkoutSet($squat, 5, new Weight(110.0, WeightUnit::KG));
        $session->performWorkoutSet($bench, 10, new Weight(60.0, WeightUnit::KG));
        $session->complete();

        // Then
        $events = $session->getRecordedEvents();
        $this->assertInstanceOf(WorkoutSessionCompleted::class, \end($events));
    }

    public function testItCancelsWhenStarted(): void
    {
        // Given
        $session = WorkoutSession::startWithoutPlan(Uuid::uuid4(), new DateTimeImmutable('-1 minute'));

        // When
        $session->cancel();

        // Then
        $events = $session->getRecordedEvents();
        $this->assertInstanceOf(WorkoutSessionCancelled::class, \end($events));
    }

    public function testItThrowsExceptionWhenCancellingAfterCompletion(): void
    {
        // Given
        $session = WorkoutSession::startWithoutPlan(Uuid::uuid4(), new DateTimeImmutable('-1 minute'));
        $session->performWorkoutSet(ExerciseCode::fromCode('SQUAT'), 5, new Weight(100.0, WeightUnit::KG));
        $session->complete();

        // Expect
        $this->expectException(DisallowedTransitionException::class);

        // When
        $session->cancel();
    }
}
