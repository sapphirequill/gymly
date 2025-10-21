<?php

declare(strict_types=1);

namespace App\UserInterface\Rest\Api\State\Processor\TrainingPlan;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Application\Command\TrainingPlan\DeleteTrainingPlanCommand;
use App\Application\Port\CommandBus;
use Ramsey\Uuid\Uuid;

/**
 * @implements ProcessorInterface<mixed, mixed>
 */
final readonly class DeleteTrainingPlanProcessor implements ProcessorInterface
{
    public function __construct(private CommandBus $commandBus)
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        $trainingPlanId = Uuid::fromString((string) ($uriVariables['id'] ?? ''));

        $this->commandBus->dispatch(new DeleteTrainingPlanCommand($trainingPlanId));

        return null;
    }
}
