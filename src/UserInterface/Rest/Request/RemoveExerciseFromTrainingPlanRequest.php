<?php

declare(strict_types=1);

namespace App\UserInterface\Rest\Request;

use ApiPlatform\Metadata\ApiProperty;
use Symfony\Component\Validator\Constraints as Assert;

final class RemoveExerciseFromTrainingPlanRequest
{
    #[ApiProperty(example: 'BENCH_PRESS')]
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: '/^[A-Z_]+$/')]
    public string $exerciseCode;
}
