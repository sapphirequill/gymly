<?php

declare(strict_types=1);

namespace App\Infrastructure\Bus\Messenger;

use App\Application\Port\CommandBus;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class MessengerCommandBus implements CommandBus
{
    public function __construct(private MessageBusInterface $commandBus)
    {
    }

    public function dispatch(object $command): void
    {
        try {
            $this->commandBus->dispatch($command);
        } catch (HandlerFailedException $handlerFailedException) {
            throw $handlerFailedException->getPrevious();
        }
    }
}
