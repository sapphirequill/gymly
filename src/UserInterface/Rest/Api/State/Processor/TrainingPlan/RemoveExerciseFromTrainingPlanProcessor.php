<?php

declare(strict_types=1);

namespace App\UserInterface\Rest\Api\State\Processor\TrainingPlan;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Application\Command\TrainingPlan\RemoveExerciseFromTrainingPlanCommand;
use App\Application\Port\CommandBus;
use App\Domain\Shared\ValueObject\ExerciseCode;
use Ramsey\Uuid\Uuid;

final readonly class RemoveExerciseFromTrainingPlanProcessor implements ProcessorInterface
{
    public function __construct(private CommandBus $commandBus)
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        $trainingPlanId = Uuid::fromString((string) ($uriVariables['id'] ?? ''));
        $exerciseCode = ExerciseCode::fromCode((string) ($uriVariables['exerciseCode'] ?? ''));

        $this->commandBus->dispatch(new RemoveExerciseFromTrainingPlanCommand(
            $trainingPlanId,
            $exerciseCode,
        ));

        return null;
    }
}
