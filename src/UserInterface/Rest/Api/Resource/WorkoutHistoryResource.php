<?php

declare(strict_types=1);

namespace App\UserInterface\Rest\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Application\ReadModel\WorkoutHistoryReadModel;
use App\UserInterface\Rest\Api\State\Provider\WorkoutHistoryProvider;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/workout-history',
            output: WorkoutHistoryReadModel::class,
            provider: WorkoutHistoryProvider::class,
        ),
    ],
    paginationEnabled: false,
)]
final class WorkoutHistoryResource
{
}
