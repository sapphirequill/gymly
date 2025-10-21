<?php

declare(strict_types=1);

namespace App\UserInterface\Rest\Api\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Application\Port\QueryBus;
use App\Application\Query\WorkoutHistory\ListWorkoutHistoryQuery;

/**
 * @implements ProviderInterface<object>
 */
final readonly class WorkoutHistoryProvider implements ProviderInterface
{
    public function __construct(private QueryBus $queryBus)
    {
    }

    /**
     * @return array<int, object>|object|null
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array|object|null
    {
        return $this->queryBus->query(new ListWorkoutHistoryQuery());
    }
}
