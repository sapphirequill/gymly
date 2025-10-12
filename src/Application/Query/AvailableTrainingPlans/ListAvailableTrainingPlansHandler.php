<?php

declare(strict_types=1);

namespace App\Application\Query\AvailableTrainingPlans;

use App\Application\Port\AvailableTrainingPlanFinder;
use App\Application\ReadModel\AvailableTrainingPlanReadModel;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ListAvailableTrainingPlansHandler
{
    public function __construct(private AvailableTrainingPlanFinder $finder)
    {
    }

    /** @return AvailableTrainingPlanReadModel[] */
    public function __invoke(ListAvailableTrainingPlansQuery $query): array
    {
        return $this->finder->all();
    }
}
