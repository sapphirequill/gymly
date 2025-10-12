<?php

declare(strict_types=1);

namespace App\Application\Command\TrainingPlan;

use App\Application\Port\TrainingPlanRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class DeleteTrainingPlanHandler
{
    public function __construct(private TrainingPlanRepository $trainingPlanRepository)
    {
    }

    public function __invoke(DeleteTrainingPlanCommand $command): void
    {
        $trainingPlan = $this->trainingPlanRepository->getById($command->id);

        $trainingPlan->delete();

        $this->trainingPlanRepository->save($trainingPlan);
    }
}
