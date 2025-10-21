<?php

declare(strict_types=1);

namespace App\UserInterface\Rest\Api\State\Processor\TrainingPlan;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Application\Command\TrainingPlan\AddExerciseToTrainingPlanCommand;
use App\Application\Port\CommandBus;
use App\Domain\Shared\ValueObject\ExerciseCode;
use App\UserInterface\Rest\Request\AddExerciseToTrainingPlanRequest;
use InvalidArgumentException;
use Ramsey\Uuid\Uuid;

/**
 * @implements ProcessorInterface<mixed, mixed>
 */
final readonly class AddExerciseToTrainingPlanProcessor implements ProcessorInterface
{
    public function __construct(private CommandBus $commandBus)
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if (!$data instanceof AddExerciseToTrainingPlanRequest) {
            throw new InvalidArgumentException('Expected AddExerciseToTrainingPlanRequest.');
        }

        $trainingPlanId = Uuid::fromString((string) ($uriVariables['id'] ?? ''));

        $this->commandBus->dispatch(new AddExerciseToTrainingPlanCommand(
            $trainingPlanId,
            ExerciseCode::fromCode($data->exerciseCode),
            $data->minSets,
        ));

        return null;
    }
}
