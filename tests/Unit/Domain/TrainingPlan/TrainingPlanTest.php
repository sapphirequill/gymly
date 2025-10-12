<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\TrainingPlan;

use App\Domain\Shared\ValueObject\ExerciseCode;
use App\Domain\TrainingPlan\DomainEvent\ExerciseAddedToTrainingPlan;
use App\Domain\TrainingPlan\DomainEvent\ExerciseRemovedFromTrainingPlan;
use App\Domain\TrainingPlan\DomainEvent\TrainingPlanCreated;
use App\Domain\TrainingPlan\DomainEvent\TrainingPlanDeleted;
use App\Domain\TrainingPlan\Exception\DeletedTrainingPlanModificationException;
use App\Domain\TrainingPlan\Exception\DuplicateExerciseInPlanException;
use App\Domain\TrainingPlan\Exception\ExerciseNotInPlanException;
use App\Domain\TrainingPlan\Exception\TrainingPlanAlreadyDeletedException;
use App\Domain\TrainingPlan\TrainingPlan;
use App\Domain\TrainingPlan\ValueObject\ExerciseRequirement;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class TrainingPlanTest extends TestCase
{
    public function testItCreatesPlanWithInitialRequirementsWhenInputIsValid(): void
    {
        // Given
        $id = Uuid::uuid4();
        $name = 'FBW';
        $squat = ExerciseCode::fromCode('SQUAT');
        $bench = ExerciseCode::fromCode('BENCH_PRESS');
        $initial = [new ExerciseRequirement($squat, 2), new ExerciseRequirement($bench, 1)];

        // When
        $plan = TrainingPlan::create($id, $name, $initial);

        // Then
        $this->assertCount(1, $plan->getRecordedEvents());
        $this->assertInstanceOf(TrainingPlanCreated::class, $plan->getRecordedEvents()[0]);
        $this->assertCount(2, $plan->getExerciseRequirements());
        $this->assertTrue($plan->getExerciseRequirements()[0]->exerciseCode->equals($squat));
        $this->assertTrue($plan->getExerciseRequirements()[1]->exerciseCode->equals($bench));
    }

    public function testItThrowsExceptionWhenDuplicateExercisesProvidedOnCreate(): void
    {
        // Given
        $id = Uuid::uuid4();
        $name = 'FBW';
        $squat = ExerciseCode::fromCode('SQUAT');
        $initial = [new ExerciseRequirement($squat, 2), new ExerciseRequirement($squat, 3)];

        // Expect
        $this->expectException(DuplicateExerciseInPlanException::class);

        // When
        TrainingPlan::create($id, $name, $initial);
    }

    public function testItAddsExerciseWhenNotDeletedAndNotPresent(): void
    {
        // Given
        $id = Uuid::uuid4();
        $plan = TrainingPlan::create($id, 'FBW', [
            new ExerciseRequirement(ExerciseCode::fromCode('SQUAT'), 2),
        ]);
        $bench = ExerciseCode::fromCode('BENCH_PRESS');

        // When
        $plan->addExercise($bench, 1);

        // Then
        $events = $plan->getRecordedEvents();
        $this->assertInstanceOf(ExerciseAddedToTrainingPlan::class, \end($events));
        $this->assertCount(2, $plan->getExerciseRequirements());
        $this->assertTrue($plan->getExerciseRequirements()[1]->exerciseCode->equals($bench));
    }

    public function testItThrowsExceptionWhenAddingExerciseThatAlreadyExists(): void
    {
        // Given
        $id = Uuid::uuid4();
        $squat = ExerciseCode::fromCode('SQUAT');
        $plan = TrainingPlan::create($id, 'FBW', [new ExerciseRequirement($squat, 2)]);

        // Expect
        $this->expectException(DuplicateExerciseInPlanException::class);

        // When
        $plan->addExercise($squat, 3);
    }

    public function testItThrowsExceptionWhenAddingExerciseToDeletedPlan(): void
    {
        // Given
        $id = Uuid::uuid4();
        $plan = TrainingPlan::create($id, 'FBW', [new ExerciseRequirement(ExerciseCode::fromCode('SQUAT'), 2)]);
        $plan->delete();

        // Expect
        $this->expectException(DeletedTrainingPlanModificationException::class);

        // When
        $plan->addExercise(ExerciseCode::fromCode('BENCH_PRESS'), 1);
    }

    public function testItRemovesExerciseWhenExistsAndNotDeleted(): void
    {
        // Given
        $id = Uuid::uuid4();
        $squat = ExerciseCode::fromCode('SQUAT');
        $bench = ExerciseCode::fromCode('BENCH_PRESS');
        $plan = TrainingPlan::create($id, 'FBW', [
            new ExerciseRequirement($squat, 2),
            new ExerciseRequirement($bench, 1),
        ]);

        // When
        $plan->removeExercise($squat);

        // Then
        $events = $plan->getRecordedEvents();
        $this->assertInstanceOf(ExerciseRemovedFromTrainingPlan::class, \end($events));
        $codes = \array_map(static fn (ExerciseRequirement $req): string => $req->exerciseCode->code, $plan->getExerciseRequirements());
        $this->assertSame(['BENCH_PRESS'], $codes);
    }

    public function testItThrowsExceptionWhenRemovingExerciseThatDoesNotExist(): void
    {
        // Given
        $id = Uuid::uuid4();
        $plan = TrainingPlan::create($id, 'FBW', [new ExerciseRequirement(ExerciseCode::fromCode('SQUAT'), 2)]);

        // Expect
        $this->expectException(ExerciseNotInPlanException::class);

        // When
        $plan->removeExercise(ExerciseCode::fromCode('BENCH_PRESS'));
    }

    public function testItThrowsExceptionWhenRemovingExerciseFromDeletedPlan(): void
    {
        // Given
        $id = Uuid::uuid4();
        $plan = TrainingPlan::create($id, 'FBW', [new ExerciseRequirement(ExerciseCode::fromCode('SQUAT'), 2)]);
        $plan->delete();

        // Expect
        $this->expectException(DeletedTrainingPlanModificationException::class);

        // When
        $plan->removeExercise(ExerciseCode::fromCode('SQUAT'));
    }

    public function testItDeletesPlanWhenNotDeleted(): void
    {
        // Given
        $id = Uuid::uuid4();
        $plan = TrainingPlan::create($id, 'FBW', [new ExerciseRequirement(ExerciseCode::fromCode('SQUAT'), 2)]);

        // When
        $plan->delete();

        // Then
        $events = $plan->getRecordedEvents();
        $this->assertInstanceOf(TrainingPlanDeleted::class, \end($events));
    }

    public function testItThrowsExceptionWhenDeletingAlreadyDeletedPlan(): void
    {
        // Given
        $id = Uuid::uuid4();
        $plan = TrainingPlan::create($id, 'FBW', [new ExerciseRequirement(ExerciseCode::fromCode('SQUAT'), 2)]);
        $plan->delete();

        // Expect
        $this->expectException(TrainingPlanAlreadyDeletedException::class);

        // When
        $plan->delete();
    }
}
