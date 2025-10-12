<?php

declare(strict_types=1);

namespace App\Application\Port;

interface CommandBus
{
    public function dispatch(object $command): void;
}
