<?php

declare(strict_types=1);

namespace App\Infrastructure\Bus\Messenger;

use App\Application\Port\QueryBus;
use RuntimeException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final readonly class MessengerQueryBus implements QueryBus
{
    public function __construct(private MessageBusInterface $queryBus)
    {
    }

    public function query(object $query): mixed
    {
        $envelope = $this->queryBus->dispatch($query);
        /** @var HandledStamp|null $handled */
        $handled = $envelope->last(HandledStamp::class);

        if (null === $handled) {
            throw new RuntimeException(sprintf('Query was not handled: %s', get_debug_type($query)));
        }

        return $handled->getResult();
    }
}
