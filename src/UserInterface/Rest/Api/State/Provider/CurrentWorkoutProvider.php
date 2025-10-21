<?php

declare(strict_types=1);

namespace App\UserInterface\Rest\Api\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Application\Port\QueryBus;
use App\Application\Query\CurrentWorkout\GetCurrentWorkoutQuery;

/**
 * @implements ProviderInterface<object>
 */
final readonly class CurrentWorkoutProvider implements ProviderInterface
{
    public function __construct(private QueryBus $queryBus)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array|object|null
    {
        return $this->queryBus->query(new GetCurrentWorkoutQuery());
    }
}
