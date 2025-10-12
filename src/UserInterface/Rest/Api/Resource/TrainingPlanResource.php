<?php

declare(strict_types=1);

namespace App\UserInterface\Rest\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Application\ReadModel\AvailableTrainingPlanReadModel;
use App\UserInterface\Rest\Api\State\Processor\TrainingPlan\AddExerciseToTrainingPlanProcessor;
use App\UserInterface\Rest\Api\State\Processor\TrainingPlan\CreateTrainingPlanProcessor;
use App\UserInterface\Rest\Api\State\Processor\TrainingPlan\DeleteTrainingPlanProcessor;
use App\UserInterface\Rest\Api\State\Processor\TrainingPlan\RemoveExerciseFromTrainingPlanProcessor;
use App\UserInterface\Rest\Api\State\Provider\AvailableTrainingPlansProvider;
use App\UserInterface\Rest\Request\AddExerciseToTrainingPlanRequest;
use App\UserInterface\Rest\Request\CreateTrainingPlanRequest;
use App\UserInterface\Rest\Response\ResourceCreatedResponse;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/training-plans',
            output: AvailableTrainingPlanReadModel::class,
            provider: AvailableTrainingPlansProvider::class,
        ),
        new Post(
            uriTemplate: '/training-plans',
            input: CreateTrainingPlanRequest::class,
            output: ResourceCreatedResponse::class,
            read: false,
            processor: CreateTrainingPlanProcessor::class,
        ),
        new Post(
            uriTemplate: '/training-plans/{id}/exercises',
            input: AddExerciseToTrainingPlanRequest::class,
            output: false,
            read: false,
            processor: AddExerciseToTrainingPlanProcessor::class,
        ),
        new Delete(
            uriTemplate: '/training-plans/{id}/exercises/{exerciseCode}',
            output: false,
            read: false,
            processor: RemoveExerciseFromTrainingPlanProcessor::class,
        ),
        new Delete(
            uriTemplate: '/training-plans/{id}',
            output: false,
            read: false,
            processor: DeleteTrainingPlanProcessor::class,
        ),
    ],
    paginationEnabled: false,
)]
final class TrainingPlanResource
{
}
