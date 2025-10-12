<?php

declare(strict_types=1);

namespace App\Application\Query\CurrentWorkout;

use App\Application\Port\CurrentWorkoutFinder;
use App\Application\ReadModel\CurrentWorkoutReadModel;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetCurrentWorkoutHandler
{
    public function __construct(private CurrentWorkoutFinder $finder)
    {
    }

    public function __invoke(GetCurrentWorkoutQuery $query): ?CurrentWorkoutReadModel
    {
        return $this->finder->get();
    }
}
