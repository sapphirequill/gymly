<?php

declare(strict_types=1);

namespace App\UserInterface\Rest\Api\State\Processor\WorkoutSession;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Application\Command\WorkoutSession\PerformWorkoutSetCommand;
use App\Application\Port\CommandBus;
use App\Domain\Shared\ValueObject\ExerciseCode;
use App\Domain\WorkoutSession\ValueObject\Weight;
use App\Domain\WorkoutSession\ValueObject\WeightUnit;
use App\UserInterface\Rest\Request\PerformWorkoutSetRequest;
use InvalidArgumentException;

/**
 * @implements ProcessorInterface<mixed, mixed>
 */
final readonly class PerformWorkoutSetProcessor implements ProcessorInterface
{
    public function __construct(private CommandBus $commandBus)
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if (!$data instanceof PerformWorkoutSetRequest) {
            throw new InvalidArgumentException('Expected PerformWorkoutSetRequest.');
        }

        $weight = null;
        if (null !== $data->weight) {
            $unit = null !== $data->unit ? WeightUnit::fromLabel($data->unit) : WeightUnit::KG;
            $weight = new Weight($data->weight, $unit);
        }

        $this->commandBus->dispatch(new PerformWorkoutSetCommand(
            ExerciseCode::fromCode($data->exerciseCode),
            $data->repetitions,
            $weight,
        ));

        return null;
    }
}
