<?php

declare(strict_types=1);

namespace App\UserInterface\Rest\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Application\ReadModel\CurrentWorkoutReadModel;
use App\UserInterface\Rest\Api\State\Processor\WorkoutSession\CancelWorkoutSessionProcessor;
use App\UserInterface\Rest\Api\State\Processor\WorkoutSession\CompleteWorkoutSessionProcessor;
use App\UserInterface\Rest\Api\State\Processor\WorkoutSession\PerformWorkoutSetProcessor;
use App\UserInterface\Rest\Api\State\Processor\WorkoutSession\StartWorkoutSessionProcessor;
use App\UserInterface\Rest\Api\State\Provider\CurrentWorkoutProvider;
use App\UserInterface\Rest\Request\PerformWorkoutSetRequest;
use App\UserInterface\Rest\Request\StartWorkoutSessionRequest;
use App\UserInterface\Rest\Response\ResourceCreatedResponse;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/workout-sessions/current',
            output: CurrentWorkoutReadModel::class,
            provider: CurrentWorkoutProvider::class,
        ),
        new Post(
            uriTemplate: '/workout-sessions',
            input: StartWorkoutSessionRequest::class,
            output: ResourceCreatedResponse::class,
            read: false,
            processor: StartWorkoutSessionProcessor::class,
        ),
        new Post(
            uriTemplate: '/workout-sessions/current/sets',
            input: PerformWorkoutSetRequest::class,
            output: false,
            read: false,
            processor: PerformWorkoutSetProcessor::class,
        ),
        new Post(
            uriTemplate: '/workout-sessions/current/complete',
            output: false,
            read: false,
            processor: CompleteWorkoutSessionProcessor::class,
        ),
        new Post(
            uriTemplate: '/workout-sessions/current/cancel',
            output: false,
            read: false,
            processor: CancelWorkoutSessionProcessor::class,
        ),
    ],
    paginationEnabled: false,
)]
final class WorkoutSessionResource
{
}
