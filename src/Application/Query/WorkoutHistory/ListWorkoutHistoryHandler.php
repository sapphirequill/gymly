<?php

declare(strict_types=1);

namespace App\Application\Query\WorkoutHistory;

use App\Application\Port\WorkoutHistoryFinder;
use App\Application\ReadModel\WorkoutHistoryReadModel;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ListWorkoutHistoryHandler
{
    public function __construct(private WorkoutHistoryFinder $finder)
    {
    }

    /** @return WorkoutHistoryReadModel[] */
    public function __invoke(ListWorkoutHistoryQuery $query): array
    {
        return $this->finder->all();
    }
}
