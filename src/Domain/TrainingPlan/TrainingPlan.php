<?php

declare(strict_types=1);

namespace App\Domain\TrainingPlan;

use App\Domain\Shared\AggregateRoot;
use App\Domain\Shared\DomainEvent;
use App\Domain\Shared\ValueObject\ExerciseCode;
use App\Domain\TrainingPlan\DomainEvent\ExerciseAddedToTrainingPlan;
use App\Domain\TrainingPlan\DomainEvent\ExerciseRemovedFromTrainingPlan;
use App\Domain\TrainingPlan\DomainEvent\TrainingPlanCreated;
use App\Domain\TrainingPlan\DomainEvent\TrainingPlanDeleted;
use App\Domain\TrainingPlan\Exception\DeletedTrainingPlanModificationException;
use App\Domain\TrainingPlan\Exception\DuplicateExerciseInPlanException;
use App\Domain\TrainingPlan\Exception\ExerciseNotInPlanException;
use App\Domain\TrainingPlan\Exception\TrainingPlanAlreadyDeletedException;
use App\Domain\TrainingPlan\ValueObject\ExerciseRequirement;
use DateTimeImmutable;
use Ramsey\Uuid\UuidInterface;
use RuntimeException;
use Webmozart\Assert\Assert;

final class TrainingPlan extends AggregateRoot
{
    private string $name;

    private bool $isDeleted = false;

    /** @var ExerciseRequirement[] */
    private array $exerciseRequirements = [];

    public function getName(): string
    {
        return $this->name;
    }

    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    /**
     * @param ExerciseRequirement[] $initialExerciseRequirements
     */
    public static function create(UuidInterface $id, string $name, array $initialExerciseRequirements): self
    {
        Assert::allIsInstanceOf($initialExerciseRequirements, ExerciseRequirement::class);
        Assert::notEmpty($name);

        $self = new self($id);

        $self->recordEvent(new TrainingPlanCreated($id, $name, $initialExerciseRequirements, new DateTimeImmutable()));

        return $self;
    }

    /** @return ExerciseRequirement[] */
    public function getExerciseRequirements(): array
    {
        return $this->exerciseRequirements;
    }

    public function addExercise(ExerciseCode $exerciseCode, int $minSets): void
    {
        if ($this->isDeleted) {
            throw DeletedTrainingPlanModificationException::createForAddAttempt();
        }

        if ($this->hasExercise($exerciseCode)) {
            throw DuplicateExerciseInPlanException::create($exerciseCode);
        }

        $this->recordEvent(new ExerciseAddedToTrainingPlan($this->id, $exerciseCode, $minSets, new DateTimeImmutable()));
    }

    public function removeExercise(ExerciseCode $exerciseCode): void
    {
        if ($this->isDeleted) {
            throw DeletedTrainingPlanModificationException::createForRemoveAttempt();
        }

        if (!$this->hasExercise($exerciseCode)) {
            throw ExerciseNotInPlanException::create($exerciseCode);
        }

        $this->recordEvent(new ExerciseRemovedFromTrainingPlan($this->id, $exerciseCode, new DateTimeImmutable()));
    }

    public function delete(): void
    {
        if ($this->isDeleted) {
            throw TrainingPlanAlreadyDeletedException::create();
        }

        $this->recordEvent(new TrainingPlanDeleted($this->id, new DateTimeImmutable()));
    }

    private function hasExercise(ExerciseCode $exerciseCode): bool
    {
        return array_any($this->exerciseRequirements, fn ($requirement): bool => $requirement->exerciseCode->equals($exerciseCode));
    }

    protected function apply(DomainEvent $event): void
    {
        match (true) {
            $event instanceof TrainingPlanCreated => $this->applyTrainingPlanCreated($event),
            $event instanceof ExerciseAddedToTrainingPlan => $this->applyExerciseAddedToTrainingPlan($event),
            $event instanceof ExerciseRemovedFromTrainingPlan => $this->applyExerciseRemovedFromTrainingPlan($event),
            $event instanceof TrainingPlanDeleted => $this->applyTrainingPlanDeleted($event),
            default => throw new RuntimeException(\sprintf('Missing apply* handler for event %s', $event::class)),
        };
    }

    private function applyTrainingPlanCreated(TrainingPlanCreated $event): void
    {
        $seen = [];

        foreach ($event->exerciseRequirements as $requirement) {
            $code = $requirement->exerciseCode->code;

            if (isset($seen[$code])) {
                throw DuplicateExerciseInPlanException::create($requirement->exerciseCode);
            }

            $seen[$code] = true;
            $this->exerciseRequirements[] = $requirement;
        }

        $this->name = $event->name;
    }

    private function applyExerciseAddedToTrainingPlan(ExerciseAddedToTrainingPlan $event): void
    {
        $this->exerciseRequirements[] = new ExerciseRequirement($event->exerciseCode, $event->minSets);
    }

    private function applyExerciseRemovedFromTrainingPlan(ExerciseRemovedFromTrainingPlan $event): void
    {
        foreach ($this->exerciseRequirements as $index => $requirement) {
            if ($requirement->exerciseCode->equals($event->exerciseCode)) {
                unset($this->exerciseRequirements[$index]);
                break;
            }
        }

        $this->exerciseRequirements = \array_values($this->exerciseRequirements);
    }

    private function applyTrainingPlanDeleted(TrainingPlanDeleted $event): void
    {
        $this->isDeleted = true;
    }
}
