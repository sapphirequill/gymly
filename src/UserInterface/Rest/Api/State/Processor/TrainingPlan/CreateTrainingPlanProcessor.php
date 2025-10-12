<?php

declare(strict_types=1);

namespace App\UserInterface\Rest\Api\State\Processor\TrainingPlan;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Application\Command\TrainingPlan\CreateTrainingPlanCommand;
use App\Application\Port\CommandBus;
use App\Domain\Shared\ValueObject\ExerciseCode;
use App\Domain\TrainingPlan\ValueObject\ExerciseRequirement;
use App\UserInterface\Rest\Request\CreateTrainingPlanRequest;
use App\UserInterface\Rest\Response\ResourceCreatedResponse;
use Ramsey\Uuid\Uuid;

final readonly class CreateTrainingPlanProcessor implements ProcessorInterface
{
    public function __construct(private CommandBus $commandBus)
    {
    }

    /**
     * @param CreateTrainingPlanRequest $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        $id = Uuid::uuid4();

        $exerciseRequirements = [];
        foreach ($data->exerciseRequirements as $req) {
            $exerciseRequirements[] = new ExerciseRequirement(
                ExerciseCode::fromCode($req->exerciseCode),
                $req->minSets,
            );
        }

        $this->commandBus->dispatch(new CreateTrainingPlanCommand(
            $id,
            $data->name,
            $exerciseRequirements,
        ));

        return new ResourceCreatedResponse($id->toString());
    }
}
