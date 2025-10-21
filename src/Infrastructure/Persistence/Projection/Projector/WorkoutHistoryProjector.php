<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Projection\Projector;

use App\Domain\WorkoutSession\DomainEvent\WorkoutSessionCompleted;
use App\Infrastructure\Persistence\Projection\Doctrine\Entity\WorkoutHistory;
use App\Infrastructure\Persistence\Projection\Doctrine\Repository\CurrentWorkoutRepository;
use App\Infrastructure\Persistence\Projection\Doctrine\Repository\WorkoutHistoryRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Webmozart\Assert\Assert;

final readonly class WorkoutHistoryProjector implements EventSubscriberInterface
{
    public function __construct(
        private CurrentWorkoutRepository $currentWorkoutRepo,
        private WorkoutHistoryRepository $repository,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            WorkoutSessionCompleted::class => 'onCompleted',
        ];
    }

    public function onCompleted(WorkoutSessionCompleted $event): void
    {
        $current = $this->currentWorkoutRepo->find();

        Assert::notNull($current);

        $startedAt = $current->getStartedAt();
        $completedAt = $event->occurredAt;
        $durationSec = \max(1, $completedAt->getTimestamp() - $startedAt->getTimestamp());
        $durationMinutes = (int) \ceil($durationSec / 60);

        $sets = $current->getPerformedWorkoutSets();
        $totalSetsCount = \count($sets);

        $totalVolume = 0.0;
        $byExercise = [];

        foreach ($sets as $set) {
            $weight = $set['weight'] ?? null;
            $repetitions = $set['repetitions'];
            $code = $set['exerciseCode'];
            $setData = ['repetitions' => $repetitions];

            if (!isset($byExercise[$code])) {
                $byExercise[$code] = ['exerciseCode' => $code, 'sets' => []];
            }

            if (null !== $weight) {
                $totalVolume += $weight['value'] * $repetitions;
                $setData['weight'] = $weight;
            }

            $byExercise[$code]['sets'][] = $setData;
        }

        $history = new WorkoutHistory(
            (string) $event->workoutSessionId,
            $startedAt,
            $completedAt,
            $durationMinutes,
            $totalSetsCount,
            $totalVolume,
            \array_values($byExercise)
        );

        $this->repository->save($history);
    }
}
