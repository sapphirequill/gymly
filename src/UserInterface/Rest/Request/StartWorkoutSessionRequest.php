<?php

declare(strict_types=1);

namespace App\UserInterface\Rest\Request;

use Symfony\Component\Validator\Constraints as Assert;

final class StartWorkoutSessionRequest
{
    #[Assert\Uuid]
    public ?string $trainingPlanId = null;
}
