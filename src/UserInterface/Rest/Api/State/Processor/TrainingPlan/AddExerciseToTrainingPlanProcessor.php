<?php

declare(strict_types=1);

namespace App\UserInterface\Rest\Api\State\Processor\TrainingPlan;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Application\Command\TrainingPlan\AddExerciseToTrainingPlanCommand;
use App\Application\Port\CommandBus;
use App\Domain\Shared\ValueObject\ExerciseCode;
use App\UserInterface\Rest\Request\AddExerciseToTrainingPlanRequest;
use Ramsey\Uuid\Uuid;

final readonly class AddExerciseToTrainingPlanProcessor implements ProcessorInterface
{
    public function __construct(private CommandBus $commandBus)
    {
    }

    /**
     * @param AddExerciseToTrainingPlanRequest $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        $trainingPlanId = Uuid::fromString((string) ($uriVariables['id'] ?? ''));

        $this->commandBus->dispatch(new AddExerciseToTrainingPlanCommand(
            $trainingPlanId,
            ExerciseCode::fromCode($data->exerciseCode),
            $data->minSets,
        ));

        return null;
    }
}
