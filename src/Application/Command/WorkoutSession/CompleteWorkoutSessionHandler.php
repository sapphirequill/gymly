<?php

declare(strict_types=1);

namespace App\Application\Command\WorkoutSession;

use App\Application\ReadModel\CurrentWorkoutReadModel;
use App\Application\Port\CurrentWorkoutFinder;
use App\Application\Port\WorkoutSessionRepository;
use App\Domain\WorkoutSession\Exception\WorkoutSessionNotFoundException;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class CompleteWorkoutSessionHandler
{
    public function __construct(private WorkoutSessionRepository $workoutSessionRepository, private CurrentWorkoutFinder $currentWorkoutFinder)
    {
    }

    public function __invoke(CompleteWorkoutSessionCommand $command): void
    {
        $currentWorkout = $this->currentWorkoutFinder->get();

        if (!$currentWorkout instanceof CurrentWorkoutReadModel) {
            throw WorkoutSessionNotFoundException::createForCurrent();
        }

        $session = $this->workoutSessionRepository->getById(Uuid::fromString($currentWorkout->id));

        $session->complete();

        $this->workoutSessionRepository->save($session);
    }
}
