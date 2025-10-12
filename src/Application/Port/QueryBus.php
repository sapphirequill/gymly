<?php

declare(strict_types=1);

namespace App\Application\Port;

interface QueryBus
{
    public function query(object $query): mixed;
}
