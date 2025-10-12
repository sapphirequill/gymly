<?php

declare(strict_types=1);

namespace App\Application\Command\TrainingPlan;

use App\Domain\TrainingPlan\ValueObject\ExerciseRequirement;
use Ramsey\Uuid\UuidInterface;
use Webmozart\Assert\Assert;

final readonly class CreateTrainingPlanCommand
{
    /** @param ExerciseRequirement[] $exerciseRequirements */
    public function __construct(public UuidInterface $id, public string $name, public array $exerciseRequirements)
    {
        Assert::allIsInstanceOf($exerciseRequirements, ExerciseRequirement::class);
    }
}
