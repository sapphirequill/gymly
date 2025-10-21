<?php

declare(strict_types=1);

namespace App\Infrastructure\Serializer\Normalizer;

use App\Domain\Shared\ValueObject\ExerciseCode;
use App\Domain\TrainingPlan\ValueObject\ExerciseRequirement;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ExerciseRequirementNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function getSupportedTypes(?string $format): array
    {
        return [
            ExerciseRequirement::class => true,
            '*' => false,
        ];
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof ExerciseRequirement;
    }

    /**
     * @return array{exerciseCode:string,minSets:int}
     */
    public function normalize(mixed $data, ?string $format = null, array $context = []): array
    {
        if (!$data instanceof ExerciseRequirement) {
            throw new InvalidArgumentException('ExerciseRequirement normalizer expects ExerciseRequirement instance.');
        }

        return [
            'exerciseCode' => new ExerciseCodeNormalizer()->normalize($data->exerciseCode),
            'minSets' => $data->minSets,
        ];
    }

    public function supportsDenormalization($data, string $type, ?string $format = null, array $context = []): bool
    {
        return ExerciseRequirement::class === $type && \is_array($data);
    }

    public function denormalize($data, string $type, ?string $format = null, array $context = []): mixed
    {
        if (!\is_array($data)) {
            throw new InvalidArgumentException('ExerciseRequirement denormalizer expects array.');
        }

        if (!isset($data['exerciseCode'], $data['minSets'])) {
            throw new InvalidArgumentException('Missing keys for ExerciseRequirement denormalization.');
        }

        return new ExerciseRequirement(
            new ExerciseCodeNormalizer()->denormalize($data['exerciseCode'], ExerciseCode::class, $format, $context),
            (int) $data['minSets']
        );
    }
}
