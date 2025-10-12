<?php

declare(strict_types=1);

namespace App\UserInterface\Rest\Request;

use ApiPlatform\Metadata\ApiProperty;
use Symfony\Component\Validator\Constraints as Assert;

final class CreateTrainingPlanRequest
{
    #[ApiProperty(example: 'Chest & triceps')]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 100)]
    public string $name;

    /**
     * @var array<int, ExerciseRequirementInput>
     */
    #[Assert\Valid]
    #[Assert\Count(min: 1)]
    public array $exerciseRequirements = [];
}
