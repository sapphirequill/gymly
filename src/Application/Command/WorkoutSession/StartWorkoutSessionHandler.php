<?php

declare(strict_types=1);

namespace App\Application\Command\WorkoutSession;

use App\Application\Port\TrainingPlanRepository;
use App\Application\Port\WorkoutSessionRepository;
use App\Domain\TrainingPlan\Exception\TrainingPlanNotFoundException;
use App\Domain\TrainingPlan\TrainingPlan;
use App\Domain\TrainingPlan\ValueObject\ExerciseRequirement;
use App\Domain\WorkoutSession\ValueObject\CompletionRequirements;
use App\Domain\WorkoutSession\ValueObject\MinSetsCompletionRequirement;
use App\Domain\WorkoutSession\WorkoutSession;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class StartWorkoutSessionHandler
{
    public function __construct(
        private WorkoutSessionRepository $workoutSessionRepository,
        private TrainingPlanRepository $trainingPlanRepository,
    ) {
    }

    public function __invoke(StartWorkoutSessionCommand $command): void
    {
        if (!$command->trainingPlanId instanceof UuidInterface) {
            $workoutSession = WorkoutSession::startWithoutPlan(
                $command->id,
                $command->startedAt,
            );
        } else {
            $trainingPlan = $this->trainingPlanRepository->getById($command->trainingPlanId);

            if ($trainingPlan->isDeleted()) {
                throw TrainingPlanNotFoundException::create();
            }

            $workoutSession = WorkoutSession::startWithPlan(
                $command->id,
                $command->trainingPlanId,
                $this->createCompletionRequirements($trainingPlan),
                $command->startedAt,
            );
        }

        $this->workoutSessionRepository->save($workoutSession);
    }

    private function createCompletionRequirements(TrainingPlan $trainingPlan): CompletionRequirements
    {
        $requirements = \array_map(
            static fn (ExerciseRequirement $r): MinSetsCompletionRequirement => new MinSetsCompletionRequirement($r->exerciseCode, $r->minSets),
            $trainingPlan->getExerciseRequirements(),
        );

        return new CompletionRequirements($requirements);
    }
}
