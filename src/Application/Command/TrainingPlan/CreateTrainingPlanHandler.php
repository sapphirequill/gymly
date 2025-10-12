<?php

declare(strict_types=1);

namespace App\Application\Command\TrainingPlan;

use App\Application\Port\TrainingPlanRepository;
use App\Domain\TrainingPlan\TrainingPlan;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class CreateTrainingPlanHandler
{
    public function __construct(private TrainingPlanRepository $trainingPlanRepository)
    {
    }

    public function __invoke(CreateTrainingPlanCommand $command): void
    {
        $this->trainingPlanRepository->save(
            TrainingPlan::create(
                $command->id,
                $command->name,
                $command->exerciseRequirements,
            )
        );
    }
}
