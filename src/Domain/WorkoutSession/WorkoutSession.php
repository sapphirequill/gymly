<?php

declare(strict_types=1);

namespace App\Domain\WorkoutSession;

use App\Domain\Shared\AggregateRoot;
use App\Domain\Shared\DomainEvent;
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
use App\Domain\WorkoutSession\ValueObject\Weight;
use App\Domain\WorkoutSession\ValueObject\WorkoutSet;
use DateTimeImmutable;
use Ramsey\Uuid\UuidInterface;
use RuntimeException;

final class WorkoutSession extends AggregateRoot
{
    private WorkoutSessionStatus $status;

    private ?UuidInterface $trainingPlanId = null;

    private ?CompletionRequirements $completionRequirements = null;

    /** @var WorkoutSet[] */
    private array $performedWorkoutSets = [];

    private DateTimeImmutable $startedAt;

    private ?DateTimeImmutable $completedAt = null;

    private ?DateTimeImmutable $cancelledAt = null;

    public function getStatus(): WorkoutSessionStatus
    {
        return $this->status;
    }

    public function getTrainingPlanId(): ?UuidInterface
    {
        return $this->trainingPlanId;
    }

    public function getCompletionRequirements(): ?CompletionRequirements
    {
        return $this->completionRequirements;
    }

    /** @return WorkoutSet[] */
    public function getPerformedWorkoutSets(): array
    {
        return $this->performedWorkoutSets;
    }

    public function getStartedAt(): DateTimeImmutable
    {
        return $this->startedAt;
    }

    public function getCompletedAt(): ?DateTimeImmutable
    {
        return $this->completedAt;
    }

    public function getCancelledAt(): ?DateTimeImmutable
    {
        return $this->cancelledAt;
    }

    public static function startWithPlan(UuidInterface $id, UuidInterface $trainingPlanId, CompletionRequirements $completionRequirements, DateTimeImmutable $startedAt): self
    {
        $self = new self($id);

        $self->recordEvent(new WorkoutSessionStarted($id, $trainingPlanId, $completionRequirements, $startedAt));

        return $self;
    }

    public static function startWithoutPlan(UuidInterface $id, DateTimeImmutable $startedAt): self
    {
        $self = new self($id);

        $self->recordEvent(new WorkoutSessionStarted($id, null, null, $startedAt));

        return $self;
    }

    public function performWorkoutSet(ExerciseCode $exerciseCode, int $repetitions, ?Weight $weight): void
    {
        if (WorkoutSessionStatus::STARTED !== $this->status) {
            throw FinishedWorkoutSessionException::createForWorkoutSetAttempt();
        }

        if ($this->completionRequirements instanceof CompletionRequirements && !$this->completionRequirements->containsExercise($exerciseCode)) {
            throw CompletionRequirementsNotMetException::createForRedundantExerciseCode($exerciseCode);
        }

        $this->recordEvent(new WorkoutSetPerformed($this->id, $exerciseCode, $repetitions, $weight, new DateTimeImmutable()));
    }

    public function complete(): void
    {
        if (WorkoutSessionStatus::STARTED !== $this->status) {
            throw DisallowedTransitionException::createForCompleted();
        }

        if ([] === $this->performedWorkoutSets) {
            throw EmptyWorkoutSessionException::create();
        }

        $this->checkCompletionRequirements();

        $this->recordEvent(new WorkoutSessionCompleted($this->id, new DateTimeImmutable()));
    }

    private function checkCompletionRequirements(): void
    {
        if (!$this->completionRequirements instanceof CompletionRequirements) {
            return;
        }

        if (($missingExerciseCodes = $this->completionRequirements->getUnsatisfiedExerciseCodes($this->getUniquePerformedExerciseCodes())) !== []) {
            throw CompletionRequirementsNotMetException::createForMissingExerciseCodes($missingExerciseCodes);
        }

        foreach ($this->getSetsPerExerciseCode() as $exerciseCodeAndSets) {
            if (!$this->completionRequirements->isMinimumSetsSatisfied($exerciseCodeAndSets['code'], $exerciseCodeAndSets['sets'])) {
                throw CompletionRequirementsNotMetException::createForInsufficientSetsForExercise($exerciseCodeAndSets['code']);
            }
        }
    }

    /** @return ExerciseCode[] */
    private function getUniquePerformedExerciseCodes(): array
    {
        return \array_unique(
            \array_map(
                static fn (WorkoutSet $set): ExerciseCode => $set->exerciseCode,
                $this->performedWorkoutSets
            )
        );
    }

    /** @return array<array{code: ExerciseCode, sets: int}> */
    private function getSetsPerExerciseCode(): array
    {
        $setsPerExerciseCode = [];

        foreach ($this->performedWorkoutSets as $performedWorkoutSet) {
            if (isset($setsPerExerciseCode[$performedWorkoutSet->exerciseCode->code])) {
                ++$setsPerExerciseCode[$performedWorkoutSet->exerciseCode->code]['sets'];
            } else {
                $setsPerExerciseCode[$performedWorkoutSet->exerciseCode->code] = [
                    'code' => $performedWorkoutSet->exerciseCode,
                    'sets' => 1,
                ];
            }
        }

        return \array_values($setsPerExerciseCode);
    }

    public function cancel(): void
    {
        if (WorkoutSessionStatus::STARTED !== $this->status) {
            throw DisallowedTransitionException::createForCancelled();
        }

        $this->recordEvent(new WorkoutSessionCancelled($this->id, new DateTimeImmutable()));
    }

    protected function apply(DomainEvent $event): void
    {
        match (true) {
            $event instanceof WorkoutSessionStarted => $this->applyWorkoutSessionStarted($event),
            $event instanceof WorkoutSetPerformed => $this->applyWorkoutSetPerformed($event),
            $event instanceof WorkoutSessionCompleted => $this->applyWorkoutSessionCompleted($event),
            $event instanceof WorkoutSessionCancelled => $this->applyWorkoutSessionCancelled($event),
            default => throw new RuntimeException(\sprintf('Missing apply* handler for event %s', $event::class)),
        };
    }

    private function applyWorkoutSessionStarted(WorkoutSessionStarted $event): void
    {
        $this->status = WorkoutSessionStatus::STARTED;
        $this->trainingPlanId = $event->trainingPlanId;
        $this->completionRequirements = $event->completionRequirements;
        $this->startedAt = $event->occurredAt;
    }

    private function applyWorkoutSetPerformed(WorkoutSetPerformed $event): void
    {
        $this->performedWorkoutSets[] = new WorkoutSet($event->exerciseCode, $event->repetitions, $event->weight);
    }

    private function applyWorkoutSessionCompleted(WorkoutSessionCompleted $event): void
    {
        $this->status = WorkoutSessionStatus::COMPLETED;
        $this->completedAt = $event->occurredAt;
    }

    private function applyWorkoutSessionCancelled(WorkoutSessionCancelled $event): void
    {
        $this->status = WorkoutSessionStatus::CANCELLED;
        $this->cancelledAt = $event->occurredAt;
    }
}
