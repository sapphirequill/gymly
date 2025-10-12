<?php

declare(strict_types=1);

namespace App\UserInterface\Rest\Api\State\Processor\WorkoutSession;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Application\Command\WorkoutSession\CompleteWorkoutSessionCommand;
use App\Application\Port\CommandBus;

final readonly class CompleteWorkoutSessionProcessor implements ProcessorInterface
{
    public function __construct(private CommandBus $commandBus)
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        $this->commandBus->dispatch(new CompleteWorkoutSessionCommand());

        return null;
    }
}
