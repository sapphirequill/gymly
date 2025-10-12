<?php

declare(strict_types=1);

namespace App\UserInterface\Rest\Response;

final readonly class ResourceCreatedResponse
{
    public function __construct(public string $id)
    {
    }
}
