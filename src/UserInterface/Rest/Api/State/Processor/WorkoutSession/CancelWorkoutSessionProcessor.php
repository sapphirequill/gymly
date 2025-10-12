<?php

declare(strict_types=1);

namespace App\UserInterface\Rest\Api\State\Processor\WorkoutSession;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Application\Command\WorkoutSession\CancelWorkoutSessionCommand;
use App\Application\Port\CommandBus;

final readonly class CancelWorkoutSessionProcessor implements ProcessorInterface
{
    public function __construct(private CommandBus $commandBus)
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        $this->commandBus->dispatch(new CancelWorkoutSessionCommand());

        return null;
    }
}
