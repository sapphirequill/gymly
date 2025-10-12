<?php

declare(strict_types=1);

namespace App\UserInterface\Rest\Api\State\Processor\WorkoutSession;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Application\Command\WorkoutSession\StartWorkoutSessionCommand;
use App\Application\Port\CommandBus;
use App\UserInterface\Rest\Request\StartWorkoutSessionRequest;
use App\UserInterface\Rest\Response\ResourceCreatedResponse;
use DateTimeImmutable;
use Ramsey\Uuid\Uuid;

final readonly class StartWorkoutSessionProcessor implements ProcessorInterface
{
    public function __construct(private CommandBus $commandBus)
    {
    }

    /**
     * @param StartWorkoutSessionRequest $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        $id = Uuid::uuid4();

        $this->commandBus->dispatch(new StartWorkoutSessionCommand(
            $id,
            null !== $data->trainingPlanId ? Uuid::fromString($data->trainingPlanId) : null,
            new DateTimeImmutable(),
        ));

        return new ResourceCreatedResponse($id->toString());
    }
}
