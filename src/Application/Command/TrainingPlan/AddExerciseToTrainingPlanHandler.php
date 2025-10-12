<?php

declare(strict_types=1);

namespace App\Application\Command\TrainingPlan;

use App\Application\Port\TrainingPlanRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class AddExerciseToTrainingPlanHandler
{
    public function __construct(private TrainingPlanRepository $trainingPlanRepository)
    {
    }

    public function __invoke(AddExerciseToTrainingPlanCommand $command): void
    {
        $trainingPlan = $this->trainingPlanRepository->getById($command->trainingPlanId);

        $trainingPlan->addExercise($command->exerciseCode, $command->minSets);

        $this->trainingPlanRepository->save($trainingPlan);
    }
}
